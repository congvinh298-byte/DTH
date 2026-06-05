import React, { useEffect, useMemo, useState } from 'react';
import { Image, Pressable, RefreshControl, ScrollView, StyleSheet, Text, View } from 'react-native';
import { getApiErrorMessage } from '../../core/api/client';
import { loadStoreCounts } from '../../core/api/store';
import { COMPANY } from '../../core/data/company';
import { colors, commonStyles } from '../../core/theme';
import StateNotice from '../../shared/widgets/StateNotice';

const MARKET_CATEGORIES = [
  { id: 'food', name: 'Đồ ăn sáng', icon: '🍔', desc: 'Trống' },
  { id: 'cafe', name: 'Quán cafe', icon: '☕', desc: 'Trống' },
  { id: 'clothes', name: 'Shop quần áo', icon: '👗', desc: 'Trống' },
  { id: 'grocery', name: 'Tạp hóa', icon: '🛒', desc: 'Trống' },
  { id: 'mechanic', name: 'Gọi thợ', icon: '🔧', desc: 'Bảng giá đã sẵn sàng' },
  { id: 'map', name: 'Bản đồ Lấp Vò', icon: '🗺️', desc: 'Xem toàn cảnh' },
];

function storeStatus(count) {
  if (!count) {
    return 'Trống';
  }
  if (count === 1) {
    return '1 cửa hàng đang mở';
  }
  return `${count} cửa hàng đang mở`;
}

export default function HomeScreen({ navigation, session, onLogout }) {
  const [counts, setCounts] = useState({ activeTotal: 0, pendingTotal: 0, types: {} });
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState('');

  const loadCounts = async () => {
    setError('');
    try {
      setCounts(await loadStoreCounts());
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không tải được số cửa hàng.'));
    }
  };

  useEffect(() => {
    loadCounts();
  }, []);

  const refresh = async () => {
    setRefreshing(true);
    await loadCounts();
    setRefreshing(false);
  };

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={refresh} colors={[colors.brand]} />}
    >
      <View style={styles.header}>
        <Image source={require('../../../assets/logo.jpg')} style={styles.logo} />
        <View style={styles.headerText}>
          <Text style={styles.brand}>{COMPANY.brand}</Text>
          <Text style={styles.subtitle}>{COMPANY.slogan}</Text>
          <Text style={styles.scope}>{COMPANY.serviceArea}</Text>
        </View>
      </View>

      <View style={styles.customerPanel}>
        <View>
          <Text style={styles.customerLabel}>Khách hàng</Text>
          <Text style={styles.customerPhone}>{session?.name || session?.phone || 'Khách hàng'}</Text>
        </View>
        <Pressable onPress={onLogout} style={styles.logoutButton}>
          <Text style={styles.logoutText}>Đăng xuất</Text>
        </Pressable>
      </View>

      <View style={styles.statusStrip}>
        <Text style={styles.statusMain}>{storeStatus(counts.activeTotal)}</Text>
        <Text style={styles.statusSub}>
          {counts.pendingTotal ? `${counts.pendingTotal} cửa hàng đang chờ giám đốc duyệt` : 'Không có đơn cửa hàng đang chờ'}
        </Text>
      </View>

      {error ? <StateNotice type="error">{error}</StateNotice> : null}

      <View style={styles.actionGrid}>
        {MARKET_CATEGORIES.map((cat) => (
          <Pressable
            key={cat.id}
            onPress={() => {
              if (cat.id === 'mechanic') {
                navigation.navigate('Booking', { serviceId: 'all' });
              } else if (cat.id === 'map') {
                navigation.navigate('MapScreen');
              } else {
                navigation.navigate('StoreList', { category: cat.id, categoryName: cat.name });
              }
            }}
            style={({ pressed }) => [styles.actionButton, pressed && styles.pressed]}
          >
            <Text style={{ fontSize: 40, marginBottom: 10 }}>{cat.icon}</Text>
            <Text style={styles.catName}>{cat.name}</Text>
          </Pressable>
        ))}
      </View>

      <View style={styles.companyPanel}>
        <Text style={styles.companyText}>{COMPANY.approvalStatus}</Text>
        <Text style={styles.companyText}>{COMPANY.legalName} - MST: {COMPANY.taxCode}</Text>
      </View>
    </ScrollView>
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
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  logo: {
    width: 92,
    height: 58,
    resizeMode: 'contain',
  },
  headerText: {
    flex: 1,
    gap: 4,
  },
  brand: {
    color: colors.brand,
    fontSize: 24,
    fontWeight: '900',
  },
  subtitle: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '700',
  },
  scope: {
    color: colors.success,
    fontSize: 13,
    fontWeight: '800',
  },
  customerPanel: {
    ...commonStyles.section,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 10,
  },
  customerLabel: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
  },
  customerPhone: {
    color: colors.text,
    fontSize: 18,
    fontWeight: '900',
    marginTop: 3,
  },
  logoutButton: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 9,
    backgroundColor: colors.surface,
  },
  logoutText: {
    color: colors.muted,
    fontWeight: '800',
  },
  statusStrip: {
    borderRadius: 8,
    backgroundColor: colors.dark,
    padding: 14,
    gap: 4,
  },
  statusMain: {
    color: '#ffffff',
    fontSize: 20,
    fontWeight: '900',
  },
  statusSub: {
    color: '#d1d5db',
    fontSize: 13,
    fontWeight: '700',
  },
  actionGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  actionButton: {
    width: '48%',
    minHeight: 112,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 13,
    alignItems: 'center',
    justifyContent: 'center',
  },
  pressed: {
    borderColor: colors.brand,
    backgroundColor: '#fff7f7',
  },
  catName: {
    color: colors.text,
    fontSize: 16,
    fontWeight: '900',
    textAlign: 'center',
  },
  companyPanel: {
    gap: 4,
    paddingHorizontal: 4,
    paddingBottom: 18,
  },
  companyText: {
    color: colors.muted,
    fontSize: 12,
    lineHeight: 17,
  },
});
