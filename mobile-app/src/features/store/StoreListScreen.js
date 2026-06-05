import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator } from 'react-native';
import api from '../../core/api/client';

export default function StoreListScreen({ route, navigation }) {
  const { category, categoryName } = route.params || {};
  const [stores, setStores] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('?action=app_get_map_pins')
      .then(res => {
        if (res.data.status === 'success') {
          // Filter by category if needed, here we just show all or match
          // Since we don't have strict mapping, we'll just show all active for now or map name
          let filtered = res.data.data;
          if (category === 'food') filtered = filtered.filter(s => s.store_type?.toLowerCase().includes('ăn'));
          if (category === 'cafe') filtered = filtered.filter(s => s.store_type?.toLowerCase().includes('cafe'));
          
          setStores(filtered);
        }
      })
      .catch(err => console.log(err))
      .finally(() => setLoading(false));
  }, [category]);

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color="#dc2626" /></View>;
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Danh sách: {categoryName}</Text>
      {stores.length === 0 ? (
        <Text style={styles.empty}>Chưa có cửa hàng nào trong mục này.</Text>
      ) : (
        <FlatList
          data={stores}
          keyExtractor={item => item.id.toString()}
          renderItem={({ item }) => (
            <TouchableOpacity 
              style={styles.card}
              onPress={() => navigation.navigate('StoreMenu', { storeId: item.id, storeName: item.store_name })}
            >
              <Text style={styles.storeName}>{item.store_name}</Text>
              <Text style={styles.address}>{item.address}</Text>
              <View style={styles.ratingBox}>
                <Text style={styles.ratingText}>⭐ {(item.rating_score || 5.0).toFixed(1)}</Text>
                <Text style={styles.ratingCount}>({item.rating_count || 0} lượt)</Text>
              </View>
            </TouchableOpacity>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6', padding: 15 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 22, fontWeight: 'bold', marginBottom: 15, color: '#111827' },
  empty: { textAlign: 'center', marginTop: 50, color: '#6b7280' },
  card: { backgroundColor: '#fff', padding: 15, borderRadius: 10, marginBottom: 10, elevation: 2 },
  storeName: { fontSize: 18, fontWeight: 'bold', color: '#111827' },
  address: { fontSize: 14, color: '#6b7280', marginTop: 5, marginBottom: 10 },
  ratingBox: { flexDirection: 'row', alignItems: 'center' },
  ratingText: { color: '#f59e0b', fontWeight: 'bold', fontSize: 16, marginRight: 5 },
  ratingCount: { color: '#6b7280', fontSize: 14 }
});
