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
} from 'react-native';
import { COMPANY, LEGAL_LINKS } from '../../core/data/company';
import { colors, commonStyles } from '../../core/theme';
import { isValidPhone, normalizePhone, saveSession } from '../../core/storage/session';
import PrimaryButton from '../../shared/widgets/PrimaryButton';

export default function LoginScreen({ onAuthenticated }) {
  const [phone, setPhone] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    const normalizedPhone = normalizePhone(phone);
    if (!isValidPhone(normalizedPhone)) {
      Alert.alert('Số điện thoại chưa đúng', 'Vui lòng nhập từ 8 đến 15 chữ số.');
      return;
    }

    setLoading(true);
    try {
      const nextSession = { phone: normalizedPhone, name: '' };
      await saveSession(nextSession);
      onAuthenticated(nextSession);
    } catch {
      Alert.alert('Không thể đăng nhập', 'Ứng dụng chưa lưu được thông tin trên thiết bị.');
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
        <Text style={styles.approval}>{COMPANY.approvalStatus}</Text>

        <View style={styles.brandBlock}>
          <Image source={require('../../../assets/logo.jpg')} style={styles.logo} />
          <Text style={styles.brand}>{COMPANY.brand}</Text>
          <Text style={styles.slogan}>{COMPANY.slogan}</Text>
          <Text style={styles.mission}>{COMPANY.mission}</Text>
          <Text style={styles.scope}>Phạm vi phục vụ: bán kính 15 km từ Chợ Lấp Vò</Text>
        </View>

        <View style={styles.loginPanel}>
          <Text style={styles.title}>Đăng nhập hệ sinh thái</Text>
          <Text style={styles.description}>
            Số điện thoại được dùng để mua hàng, gọi thợ và để nhân viên liên hệ xác nhận.
          </Text>
          <TextInput
            autoComplete="tel"
            keyboardType="phone-pad"
            maxLength={15}
            onChangeText={(value) => setPhone(normalizePhone(value))}
            onSubmitEditing={handleLogin}
            placeholder="Nhập số điện thoại"
            returnKeyType="done"
            style={commonStyles.input}
            value={phone}
          />
          <PrimaryButton label="Tiếp tục" loading={loading} onPress={handleLogin} />
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
