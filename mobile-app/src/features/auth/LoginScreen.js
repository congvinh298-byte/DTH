import React, { useState } from 'react';
import {
  Alert,
  Image,
  KeyboardAvoidingView,
  Linking,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
  LayoutAnimation,
  UIManager,
} from 'react-native';

if (Platform.OS === 'android' && UIManager.setLayoutAnimationEnabledExperimental) {
  UIManager.setLayoutAnimationEnabledExperimental(true);
}

import { CameraView, useCameraPermissions } from 'expo-camera';
import { getApiErrorMessage } from '../../core/api/client';
import { loginCustomer, registerCustomer } from '../../core/api/customer';
import { loginStore, registerStore } from '../../core/api/store';
import { COMPANY, LEGAL_LINKS } from '../../core/data/company';
import { colors, commonStyles } from '../../core/theme';
import { isValidPhone, normalizePhone, saveSession, saveStoreSession } from '../../core/storage/session';
import PrimaryButton from '../../shared/widgets/PrimaryButton';

export default function LoginScreen({ navigation, onAuthenticated }) {
  const [cameraPermission, requestCameraPermission] = useCameraPermissions();
  const [loginKey, setLoginKey] = useState('');
  const [showScanner, setShowScanner] = useState(false);
  const [phone, setPhone] = useState('');
  const [name, setName] = useState('');
  const [issuedCustomer, setIssuedCustomer] = useState(null);
  const [loadingLogin, setLoadingLogin] = useState(false);
  const [loadingRegister, setLoadingRegister] = useState(false);
  const [error, setError] = useState('');
  const [registerType, setRegisterType] = useState('none'); // 'none', 'customer', 'store'
  const [storeTaxCode, setStoreTaxCode] = useState('');
  const [storeNameInput, setStoreNameInput] = useState('');

  const finishCustomerLogin = async (customer) => {
    const nextSession = {
      phone: customer.phone,
      name: customer.name,
      customerId: customer.id,
      loginKey: customer.loginKey,
    };
    await saveSession(nextSession);
    onAuthenticated(nextSession);
  };

  const loginByQr = async (value = loginKey) => {
    const key = String(value || '').trim();
    if (!key) {
      setError('Vui lòng quét QR hoặc dán key để đăng nhập.');
      return;
    }

    setLoadingLogin(true);
    setError('');
    try {
      if (key.startsWith('DTH-STORE:') || key.startsWith('STORE:')) {
        const store = await loginStore({ qr_data: key, login_key: key });
        await saveStoreSession(store);
        setLoadingLogin(false);
        setShowScanner(false);
        Alert.alert('Thành công', 'Đã đăng nhập cửa hàng', [
          { text: 'Vào Quản lý', onPress: () => navigation.navigate('StoreProducts') }
        ]);
        return;
      }

      const customer = await loginCustomer({ qr_data: key, login_key: key });
      await finishCustomerLogin(customer);
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không đăng nhập được.'));
    } finally {
      setLoadingLogin(false);
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
    if (loadingLogin) {
      return;
    }
    setLoginKey(String(data || ''));
    loginByQr(data);
  };

  const handleRegister = async () => {
    const normalizedPhone = normalizePhone(phone);
    if (!isValidPhone(normalizedPhone)) {
      setError('Vui lòng nhập số điện thoại khách hàng từ 8 đến 15 chữ số.');
      return;
    }

    setLoadingRegister(true);
    setError('');
    setIssuedCustomer(null);
    try {
      const result = await registerCustomer({
        name: name.trim(),
        phone: normalizedPhone,
      });
      setIssuedCustomer(result.customer);
      setLoginKey(result.customer.qrPayload || result.customer.loginKey);
      Alert.alert('Đã cấp QR khách hàng', result.message || 'Hãy giữ QR này để đăng nhập về sau.');
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không đăng ký được khách hàng.'));
    } finally {
      setLoadingRegister(false);
    }
  };

  const handleRegisterStore = async () => {
    const normalizedPhone = normalizePhone(phone);
    if (!isValidPhone(normalizedPhone)) {
      setError('Vui lòng nhập số điện thoại hợp lệ từ 8 đến 15 chữ số.');
      return;
    }
    if (!storeNameInput.trim() || !storeTaxCode.trim()) {
      setError('Vui lòng nhập đầy đủ tên cửa hàng và mã số thuế.');
      return;
    }

    setLoadingRegister(true);
    setError('');
    setIssuedCustomer(null);
    try {
      const result = await registerStore({
        store_name: storeNameInput.trim(),
        owner_name: name.trim() || storeNameInput.trim(),
        phone: normalizedPhone,
        tax_code: storeTaxCode.trim(),
      });
      setIssuedCustomer(result.store);
      setLoginKey(result.store.qrPayload || result.store.loginKey);
      Alert.alert(
        'Đã ghi nhận thông tin',
        'Cửa hàng đã được tạo. Vui lòng chờ Giám đốc duyệt (Trạng thái: Pending). Sau khi được duyệt, bạn có thể đăng nhập bằng QR đã cấp.'
      );
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không đăng ký được cửa hàng.'));
    } finally {
      setLoadingRegister(false);
    }
  };

  const issuedPayload = issuedCustomer?.qrPayload || issuedCustomer?.loginKey || '';

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <Text style={styles.approval}>{COMPANY.approvalStatus}</Text>

        <View style={styles.brandBlock}>
          <Image source={require('../../../assets/logo.jpg')} style={styles.logo} />
          <Text style={styles.brand}>{COMPANY.brand}</Text>
          <Text style={styles.slogan}>{COMPANY.slogan}</Text>
          <Text style={styles.mission}>{COMPANY.mission}</Text>
          <Text style={styles.scope}>{COMPANY.serviceArea}</Text>
        </View>

        {error ? <Text style={styles.errorText}>{error}</Text> : null}

        <View style={styles.loginPanel}>
          <Text style={styles.title}>Quét QR Đăng nhập</Text>
          <Text style={styles.description}>
            Hệ thống sẽ tự động nhận diện QR Khách hàng hoặc QR Cửa hàng để điều hướng phù hợp.
          </Text>

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

          <PrimaryButton label="Quét QR đăng nhập" onPress={openScanner} variant="dark" />
          <TextInput
            autoCapitalize="characters"
            onChangeText={setLoginKey}
            placeholder="Hoặc dán key đăng nhập..."
            style={commonStyles.input}
            value={loginKey}
          />
          <PrimaryButton label="Đăng nhập bằng key" loading={loadingLogin} onPress={() => loginByQr()} />
        </View>

        <View style={styles.registerPanel}>
          <Text style={styles.title}>Bạn chưa có tài khoản?</Text>
          <View style={styles.registerOptions}>
            <Pressable 
              style={[styles.registerTab, registerType === 'customer' && styles.registerTabActive]} 
              onPress={() => {
                LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
                setRegisterType(registerType === 'customer' ? 'none' : 'customer');
              }}>
              <Text style={[styles.registerTabText, registerType === 'customer' && styles.registerTabTextActive]}>Khách hàng mới</Text>
            </Pressable>
            
            <Pressable 
              style={[styles.registerTab, registerType === 'store' && styles.registerTabActive]} 
              onPress={() => {
                LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
                setRegisterType(registerType === 'store' ? 'none' : 'store');
              }}>
              <Text style={[styles.registerTabText, registerType === 'store' && styles.registerTabTextActive]}>Đăng ký Cửa hàng</Text>
            </Pressable>
          </View>

          {registerType === 'customer' && (
            <View style={{gap: 12, marginTop: 10}}>
              <Text style={styles.description}>
                Nhập tên và số điện thoại để hệ thống cấp một QR duy nhất. Một số điện thoại chỉ có một tài khoản.
              </Text>
              <TextInput
                onChangeText={setName}
                placeholder="Tên khách hàng"
                style={commonStyles.input}
                value={name}
              />
              <TextInput
                autoComplete="tel"
                keyboardType="phone-pad"
                maxLength={15}
                onChangeText={(value) => setPhone(normalizePhone(value))}
                placeholder="Số điện thoại"
                returnKeyType="done"
                style={commonStyles.input}
                value={phone}
              />
              <PrimaryButton label="Cấp QR khách hàng" loading={loadingRegister} onPress={handleRegister} />
            </View>
          )}

          {registerType === 'store' && (
            <View style={{gap: 12, marginTop: 10}}>
              <Text style={styles.description}>
                Chủ cửa hàng đăng ký quầy riêng. Sau khi giám đốc duyệt, cửa hàng chỉ đăng nhập bằng QR/key đã cấp.
              </Text>
              <TextInput
                onChangeText={setStoreNameInput}
                placeholder="Tên Cửa hàng *"
                style={commonStyles.input}
                value={storeNameInput}
              />
              <TextInput
                onChangeText={setStoreTaxCode}
                placeholder="Mã số thuế / Số CMND *"
                style={commonStyles.input}
                value={storeTaxCode}
              />
              <TextInput
                onChangeText={setName}
                placeholder="Tên chủ cửa hàng"
                style={commonStyles.input}
                value={name}
              />
              <TextInput
                autoComplete="tel"
                keyboardType="phone-pad"
                maxLength={15}
                onChangeText={(value) => setPhone(normalizePhone(value))}
                placeholder="Số điện thoại liên hệ *"
                returnKeyType="done"
                style={commonStyles.input}
                value={phone}
              />
              <PrimaryButton label="Đăng ký Cửa hàng" loading={loadingRegister} onPress={handleRegisterStore} />
            </View>
          )}

          {issuedCustomer ? (
            <View style={styles.issuedBox}>
              <Text style={styles.issuedTitle}>QR đăng nhập của {issuedCustomer.storeName || issuedCustomer.name}</Text>
              {issuedCustomer.qrImageUrl ? (
                <Image source={{ uri: issuedCustomer.qrImageUrl }} style={styles.customerQr} />
              ) : null}
              <Text style={styles.keyText}>{issuedPayload}</Text>
              <PrimaryButton
                label="Đăng nhập bằng QR vừa cấp"
                onPress={() => loginByQr(issuedPayload)}
                variant="dark"
              />
            </View>
          ) : null}
        </View>

        <View style={styles.companyPanel}>
          <Text style={styles.companyTitle}>{COMPANY.legalName}</Text>
          <Text style={styles.companyText}>MST: {COMPANY.taxCode}</Text>
          <Text style={styles.companyText}>Địa chỉ: {COMPANY.address}</Text>
          <Text style={styles.companyText}>Hotline: {COMPANY.phone}</Text>
          <Pressable onPress={() => Linking.openURL(COMPANY.websiteUrl)}>
            <Text style={styles.link}>Website: {COMPANY.website}</Text>
          </Pressable>
          <View style={styles.legalLinks}>
            {LEGAL_LINKS.map((item) => (
              <Pressable key={item.label} onPress={() => Linking.openURL(item.url)}>
                <Text style={styles.link}>{item.label}</Text>
              </Pressable>
            ))}
          </View>
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
    paddingBottom: 28,
  },
  approval: {
    backgroundColor: colors.warningSoft,
    color: colors.warning,
    paddingHorizontal: 16,
    paddingVertical: 9,
    fontSize: 13,
    fontWeight: '800',
    textAlign: 'center',
  },
  brandBlock: {
    alignItems: 'center',
    backgroundColor: colors.surface,
    paddingHorizontal: 22,
    paddingVertical: 22,
    gap: 6,
  },
  logo: {
    width: 154,
    height: 88,
    resizeMode: 'contain',
  },
  brand: {
    color: colors.brand,
    fontSize: 27,
    fontWeight: '900',
  },
  slogan: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '700',
  },
  mission: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 21,
    marginTop: 5,
    textAlign: 'center',
  },
  scope: {
    color: colors.success,
    fontSize: 14,
    fontWeight: '800',
    marginTop: 4,
    textAlign: 'center',
  },
  loginPanel: {
    ...commonStyles.section,
    gap: 12,
    margin: 14,
    marginBottom: 0,
  },
  registerPanel: {
    ...commonStyles.section,
    gap: 12,
    margin: 14,
    marginBottom: 0,
  },
  storePanel: {
    ...commonStyles.section,
    gap: 12,
    margin: 14,
    marginBottom: 0,
  },
  title: {
    color: colors.text,
    fontSize: 21,
    fontWeight: '800',
  },
  description: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 20,
  },
  errorText: {
    color: colors.danger,
    fontSize: 14,
    fontWeight: '700',
    lineHeight: 20,
    marginHorizontal: 14,
    marginTop: 14,
  },
  scannerBox: {
    borderRadius: 8,
    overflow: 'hidden',
    backgroundColor: '#111827',
  },
  camera: {
    height: 260,
  },
  closeScanner: {
    alignItems: 'center',
    paddingVertical: 11,
    backgroundColor: '#111827',
  },
  closeScannerText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '800',
  },
  issuedBox: {
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#dfe3e8',
    borderRadius: 8,
    gap: 10,
    padding: 12,
    backgroundColor: '#fff',
  },
  issuedTitle: {
    color: colors.text,
    fontSize: 15,
    fontWeight: '900',
    textAlign: 'center',
  },
  customerQr: {
    width: 190,
    height: 190,
    resizeMode: 'contain',
  },
  keyText: {
    color: colors.muted,
    fontSize: 12,
    lineHeight: 18,
    textAlign: 'center',
  },
  registerOptions: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 10,
    backgroundColor: '#f1f5f9',
    padding: 4,
    borderRadius: 8,
  },
  registerTab: {
    flex: 1,
    minHeight: 44,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 6,
  },
  registerTabActive: {
    backgroundColor: '#fff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  registerTabText: {
    color: '#64748b',
    fontSize: 14,
    fontWeight: '700',
  },
  registerTabTextActive: {
    color: colors.brand,
    fontWeight: '800',
  },
  companyPanel: {
    ...commonStyles.section,
    gap: 6,
    margin: 14,
  },
  companyTitle: {
    color: colors.text,
    fontSize: 15,
    fontWeight: '900',
  },
  companyText: {
    color: colors.muted,
    fontSize: 13,
    lineHeight: 19,
  },
  legalLinks: {
    gap: 7,
    marginTop: 7,
  },
  link: {
    color: colors.brand,
    fontSize: 13,
    fontWeight: '700',
    lineHeight: 20,
  },
});
