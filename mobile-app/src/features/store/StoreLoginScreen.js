import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ActivityIndicator } from 'react-native';
import api from '../../core/api/client';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function StoreLoginScreen({ navigation }) {
  const [phone, setPhone] = useState('');
  const [taxCode, setTaxCode] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    if (!phone || !taxCode) {
      Alert.alert('Lỗi', 'Vui lòng nhập SĐT và Mã Số Thuế hợp lệ!');
      return;
    }
    setLoading(true);
    try {
      const res = await api.post('?action=app_store_login', { phone, tax_code: taxCode });
      if (res.data.status === 'success') {
        await AsyncStorage.setItem('store_info', JSON.stringify(res.data.data));
        Alert.alert('Thành công', `Chào mừng cửa hàng: ${res.data.data.store_name}`);
        navigation.goBack();
      } else {
        Alert.alert('Lỗi', res.data.message || 'Có lỗi xảy ra.');
      }
    } catch (err) {
      Alert.alert('Lỗi', 'Không thể kết nối máy chủ.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Mở Cửa Hàng Chợ Lấp Vò</Text>
      <Text style={styles.subtitle}>Nhập Mã số thuế để hệ thống tự động nhận diện Cơ sở kinh doanh của bạn.</Text>
      
      <TextInput
        style={styles.input}
        placeholder="Số điện thoại liên hệ"
        keyboardType="phone-pad"
        value={phone}
        onChangeText={setPhone}
      />
      <TextInput
        style={styles.input}
        placeholder="Mã Số Thuế (VD: 0312...)"
        value={taxCode}
        onChangeText={setTaxCode}
      />
      
      <TouchableOpacity style={styles.button} onPress={handleLogin} disabled={loading}>
        {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.buttonText}>Đăng Ký / Đăng Nhập</Text>}
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, justifyContent: 'center', backgroundColor: '#fff' },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 10, textAlign: 'center', color: '#111827' },
  subtitle: { fontSize: 14, color: '#6b7280', textAlign: 'center', marginBottom: 30 },
  input: { borderWidth: 1, borderColor: '#ccc', borderRadius: 8, padding: 15, fontSize: 16, marginBottom: 20 },
  button: { backgroundColor: '#dc2626', padding: 15, borderRadius: 8, alignItems: 'center' },
  buttonText: { color: '#fff', fontSize: 18, fontWeight: 'bold' }
});
