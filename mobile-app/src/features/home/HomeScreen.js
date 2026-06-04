import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';

const CATEGORIES = [
  { id: '1', name: 'Chợ Thực Phẩm', icon: '🛒' },
  { id: '2', name: 'Quán Ăn', icon: '🍜' },
  { id: '3', name: 'Trạm Xăng', icon: '⛽' },
  { id: '4', name: 'Dịch Vụ Gọi Thợ', icon: '🛠️' },
  { id: '5', name: 'Quần Áo', icon: '👕' },
  { id: '6', name: 'Cơ Quan', icon: '🏢' },
];

export default function HomeScreen({ navigation }) {
  return (
    <ScrollView style={styles.container}>
      <View style={styles.headerBox}>
        <Text style={styles.title}>Chợ Xã Lấp Vò</Text>
        <Text style={styles.subtitle}>Khám phá mọi tiện ích quanh bạn</Text>
      </View>

      <TouchableOpacity 
        style={styles.mapButton} 
        onPress={() => navigation.navigate('MapScreen')}
      >
        <Text style={styles.mapButtonIcon}>📍</Text>
        <Text style={styles.mapButtonText}>Mở Bản Đồ Khám Phá</Text>
      </TouchableOpacity>

      <Text style={styles.sectionTitle}>Danh mục dịch vụ</Text>
      <View style={styles.grid}>
        {CATEGORIES.map(cat => (
          <TouchableOpacity key={cat.id} style={styles.categoryCard} onPress={() => navigation.navigate('MapScreen')}>
            <Text style={styles.categoryIcon}>{cat.icon}</Text>
            <Text style={styles.categoryName}>{cat.name}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <View style={{ height: 20 }} />

      <TouchableOpacity 
        style={styles.sellerButton} 
        onPress={() => navigation.navigate('StoreLogin')}
      >
        <Text style={styles.sellerButtonText}>Dành cho Chủ cửa hàng (Mở quầy)</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6' },
  headerBox: { padding: 30, backgroundColor: '#dc2626', borderBottomLeftRadius: 30, borderBottomRightRadius: 30 },
  title: { fontSize: 26, fontWeight: 'bold', color: '#fff', textAlign: 'center' },
  subtitle: { fontSize: 16, color: '#fee2e2', textAlign: 'center', marginTop: 5 },
  
  mapButton: { backgroundColor: '#111827', margin: 20, padding: 20, borderRadius: 15, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' },
  mapButtonIcon: { fontSize: 24, marginRight: 10 },
  mapButtonText: { color: '#fff', fontSize: 18, fontWeight: 'bold' },

  sectionTitle: { fontSize: 18, fontWeight: 'bold', marginHorizontal: 20, marginBottom: 10, color: '#374151' },
  grid: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 10 },
  categoryCard: { width: '33.33%', padding: 10, alignItems: 'center' },
  categoryIcon: { fontSize: 40, backgroundColor: '#fff', padding: 15, borderRadius: 20, overflow: 'hidden', elevation: 2 },
  categoryName: { marginTop: 10, fontSize: 14, fontWeight: '500', color: '#4b5563', textAlign: 'center' },

  sellerButton: { margin: 20, padding: 15, borderRadius: 10, borderWidth: 1, borderColor: '#dc2626', alignItems: 'center' },
  sellerButtonText: { color: '#dc2626', fontWeight: 'bold', fontSize: 16 }
});
