import React, { useEffect, useRef, useState } from 'react';
import { ActivityIndicator, Alert, StyleSheet, Text, View } from 'react-native';
import MapView, { Circle, Marker } from 'react-native-maps';
import { getApiErrorMessage } from '../../core/api/client';
import { loadMapPins } from '../../core/api/store';
import { COMPANY } from '../../core/data/company';
import {
  LAP_VO_MARKET,
  SERVICE_RADIUS_KM,
  distanceFromLapVoMarketKm,
} from '../../core/utils/geo';
import { colors } from '../../core/theme';
import StateNotice from '../../shared/widgets/StateNotice';

const INITIAL_REGION = {
  latitude: LAP_VO_MARKET.latitude,
  longitude: LAP_VO_MARKET.longitude,
  latitudeDelta: 0.12,
  longitudeDelta: 0.12,
};

export default function MapScreen({ navigation, route }) {
  const selectedType = route?.params?.storeType || '';
  const screenTitle = route?.params?.title || 'Chợ Xã Lấp Vò';
  const [stores, setStores] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const mapRef = useRef(null);

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    loadMapPins()
      .then((items) => {
        if (mounted) {
          setStores(selectedType ? items.filter((item) => item.storeType === selectedType) : items);
        }
      })
      .catch((requestError) => {
        if (mounted) {
          setError(getApiErrorMessage(requestError, requestError?.message || 'Không tải được bản đồ cửa hàng.'));
        }
      })
      .finally(() => {
        if (mounted) {
          setLoading(false);
        }
      });

    return () => {
      mounted = false;
    };
  }, [selectedType]);

  const handleRegionChangeComplete = (region) => {
    const distance = distanceFromLapVoMarketKm({
      latitude: region.latitude,
      longitude: region.longitude,
    });
    if (distance > SERVICE_RADIUS_KM + 3) {
      Alert.alert(
        'Ngoài khu vực phục vụ',
        `${COMPANY.brand} đang phục vụ trong ${COMPANY.serviceArea}.`,
        [{ text: 'Quay lại', onPress: () => mapRef.current?.animateToRegion(INITIAL_REGION, 700) }],
      );
    }
  };

  const openStore = (store) => {
    Alert.alert(
      store.storeName,
      `${store.storeType}\n${store.address || 'Chưa có địa chỉ'}\nSĐT: ${store.phone || 'Chưa cập nhật'}`,
      [
        { text: 'Đóng', style: 'cancel' },
        { text: 'Xem hàng', onPress: () => navigation.navigate('Store') },
      ],
    );
  };

  return (
    <View style={styles.container}>
      <MapView
        ref={mapRef}
        style={styles.map}
        initialRegion={INITIAL_REGION}
        onRegionChangeComplete={handleRegionChangeComplete}
      >
        <Circle
          center={LAP_VO_MARKET}
          radius={SERVICE_RADIUS_KM * 1000}
          strokeColor="rgba(220,38,38,0.55)"
          fillColor="rgba(220,38,38,0.08)"
        />
        <Marker
          coordinate={LAP_VO_MARKET}
          title="Chợ Lấp Vò"
          description="Tâm phạm vi phục vụ 15 km"
          pinColor="red"
        />
        {stores.map((store) => (
          <Marker
            key={store.id}
            coordinate={{ latitude: store.latitude, longitude: store.longitude }}
            title={store.storeName}
            description={store.storeType}
            onCalloutPress={() => openStore(store)}
            pinColor={store.storeType.includes('Quán') ? 'orange' : 'blue'}
          />
        ))}
      </MapView>

      <View style={styles.overlay}>
        <Text style={styles.overlayTitle}>{screenTitle}</Text>
        <Text style={styles.overlayText}>
          {stores.length ? `${stores.length} cửa hàng đang hoạt động` : 'Trống'}
        </Text>
      </View>

      {loading ? (
        <View style={styles.loadingBox}>
          <ActivityIndicator size="large" color={colors.brand} />
          <Text style={styles.loadingText}>Đang tải cửa hàng...</Text>
        </View>
      ) : null}

      {error ? (
        <View style={styles.errorBox}>
          <StateNotice type="error">{error}</StateNotice>
        </View>
      ) : null}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  map: {
    width: '100%',
    height: '100%',
  },
  overlay: {
    position: 'absolute',
    left: 14,
    right: 14,
    top: 14,
    borderRadius: 8,
    backgroundColor: 'rgba(17,24,39,0.92)',
    paddingHorizontal: 14,
    paddingVertical: 10,
  },
  overlayTitle: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '900',
  },
  overlayText: {
    color: '#d1d5db',
    fontSize: 13,
    marginTop: 2,
  },
  loadingBox: {
    position: 'absolute',
    alignSelf: 'center',
    top: '45%',
    alignItems: 'center',
    gap: 8,
    borderRadius: 8,
    backgroundColor: 'rgba(255,255,255,0.94)',
    padding: 16,
  },
  loadingText: {
    color: colors.text,
    fontWeight: '700',
  },
  errorBox: {
    position: 'absolute',
    left: 14,
    right: 14,
    bottom: 22,
  },
});
