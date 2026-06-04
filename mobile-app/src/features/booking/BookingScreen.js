import React, { useRef, useState } from 'react';
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import MapView, { Circle, Marker } from 'react-native-maps';
import * as Location from 'expo-location';
import { createBooking } from '../../core/api/services';
import { getApiErrorMessage } from '../../core/api/client';
import { formatMoney } from '../../core/data/services';
import { getDeviceFingerprint, isValidPhone, normalizePhone, saveActiveBooking } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import {
  distanceFromLapVoMarketKm,
  isWithinServiceArea,
  LAP_VO_MARKET,
  SERVICE_RADIUS_KM,
} from '../../core/utils/geo';
import PrimaryButton from '../../shared/widgets/PrimaryButton';
import StateNotice from '../../shared/widgets/StateNotice';

function addressFromGeocode(place) {
  if (!place) {
    return '';
  }

  return [
    place.name,
    place.street,
    place.district,
    place.subregion,
    place.region,
    place.country,
  ]
    .filter(Boolean)
    .filter((value, index, values) => values.indexOf(value) === index)
    .join(', ');
}

export default function BookingScreen({ route, navigation, session }) {
  const service = route.params?.service;
  const mapRef = useRef(null);
  const [customerName, setCustomerName] = useState(session.name || '');
  const [phone, setPhone] = useState(session.phone || '');
  const [address, setAddress] = useState('');
  const [issue, setIssue] = useState(service?.name || '');
  const [quantity, setQuantity] = useState('1');
  const [coordinates, setCoordinates] = useState(null);
  const [distanceKm, setDistanceKm] = useState(null);
  const [locationConfirmed, setLocationConfirmed] = useState(false);
  const [locationMessage, setLocationMessage] = useState('Chạm bản đồ hoặc dùng vị trí hiện tại.');
  const [loadingLocation, setLoadingLocation] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  const updateCoordinates = async (nextCoordinates, resolveAddress = false) => {
    const nextDistance = distanceFromLapVoMarketKm(nextCoordinates);
    setCoordinates(nextCoordinates);
    setDistanceKm(nextDistance);
    setLocationConfirmed(false);
    setLocationMessage(
      nextDistance <= SERVICE_RADIUS_KM
        ? `Đã chọn vị trí cách Chợ Lấp Vò ${nextDistance.toFixed(1)} km. Hãy xác nhận tọa độ.`
        : `Vị trí cách Chợ Lấp Vò ${nextDistance.toFixed(1)} km, ngoài phạm vi phục vụ ${SERVICE_RADIUS_KM} km.`,
    );

    if (!resolveAddress) {
      return;
    }

    try {
      const places = await Location.reverseGeocodeAsync(nextCoordinates);
      const resolvedAddress = addressFromGeocode(places[0]);
      if (resolvedAddress) {
        setAddress(resolvedAddress);
      }
    } catch {
      // Tọa độ vẫn hợp lệ nếu dịch vụ đổi tọa độ sang địa chỉ không phản hồi.
    }
  };

  const useCurrentLocation = async () => {
    setLoadingLocation(true);
    setError('');
    try {
      const permission = await Location.requestForegroundPermissionsAsync();
      if (permission.status !== 'granted') {
        setError('Ứng dụng cần quyền vị trí để dùng vị trí hiện tại.');
        return;
      }

      const current = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.Balanced,
      });
      const nextCoordinates = {
        latitude: current.coords.latitude,
        longitude: current.coords.longitude,
      };
      mapRef.current?.animateToRegion(
        {
          ...nextCoordinates,
          latitudeDelta: 0.015,
          longitudeDelta: 0.015,
        },
        500,
      );
      await updateCoordinates(nextCoordinates, true);
    } catch {
      setError('Không lấy được vị trí hiện tại. Hãy chọn trực tiếp trên bản đồ.');
    } finally {
      setLoadingLocation(false);
    }
  };

  const confirmCoordinates = () => {
    if (!coordinates) {
      setError('Vui lòng chọn vị trí trên bản đồ trước.');
      return;
    }
    if (!isWithinServiceArea(coordinates)) {
      setError(`Điện Tử Hiếu hiện phục vụ trong bán kính ${SERVICE_RADIUS_KM} km từ Chợ Lấp Vò.`);
      return;
    }
    setError('');
    setLocationConfirmed(true);
    setLocationMessage(
      `Đã xác nhận tọa độ trong phạm vi phục vụ, cách Chợ Lấp Vò ${distanceKm.toFixed(1)} km.`,
    );
  };

  const handleBooking = async () => {
    const normalizedPhone = normalizePhone(phone);
    const normalizedQuantity = Math.max(1, Number.parseInt(quantity, 10) || 1);

    if (!service) {
      Alert.alert('Thiếu dịch vụ', 'Vui lòng quay lại và chọn dịch vụ.');
      return;
    }
    if (!customerName.trim()) {
      setError('Vui lòng nhập tên khách.');
      return;
    }
    if (!isValidPhone(normalizedPhone)) {
      setError('Số điện thoại phải có từ 8 đến 15 chữ số.');
      return;
    }
    if (!address.trim() || !issue.trim()) {
      setError('Vui lòng nhập địa chỉ và mô tả sự cố.');
      return;
    }
    if (!coordinates || !locationConfirmed) {
      setError('Vui lòng chọn và xác nhận tọa độ trước khi gửi.');
      return;
    }
    if (!isWithinServiceArea(coordinates)) {
      setError(`Vị trí nằm ngoài bán kính phục vụ ${SERVICE_RADIUS_KM} km từ Chợ Lấp Vò.`);
      return;
    }

    setSubmitting(true);
    setError('');
    try {
      const deviceFingerprint = await getDeviceFingerprint();
      const result = await createBooking({
        customer_name: customerName.trim(),
        phone: normalizedPhone,
        service_type: service.group,
        selected_service_name: service.name,
        issue_description: `${service.name}: ${issue.trim()}`,
        description: `${service.name}: ${issue.trim()}`,
        address: address.trim(),
        map_location: `${coordinates.latitude.toFixed(6)},${coordinates.longitude.toFixed(6)}`,
        map_lat: coordinates.latitude,
        map_lng: coordinates.longitude,
        quantity: normalizedQuantity,
        tech_target_base: service.techBase,
        estimated_price: service.publicPrice * normalizedQuantity,
        device_fingerprint: deviceFingerprint,
      });

      const booking = {
        id: result.bookingId,
        createdAt: new Date().toISOString(),
        service,
        address: address.trim(),
        coordinates,
        quantity: normalizedQuantity,
        result,
      };
      await saveActiveBooking(booking);
      navigation.replace('Tracking', { booking });
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không gửi được yêu cầu.'));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <View style={styles.serviceSummary}>
          <Text style={styles.group}>{service?.group || 'Dịch vụ kỹ thuật'}</Text>
          <Text style={styles.serviceName}>{service?.name || 'Chưa chọn dịch vụ'}</Text>
          <Text style={styles.price}>{formatMoney(service?.publicPrice)}</Text>
          <Text style={styles.note}>{service?.note || 'Giá tham khảo đã gồm VAT.'}</Text>
          {service ? (
            <PrimaryButton
              label="Hỏi trợ lý về dịch vụ này"
              onPress={() => navigation.navigate('Assistant', { service })}
              variant="outline"
            />
          ) : null}
        </View>

        <View style={styles.form}>
          <Text style={styles.label}>Tên khách</Text>
          <TextInput
            onChangeText={setCustomerName}
            placeholder="Nhập tên khách"
            style={commonStyles.input}
            value={customerName}
          />

          <Text style={styles.label}>Số điện thoại</Text>
          <TextInput
            keyboardType="phone-pad"
            maxLength={15}
            onChangeText={(value) => setPhone(normalizePhone(value))}
            placeholder="09xxxxxxxx"
            style={commonStyles.input}
            value={phone}
          />

          <Text style={styles.label}>Số lượng</Text>
          <TextInput
            keyboardType="number-pad"
            maxLength={3}
            onChangeText={setQuantity}
            style={commonStyles.input}
            value={quantity}
          />

          <Text style={styles.label}>Địa chỉ</Text>
          <TextInput
            multiline
            onChangeText={setAddress}
            placeholder="Địa chỉ nơi cần gọi thợ"
            style={[commonStyles.input, styles.multiline]}
            textAlignVertical="top"
            value={address}
          />

          <Text style={styles.label}>Chọn vị trí trên bản đồ</Text>
          <View style={styles.mapFrame}>
            <MapView
              initialRegion={{
                ...LAP_VO_MARKET,
                latitudeDelta: 0.32,
                longitudeDelta: 0.32,
              }}
              onPress={(event) => updateCoordinates(event.nativeEvent.coordinate, true)}
              ref={mapRef}
              style={styles.map}
            >
              <Circle
                center={LAP_VO_MARKET}
                fillColor="rgba(4, 120, 87, 0.08)"
                radius={SERVICE_RADIUS_KM * 1000}
                strokeColor={colors.success}
                strokeWidth={2}
              />
              <Marker
                coordinate={LAP_VO_MARKET}
                pinColor={colors.success}
                title="Chợ Lấp Vò"
                description="Tâm phạm vi phục vụ 15 km"
              />
              {coordinates ? (
                <Marker
                  coordinate={coordinates}
                  draggable
                  onDragEnd={(event) => updateCoordinates(event.nativeEvent.coordinate, true)}
                  title="Vị trí đã chọn"
                />
              ) : null}
            </MapView>
          </View>
          <Text style={[styles.locationText, locationConfirmed && styles.locationConfirmed]}>
            {locationMessage}
          </Text>
          <View style={styles.locationActions}>
            <PrimaryButton
              label="Vị trí hiện tại"
              loading={loadingLocation}
              onPress={useCurrentLocation}
              style={styles.actionButton}
              variant="dark"
            />
            <PrimaryButton
              disabled={!coordinates || !isWithinServiceArea(coordinates)}
              label="Xác nhận tọa độ"
              onPress={confirmCoordinates}
              style={styles.actionButton}
              variant="outline"
            />
          </View>

          <Text style={styles.label}>Mô tả sự cố</Text>
          <TextInput
            multiline
            onChangeText={setIssue}
            placeholder="Mô tả thiết bị và tình trạng cần xử lý"
            style={[commonStyles.input, styles.issueInput]}
            textAlignVertical="top"
            value={issue}
          />

          {error ? <StateNotice type="error">{error}</StateNotice> : null}
          <PrimaryButton label="Gửi yêu cầu gọi thợ" loading={submitting} onPress={handleBooking} />
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  content: {
    padding: 14,
    gap: 12,
  },
  serviceSummary: {
    ...commonStyles.section,
    gap: 5,
  },
  group: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
  },
  serviceName: {
    color: colors.text,
    fontSize: 20,
    fontWeight: '800',
  },
  price: {
    color: colors.brand,
    fontSize: 18,
    fontWeight: '800',
  },
  note: {
    color: colors.muted,
    fontSize: 13,
    lineHeight: 18,
  },
  form: {
    ...commonStyles.section,
    gap: 10,
  },
  label: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '700',
    marginTop: 4,
  },
  multiline: {
    minHeight: 72,
  },
  issueInput: {
    minHeight: 110,
  },
  mapFrame: {
    height: 260,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
  },
  map: {
    width: '100%',
    height: '100%',
  },
  locationText: {
    color: colors.muted,
    fontSize: 13,
    lineHeight: 18,
  },
  locationConfirmed: {
    color: colors.success,
    fontWeight: '700',
  },
  locationActions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionButton: {
    flex: 1,
  },
});
