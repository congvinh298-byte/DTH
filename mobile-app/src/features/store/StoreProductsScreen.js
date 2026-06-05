import React, { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  FlatList,
  Image,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  View,
  Modal,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { LinearGradient } from 'expo-linear-gradient';
import { BlurView } from 'expo-blur';
import { getApiErrorMessage } from '../../core/api/client';
import { loadStoreProducts, saveStoreProduct, deleteStoreProduct, scanStoreMenu } from '../../core/api/store';
import { formatMoney } from '../../core/data/services';
import { loadStoreSession } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import StateNotice from '../../shared/widgets/StateNotice';

export default function StoreProductsScreen({ navigation }) {
  const [session, setSession] = useState(null);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState('');

  // Modal state
  const [modalVisible, setModalVisible] = useState(false);
  const [editingId, setEditingId] = useState(0);
  const [name, setName] = useState('');
  const [price, setPrice] = useState('');
  const [stock, setStock] = useState('100');
  const [category, setCategory] = useState('');
  const [imageUri, setImageUri] = useState('');
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    loadStoreSession().then(s => {
      if (!s || !s.loginKey) {
        Alert.alert('Chưa đăng nhập', 'Vui lòng đăng nhập cửa hàng trước.', [
          { text: 'OK', onPress: () => navigation.replace('StoreLogin') }
        ]);
        return;
      }
      setSession(s);
    });
  }, []);

  const fetchProducts = async (refresh = false) => {
    if (!session) return;
    refresh ? setRefreshing(true) : setLoading(true);
    setError('');
    try {
      setProducts(await loadStoreProducts(session.loginKey));
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không tải được danh sách hàng hóa.'));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    if (session) {
      fetchProducts();
    }
  }, [session]);

  const openForm = (item = null) => {
    if (item) {
      setEditingId(item.id);
      setName(item.name || '');
      setPrice(String(item.price || ''));
      setStock(String(item.stock || '0'));
      setCategory(item.category || '');
      setImageUri(item.imageUri || '');
    } else {
      setEditingId(0);
      setName('');
      setPrice('');
      setStock('100');
      setCategory('Marketplace');
      setImageUri('');
    }
    setModalVisible(true);
  };

  const handlePickImage = async () => {
    const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (permissionResult.granted === false) {
      Alert.alert('Quyền truy cập', 'Vui lòng cấp quyền truy cập thư viện ảnh để upload ảnh sản phẩm!');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [1, 1],
      quality: 0.8,
      base64: true,
    });

    if (!result.canceled && result.assets[0]) {
      // Dùng Data URI dạng base64 để truyền lên server hoặc hiển thị tạm
      const base64Uri = `data:image/jpeg;base64,${result.assets[0].base64}`;
      setImageUri(base64Uri);
    }
  };

  const handleSave = async () => {
    if (!name.trim()) {
      Alert.alert('Lỗi', 'Vui lòng nhập tên sản phẩm.');
      return;
    }
    setSaving(true);
    try {
      await saveStoreProduct(session.loginKey, {
        id: editingId,
        name: name.trim(),
        price: parseInt(price.replace(/\D/g, '') || '0', 10),
        stock: parseInt(stock.replace(/\D/g, '') || '0', 10),
        category: category.trim(),
        image_url: imageUri.trim(), // Nếu là base64 thì cần backend hỗ trợ lưu, hiện tại backend lưu string nên ok.
      });
      setModalVisible(false);
      fetchProducts(true);
    } catch (err) {
      Alert.alert('Lỗi', err.message || 'Không lưu được.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = (id) => {
    Alert.alert('Xóa sản phẩm', 'Bạn có chắc chắn muốn xóa sản phẩm này?', [
      { text: 'Hủy', style: 'cancel' },
      {
        text: 'Xóa',
        style: 'destructive',
        onPress: async () => {
          try {
            await deleteStoreProduct(session.loginKey, id);
            fetchProducts(true);
          } catch (err) {
            Alert.alert('Lỗi', err.message || 'Không xóa được.');
          }
        }
      }
    ]);
  };

  if (!session) {
    return <ActivityIndicator style={{ flex: 1 }} color={colors.brand} />;
  }

  const totalProducts = products.length;
  const outOfStock = products.filter(p => p.stock <= 0).length;

  return (
    <View style={styles.container}>
      <LinearGradient colors={['#dc2626', '#ef4444']} style={styles.header}>
        <View style={styles.headerTop}>
          <Text style={styles.storeName}>🏪 {session.storeName}</Text>
          <View style={styles.statsRow}>
            <View style={styles.statBox}>
              <Text style={styles.statNum}>{totalProducts}</Text>
              <Text style={styles.statLabel}>Sản phẩm</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.statBox}>
              <Text style={styles.statNum}>{outOfStock}</Text>
              <Text style={styles.statLabel}>Hết hàng</Text>
            </View>
          </View>
        </View>
      </LinearGradient>

      {error ? <StateNotice type="error" style={{ margin: 14 }}>{error}</StateNotice> : null}

      <FlatList
        contentContainerStyle={styles.list}
        data={products}
        keyExtractor={(item) => String(item.id)}
        ListEmptyComponent={
          loading ? (
            <ActivityIndicator color={colors.brand} size="large" />
          ) : (
            <StateNotice>Bạn chưa đăng sản phẩm nào.</StateNotice>
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
          <View style={styles.productCard}>
            {item.imageUri ? (
              <Image source={{ uri: item.imageUri }} style={styles.image} />
            ) : (
              <View style={styles.imagePlaceholder}>
                <Text style={styles.imagePlaceholderText}>Không Ảnh</Text>
              </View>
            )}
            <View style={styles.productMain}>
              <Text style={styles.category}>{item.category}</Text>
              <Text style={styles.name} numberOfLines={2}>{item.name}</Text>
              {item.stock > 0 ? (
                <View style={styles.stockBadgeGreen}>
                  <Text style={styles.stockTextGreen}>Kho: {item.stock}</Text>
                </View>
              ) : (
                <View style={styles.stockBadgeRed}>
                  <Text style={styles.stockTextRed}>Hết hàng</Text>
                </View>
              )}
            </View>
            <View style={styles.priceColumn}>
              <Text style={styles.price}>{item.priceFormatted || formatMoney(item.price)}</Text>
              <View style={styles.actions}>
                <Pressable style={styles.actionBtnEdit} onPress={() => openForm(item)}>
                  <Text style={styles.actionTextEdit}>Sửa</Text>
                </Pressable>
                <Pressable style={styles.actionBtnDelete} onPress={() => handleDelete(item.id)}>
                  <Text style={styles.actionTextDelete}>Xóa</Text>
                </Pressable>
              </View>
            </View>
          </View>
        )}
      />

      {/* Modern Floating Action Button */}
      <Pressable style={styles.fab} onPress={() => openForm(null)}>
        <LinearGradient colors={['#dc2626', '#f87171']} style={styles.fabGradient}>
          <Text style={styles.fabText}>+</Text>
        </LinearGradient>
      </Pressable>

      <Modal
        animationType="fade"
        transparent={true}
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <BlurView intensity={40} tint="dark" style={styles.modalOverlay}>
          <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{width: '100%'}}>
            <View style={styles.modalContent}>
              <Text style={styles.modalTitle}>{editingId ? 'Sửa sản phẩm' : 'Đăng sản phẩm mới'}</Text>
              
              <ScrollView showsVerticalScrollIndicator={false}>
                {/* Image Picker */}
                <Pressable style={styles.imagePickerBtn} onPress={handlePickImage}>
                  {imageUri ? (
                    <Image source={{ uri: imageUri }} style={styles.imagePreview} />
                  ) : (
                    <View style={styles.imagePlaceholderLarge}>
                      <Text style={styles.imagePlaceholderLargeText}>+ Chọn ảnh chụp sản phẩm</Text>
                    </View>
                  )}
                </Pressable>

                <Text style={styles.label}>Tên sản phẩm *</Text>
                <TextInput
                  style={styles.modernInput}
                  value={name}
                  onChangeText={setName}
                  placeholder="VD: Mì tôm Hảo Hảo"
                  placeholderTextColor="#94a3b8"
                />
                
                <Text style={styles.label}>Giá bán (VNĐ) *</Text>
                <TextInput
                  style={styles.modernInput}
                  value={price}
                  onChangeText={setPrice}
                  placeholder="VD: 5000"
                  placeholderTextColor="#94a3b8"
                  keyboardType="number-pad"
                />
                
                <View style={{flexDirection: 'row', gap: 12}}>
                  <View style={{flex: 1}}>
                    <Text style={styles.label}>Tồn kho</Text>
                    <TextInput
                      style={styles.modernInput}
                      value={stock}
                      onChangeText={setStock}
                      keyboardType="number-pad"
                      placeholderTextColor="#94a3b8"
                    />
                  </View>
                  <View style={{flex: 1}}>
                    <Text style={styles.label}>Phân loại</Text>
                    <TextInput
                      style={styles.modernInput}
                      value={category}
                      onChangeText={setCategory}
                      placeholder="VD: Đồ ăn"
                      placeholderTextColor="#94a3b8"
                    />
                  </View>
                </View>

                <View style={styles.modalActions}>
                  <Pressable style={styles.btnCancel} onPress={() => setModalVisible(false)}>
                    <Text style={styles.btnCancelText}>Hủy</Text>
                  </Pressable>
                  <Pressable style={styles.btnSave} onPress={handleSave} disabled={saving}>
                    <LinearGradient colors={['#dc2626', '#ef4444']} style={styles.btnSaveGradient}>
                      <Text style={styles.btnSaveText}>{saving ? 'Đang lưu...' : 'Lưu lại'}</Text>
                    </LinearGradient>
                  </Pressable>
                </View>
              </ScrollView>
            </View>
          </KeyboardAvoidingView>
        </BlurView>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f1f5f9',
  },
  header: {
    padding: 20,
    paddingTop: 30,
    borderBottomLeftRadius: 20,
    borderBottomRightRadius: 20,
    elevation: 5,
    shadowColor: '#dc2626',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    marginBottom: 10,
  },
  headerTop: {
    gap: 12,
  },
  storeName: {
    color: '#fff',
    fontSize: 20,
    fontWeight: '900',
    textAlign: 'center',
  },
  statsRow: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255,255,255,0.2)',
    borderRadius: 12,
    paddingVertical: 10,
    paddingHorizontal: 20,
    justifyContent: 'space-evenly',
    alignItems: 'center',
  },
  statBox: {
    alignItems: 'center',
  },
  statNum: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  statLabel: {
    color: '#fee2e2',
    fontSize: 12,
    fontWeight: '500',
  },
  statDivider: {
    width: 1,
    height: 30,
    backgroundColor: 'rgba(255,255,255,0.3)',
  },
  scanBtn: {
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingVertical: 10,
    borderRadius: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.3)',
    marginTop: 6,
  },
  scanBtnText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 15,
  },
  scanningOverlay: {
    padding: 20,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 12,
  },
  scanningText: {
    color: '#dc2626',
    fontWeight: '600',
    fontSize: 14,
    textAlign: 'center',
  },
  list: {
    padding: 14,
    gap: 12,
    paddingBottom: 100,
  },
  productCard: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 12,
    gap: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 3,
  },
  image: {
    width: 70,
    height: 70,
    resizeMode: 'cover',
    borderRadius: 10,
  },
  imagePlaceholder: {
    width: 70,
    height: 70,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 10,
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  imagePlaceholderText: {
    color: '#94a3b8',
    fontSize: 10,
    textAlign: 'center',
  },
  productMain: {
    flex: 1,
    justifyContent: 'center',
    gap: 4,
  },
  category: {
    color: '#64748b',
    fontSize: 12,
    textTransform: 'uppercase',
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  name: {
    color: '#0f172a',
    fontSize: 15,
    fontWeight: '800',
    lineHeight: 20,
  },
  stockBadgeGreen: {
    backgroundColor: '#dcfce7',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
    alignSelf: 'flex-start',
  },
  stockTextGreen: {
    color: '#166534',
    fontSize: 11,
    fontWeight: '700',
  },
  stockBadgeRed: {
    backgroundColor: '#fee2e2',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
    alignSelf: 'flex-start',
  },
  stockTextRed: {
    color: '#991b1b',
    fontSize: 11,
    fontWeight: '700',
  },
  priceColumn: {
    alignItems: 'flex-end',
    justifyContent: 'space-between',
  },
  price: {
    color: '#dc2626',
    fontSize: 15,
    fontWeight: '900',
  },
  actions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionBtnEdit: {
    paddingVertical: 6,
    paddingHorizontal: 12,
    backgroundColor: '#f1f5f9',
    borderRadius: 6,
  },
  actionTextEdit: {
    fontSize: 12,
    color: '#334155',
    fontWeight: '700',
  },
  actionBtnDelete: {
    paddingVertical: 6,
    paddingHorizontal: 12,
    backgroundColor: '#fef2f2',
    borderRadius: 6,
  },
  actionTextDelete: {
    fontSize: 12,
    color: '#ef4444',
    fontWeight: '700',
  },
  fab: {
    position: 'absolute',
    bottom: 30,
    right: 20,
    width: 64,
    height: 64,
    borderRadius: 32,
    shadowColor: '#dc2626',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.4,
    shadowRadius: 8,
    elevation: 8,
  },
  fabGradient: {
    width: '100%',
    height: '100%',
    borderRadius: 32,
    alignItems: 'center',
    justifyContent: 'center',
  },
  fabText: {
    color: '#fff',
    fontSize: 36,
    fontWeight: '300',
    lineHeight: 40,
  },
  // Modal styles
  modalOverlay: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: 24,
    borderRadius: 24,
    gap: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.1,
    shadowRadius: 20,
    elevation: 10,
    maxHeight: '90%',
  },
  modalTitle: {
    fontSize: 22,
    fontWeight: '900',
    color: '#0f172a',
    marginBottom: 8,
    textAlign: 'center',
  },
  imagePickerBtn: {
    alignSelf: 'center',
    marginBottom: 10,
  },
  imagePreview: {
    width: 120,
    height: 120,
    borderRadius: 16,
  },
  imagePlaceholderLarge: {
    width: 120,
    height: 120,
    borderRadius: 16,
    backgroundColor: '#f8fafc',
    borderWidth: 2,
    borderColor: '#e2e8f0',
    borderStyle: 'dashed',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 10,
  },
  imagePlaceholderLargeText: {
    color: '#64748b',
    fontSize: 12,
    fontWeight: '600',
    textAlign: 'center',
  },
  label: {
    fontSize: 13,
    fontWeight: '700',
    color: '#475569',
    marginTop: 4,
    marginBottom: -4,
    marginLeft: 4,
  },
  modernInput: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    fontSize: 15,
    color: '#0f172a',
    fontWeight: '500',
  },
  modalActions: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 20,
  },
  btnCancel: {
    flex: 1,
    padding: 14,
    backgroundColor: '#f1f5f9',
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  btnCancelText: {
    color: '#475569',
    fontWeight: '800',
    fontSize: 15,
  },
  btnSave: {
    flex: 1,
  },
  btnSaveGradient: {
    padding: 14,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  btnSaveText: {
    color: '#fff',
    fontWeight: '800',
    fontSize: 15,
  },
});
