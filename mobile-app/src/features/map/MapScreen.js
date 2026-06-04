import React, { useEffect, useState, useRef } from 'react';
import { View, StyleSheet, Text, ActivityIndicator, Alert, TouchableOpacity } from 'react-native';
import MapView, { Marker } from 'react-native-maps';
import api from '../../core/api/client';

// Tâm: Chợ Lấp Vò, Đồng Tháp
const LAP_VO_CENTER = {
  latitude: 10.3547,
  longitude: 105.5298,
  latitudeDelta: 0.1,
  longitudeDelta: 0.1,
};

// Hàm tính khoảng cách đơn giản (độ)
const getDistance = (lat1, lon1, lat2, lon2) => {
  return Math.sqrt(Math.pow(lat1 - lat2, 2) + Math.pow(lon1 - lon2, 2));
};

export default function MapScreen({ navigation }) {
  const [stores, setStores] = useState([]);
  const [loading, setLoading] = useState(true);
  const mapRef = useRef(null);

  useEffect(() => {
    api.get('?action=app_get_map_pins')
      .then(res => {
        if (res.data.status === 'success') {
          setStores(res.data.data);
        }
      })
      .catch(err => console.log(err))
      .finally(() => setLoading(false));
  }, []);

  const handleRegionChangeComplete = (region) => {
    // Nếu kéo bản đồ ra xa quá 15km (~0.13 độ) thì nảy về Chợ Lấp Vò
    const dist = getDistance(region.latitude, region.longitude, LAP_VO_CENTER.latitude, LAP_VO_CENTER.longitude);
    if (dist > 0.15) {
      Alert.alert("Ngoài khu vực hỗ trợ", "Chúng tôi hiện chỉ phục vụ quanh khu vực Xã Lấp Vò.");
      mapRef.current?.animateToRegion(LAP_VO_CENTER, 1000);
    }
  };

  const onMarkerPress = (store) => {
    Alert.alert(
      store.store_name,
      `Loại: ${store.store_type}\nĐịa chỉ: ${store.address}`,
      [
        { text: 'Đóng', style: 'cancel' },
        { text: 'Vào cửa hàng', onPress: () => alert('Đang phát triển: Menu mua hàng') }
      ]
    );
  };

  return (
    <View style={styles.container}>
      {loading && (
        <View style={styles.loadingBox}>
          <ActivityIndicator size="large" color="#dc2626" />
          <Text>Đang tải bản đồ Chợ Lấp Vò...</Text>
        </View>
      )}
      
      <MapView
        ref={mapRef}
        style={styles.map}
        initialRegion={LAP_VO_CENTER}
        onRegionChangeComplete={handleRegionChangeComplete}
      >
        {stores.map(store => (
          <Marker
            key={store.id}
            coordinate={{ latitude: parseFloat(store.lat), longitude: parseFloat(store.lng) }}
            title={store.store_name}
            description={store.store_type}
            onCalloutPress={() => onMarkerPress(store)}
            pinColor={store.store_type === 'Cửa hàng' ? 'blue' : 'red'}
          />
        ))}
      </MapView>
      
      <View style={styles.overlay}>
        <Text style={styles.overlayText}>Khu vực giới hạn: Bán kính 15km</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  map: { width: '100%', height: '100%' },
  loadingBox: { position: 'absolute', top: '40%', alignSelf: 'center', zIndex: 10, backgroundColor: 'rgba(255,255,255,0.9)', padding: 20, borderRadius: 10, alignItems: 'center' },
  overlay: { position: 'absolute', bottom: 30, alignSelf: 'center', backgroundColor: '#111827', paddingHorizontal: 20, paddingVertical: 10, borderRadius: 20 },
  overlayText: { color: '#fff', fontWeight: 'bold' }
});
