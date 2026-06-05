import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert } from 'react-native';

export default function StoreMenuScreen({ route }) {
  const { storeId, storeName } = route.params || {};

  return (
    <View style={styles.container}>
      <Text style={styles.title}>{storeName}</Text>
      <Text style={styles.subtitle}>Danh sách dịch vụ / sản phẩm đang được cập nhật...</Text>
      
      <TouchableOpacity 
        style={styles.btn}
        onPress={() => Alert.alert('Gọi chủ cửa hàng', 'Chức năng gọi điện đang được phát triển')}
      >
        <Text style={styles.btnText}>Gọi chủ cửa hàng ngay</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', padding: 20, alignItems: 'center', justifyContent: 'center' },
  title: { fontSize: 26, fontWeight: 'bold', color: '#111827', marginBottom: 10 },
  subtitle: { fontSize: 16, color: '#6b7280', marginBottom: 30, textAlign: 'center' },
  btn: { backgroundColor: '#10b981', padding: 15, borderRadius: 8, width: '100%', alignItems: 'center' },
  btnText: { color: '#fff', fontSize: 18, fontWeight: 'bold' }
});
