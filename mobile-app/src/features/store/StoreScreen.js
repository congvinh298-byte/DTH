import React, { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ActivityIndicator,
  FlatList,
  Image,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { getApiErrorMessage } from '../../core/api/client';
import { loadProducts } from '../../core/api/store';
import { formatMoney } from '../../core/data/services';
import { colors, commonStyles } from '../../core/theme';
import StateNotice from '../../shared/widgets/StateNotice';

export default function StoreScreen({ navigation }) {
  const [products, setProducts] = useState([]);
  const [query, setQuery] = useState('');
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState('');

  const fetchProducts = useCallback(async (refresh = false) => {
    refresh ? setRefreshing(true) : setLoading(true);
    setError('');
    try {
      setProducts(await loadProducts());
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không đọc được kho hàng.'));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchProducts();
  }, [fetchProducts]);

  const visibleProducts = useMemo(() => {
    const keyword = query.trim().toLocaleLowerCase('vi-VN');
    if (!keyword) {
      return products;
    }
    return products.filter(
      (product) =>
        product.name.toLocaleLowerCase('vi-VN').includes(keyword) ||
        product.category.toLocaleLowerCase('vi-VN').includes(keyword),
    );
  }, [products, query]);

  return (
    <View style={styles.container}>
      <FlatList
        contentContainerStyle={styles.list}
        data={visibleProducts}
        keyExtractor={(item) => item.id}
        ListHeaderComponent={
          <View style={styles.header}>
            <Text style={styles.heading}>Kho sản phẩm</Text>
            <TextInput
              onChangeText={setQuery}
              placeholder="Tìm sản phẩm hoặc SIM"
              style={commonStyles.input}
              value={query}
            />
            {error ? <StateNotice type="error">{error}</StateNotice> : null}
          </View>
        }
        ListEmptyComponent={
          loading ? (
            <ActivityIndicator color={colors.brand} size="large" />
          ) : (
            <StateNotice>Không có sản phẩm phù hợp.</StateNotice>
          )
        }
        refreshControl={
          <RefreshControl
            colors={[colors.brand]}
            onRefresh={() => fetchProducts(true)}
            refreshing={refreshing}
            tintColor={colors.brand}
          />
        }
        renderItem={({ item }) => (
          <Pressable
            disabled={item.stock <= 0}
            onPress={() => navigation.navigate('Order', { product: item })}
            style={({ pressed }) => [
              styles.productRow,
              item.stock <= 0 && styles.productRowDisabled,
              pressed && item.stock > 0 && styles.productRowPressed,
            ]}
          >
            {item.imageUri ? (
              <Image source={{ uri: item.imageUri }} style={styles.image} />
            ) : (
              <View style={styles.imagePlaceholder}>
                <Text style={styles.imagePlaceholderText}>{item.category}</Text>
              </View>
            )}
            <View style={styles.productMain}>
              <Text style={styles.category}>{item.category}</Text>
              <Text style={styles.name}>{item.name}</Text>
              <Text style={styles.stock}>Còn: {item.stock}</Text>
            </View>
            <View style={styles.priceColumn}>
              <Text style={styles.price}>{item.priceFormatted || formatMoney(item.price)}</Text>
              <Text style={styles.order}>{item.stock > 0 ? 'Đặt hàng' : 'Hết hàng'}</Text>
            </View>
          </Pressable>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  list: {
    padding: 14,
    paddingBottom: 30,
    gap: 8,
  },
  header: {
    gap: 12,
    marginBottom: 8,
  },
  heading: {
    color: colors.text,
    fontSize: 24,
    fontWeight: '800',
  },
  productRow: {
    minHeight: 96,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 10,
  },
  productRowPressed: {
    borderColor: colors.brand,
  },
  productRowDisabled: {
    opacity: 0.55,
  },
  image: {
    width: 68,
    height: 68,
    resizeMode: 'contain',
    borderRadius: 6,
  },
  imagePlaceholder: {
    width: 68,
    height: 68,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 6,
    backgroundColor: '#f2f4f7',
    padding: 6,
  },
  imagePlaceholderText: {
    color: colors.muted,
    fontSize: 11,
    fontWeight: '700',
    textAlign: 'center',
  },
  productMain: {
    flex: 1,
    gap: 4,
  },
  category: {
    color: colors.muted,
    fontSize: 12,
    fontWeight: '700',
  },
  name: {
    color: colors.text,
    fontSize: 15,
    fontWeight: '800',
  },
  stock: {
    color: colors.muted,
    fontSize: 12,
  },
  priceColumn: {
    maxWidth: 105,
    alignItems: 'flex-end',
    gap: 7,
  },
  price: {
    color: colors.brand,
    fontSize: 14,
    fontWeight: '800',
    textAlign: 'right',
  },
  order: {
    color: colors.brand,
    fontSize: 13,
    fontWeight: '700',
  },
});
