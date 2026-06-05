import React, { useState } from 'react';
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import * as Location from 'expo-location';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { getApiErrorMessage } from '../../core/api/client';
import { loginStore, registerStore } from '../../core/api/store';
import { COMPANY } from '../../core/data/company';
import { distanceFromLapVoMarketKm, isWithinServiceArea } from '../../core/utils/geo';
import { normalizePhone, saveStoreSession } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';
import StateNotice from '../../shared/widgets/StateNotice';

const STORE_TYPES = [
  'Quán ăn',
  'Cafe',
  'Shop quần áo',
  'Cửa hàng điện máy',
  'Tạp hóa',
  'Dịch vụ',
];

export default function StoreLoginScreen({ navigation }) {
  const [cameraPermission, requestCameraPermission] = useCameraPermissions();
  const [ownerName, setOwnerName] = useState('');
  const [phone, setPhone] = useState('');
  const [taxCode, setTaxCode] = useState('');
  const [storeName, setStoreName] = useState('');
  const [storeType, setStoreType] = useState(STORE_TYPES[0]);
  const [address, setAddress] = useState('');
  const [email, setEmail] = useState('');
  const [coordinates, setCoordinates] = useState(null);
  const [loginKey, setLoginKey] = useState('');
  const [showScanner, setShowScanner] = useState(false);
  const [loading, setLoading] = useState(false);
  const [locating, setLocating] = useState(false);
  const [error, setError] = useState('');

  const finishStoreLogin = async (store) => {
    await saveStoreSession(store);
    Alert.alert(
      'Đăng nhập cửa hàng thành công',
      `${store.storeName} đã mở phiên mua bán.`,
      [{ text: 'Vào khu mua bán', onPress: () => navigation.navigate('Store') }],
    );
  };

  const loginByKey = async (value = loginKey) => {
    const key = String(value || '').trim();
    if (!key) {
      setError('Vui lòng quét QR hoặc nhập key do giám đốc cấp.');
      return;
    }
    setLoading(true);
    setError('');
    try {
      const store = await loginStore({ qr_data: key, login_key: key });
      await finishStoreLogin(store);
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không đăng nhập được cửa hàng.'));
    } finally {
      setLoading(false);
      setShowScanner(false);
    }
  };

  const openScanner = async () => {
    setError('');
    if (!cameraPermission?.granted) {
      const permission = await requestCameraPermission();
      if (!permission.granted) {
        setError('Ứng dụng chưa được cấp quyền camera để quét QR.');
        return;
      }
    }
    setShowScanner(true);
  };

  const handleQrScanned = ({ data }) => {
    if (loading) {
      return;
    }
    setShowScanner(false);
    setLoginKey(String(data || ''));
    loginByKey(data);
  };

  const pickCurrentLocation = async () => {
    setLocating(true);
    setError('');
    try {
      const permission = await Location.requestForegroundPermissionsAsync();
      if (permission.status !== 'granted') {
        setError('Ứng dụng chưa được cấp quyền lấy vị trí cửa hàng.');
        return;
      }

      const current = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.Balanced,
      });
      const nextCoordinates = {
        latitude: current.coords.latitude,
        longitude: current.coords.longitude,
      };
      if (!isWithinServiceArea(nextCoordinates)) {
        const distance = distanceFromLapVoMarketKm(nextCoordinates);
        setError(`Vị trí cách Chợ Lấp Vò khoảng ${distance.toFixed(1)} km, vượt phạm vi 15 km.`);
        return;
      }
      setCoordinates(nextCoordinates);
    } catch (locationError) {
      setError(locationError?.message || 'Không lấy được vị trí hiện tại.');
    } finally {
      setLocating(false);
    }
  };

  const submitRegistration = async () => {
    const normalizedPhone = normalizePhone(phone);
    if (!storeName.trim()) {
      setError('Vui lòng nhập tên cửa hàng.');
      return;
    }
    if (normalizedPhone.length < 8) {
      setError('Vui lòng nhập số điện thoại liên hệ hợp lệ.');
      return;
    }
    if (taxCode.trim().replace(/\D/g, '').length < 8) {
      setError('Vui lòng nhập mã số thuế để gửi đơn cho giám đốc duyệt.');
      return;
    }
    if (!address.trim()) {
      setError('Vui lòng nhập địa chỉ cửa hàng.');
      return;
    }

    setLoading(true);
    setError('');
    try {
      const result = await registerStore({
        owner_name: ownerName.trim(),
        phone: normalizedPhone,
        tax_code: taxCode.trim(),
        store_name: storeName.trim(),
        store_type: storeType,
        address: address.trim(),
        email: email.trim(),
        lat: coordinates?.latitude,
        lng: coordinates?.longitude,
      });
      Alert.alert(
        'Đã gửi đơn đăng ký',
        result.message || `${result.store.storeName} đang chờ giám đốc duyệt và cấp QR đăng nhập.`,
      );
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không gửi được đăng ký cửa hàng.'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <Text style={styles.approval}>{COMPANY.approvalStatus}</Text>
          <Text style={styles.title}>Cửa hàng đối tác</Text>
          <Text style={styles.subtitle}>
            Đăng ký lần đầu bằng số điện thoại và mã số thuế. Sau khi văn phòng duyệt,
            cửa hàng chỉ đăng nhập bằng QR/key do giám đốc cấp.
          </Text>
        </View>

        <View style={styles.panel}>
          <Text style={styles.panelTitle}>Đăng nhập bằng QR</Text>
          <Text style={styles.helpText}>Dùng QR/key đã được cấp trong Văn phòng giám đốc.</Text>
          {showScanner ? (
            <View style={styles.scannerBox}>
              <CameraView
                barcodeScannerSettings={{ barcodeTypes: ['qr'] }}
                onBarcodeScanned={handleQrScanned}
                style={styles.camera}
              />
              <Pressable style={styles.closeScanner} onPress={() => setShowScanner(false)}>
                <Text style={styles.closeScannerText}>Đóng camera</Text>
              </Pressable>
            </View>
          ) : null}
          <View style={styles.loginActions}>
            <PrimaryButton label="Quét QR đăng nhập" onPress={openScanner} variant="dark" />
            <TextInput
              autoCapitalize="characters"
              onChangeText={setLoginKey}
              placeholder="Hoặc dán key đăng nhập"
              style={commonStyles.input}
              value={loginKey}
            />
            <PrimaryButton label="Đăng nhập bằng key" loading={loading} onPress={() => loginByKey()} />
          </View>
        </View>

        <View style={styles.panel}>
          <Text style={styles.panelTitle}>Đăng ký cửa hàng lần đầu</Text>
          <Text style={styles.helpText}>
            Thông tin này gửi về giám đốc. Khi duyệt xong, văn phòng sẽ cấp QR/key riêng.
          </Text>

          <Text style={styles.label}>Tên cửa hàng</Text>
          <TextInput
            onChangeText={setStoreName}
            placeholder="Ví dụ: Cafe Lấp Vò, Shop áo Nam..."
            style={commonStyles.input}
            value={storeName}
          />

          <Text style={styles.label}>Chủ cửa hàng</Text>
          <TextInput
            onChangeText={setOwnerName}
            placeholder="Tên người liên hệ"
            style={commonStyles.input}
            value={ownerName}
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

          <Text style={styles.label}>Mã số thuế</Text>
          <TextInput
            autoCapitalize="characters"
            keyboardType="number-pad"
            onChangeText={setTaxCode}
            placeholder="Nhập MST để gửi giám đốc duyệt"
            style={commonStyles.input}
            value={taxCode}
          />

          <Text style={styles.label}>Loại cửa hàng</Text>
          <View style={styles.typeGrid}>
            {STORE_TYPES.map((type) => (
              <PrimaryButton
                key={type}
                label={type}
                variant={storeType === type ? 'brand' : 'outline'}
                onPress={() => setStoreType(type)}
              />
            ))}
          </View>

          <Text style={styles.label}>Địa chỉ</Text>
          <TextInput
            multiline
            onChangeText={setAddress}
            placeholder="Nhập địa chỉ cửa hàng"
            style={[commonStyles.input, styles.multiline]}
            textAlignVertical="top"
            value={address}
          />

          <Text style={styles.label}>Email nếu có</Text>
          <TextInput
            autoCapitalize="none"
            keyboardType="email-address"
            onChangeText={setEmail}
            placeholder="email@cuahang.vn"
            style={commonStyles.input}
            value={email}
          />

          <PrimaryButton
            label={coordinates ? 'Đã lấy vị trí cửa hàng' : 'Dùng vị trí hiện tại'}
            loading={locating}
            onPress={pickCurrentLocation}
            variant={coordinates ? 'outline' : 'dark'}
          />
          {coordinates ? (
            <StateNotice>
              Tọa độ: {coordinates.latitude.toFixed(6)}, {coordinates.longitude.toFixed(6)}
            </StateNotice>
          ) : (
            <StateNotice>
              Nếu chưa lấy vị trí, hệ thống tạm đặt cửa hàng quanh Chợ Lấp Vò để văn phòng thấy trước.
            </StateNotice>
          )}

          {error ? <StateNotice type="error">{error}</StateNotice> : null}
          <PrimaryButton label="Gửi đơn đăng ký cho giám đốc" loading={loading} onPress={submitRegistration} />
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
  header: {
    ...commonStyles.section,
    gap: 8,
  },
  approval: {
    color: colors.warning,
    fontSize: 13,
    fontWeight: '800',
  },
  title: {
    color: colors.text,
    fontSize: 24,
    fontWeight: '900',
  },
  subtitle: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 20,
  },
  panel: {
    ...commonStyles.section,
    gap: 10,
  },
  panelTitle: {
    color: colors.text,
    fontSize: 20,
    fontWeight: '900',
  },
  helpText: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 20,
  },
  loginActions: {
    gap: 9,
  },
  scannerBox: {
    overflow: 'hidden',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.border,
    backgroundColor: colors.dark,
  },
  camera: {
    height: 260,
  },
  closeScanner: {
    alignItems: 'center',
    padding: 12,
    backgroundColor: colors.dark,
  },
  closeScannerText: {
    color: '#ffffff',
    fontWeight: '800',
  },
  label: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '800',
    marginTop: 4,
  },
  multiline: {
    minHeight: 72,
  },
  typeGrid: {
    gap: 8,
  },
});
