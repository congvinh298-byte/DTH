import { requestApi } from './client';

function normalizeProduct(item, index) {
  const name = String(item?.name || '').trim();
  const price = Number(item?.price || item?.gia_ban || 0);
  if (!name || !Number.isFinite(price) || price <= 0) {
    return null;
  }

  const category = String(item?.category || item?.store_type || 'Sản phẩm').trim();
  return {
    id: String(item?.id || `product-${index}`),
    name,
    price,
    priceFormatted: String(item?.gia_ban_fm || '').trim(),
    stock: Math.max(0, Number(item?.stock_quantity ?? item?.stock ?? 0)),
    category,
    source: String(item?.src || 'product').trim(),
    orderType: category.toLowerCase() === 'sim' ? 'sim' : 'product',
    imageUri: String(item?.image_url || item?.image || '').trim(),
  };
}

function normalizeStore(item, index) {
  const latitude = Number(item?.lat ?? item?.latitude);
  const longitude = Number(item?.lng ?? item?.longitude);

  return {
    id: String(item?.id || `store-${index}`),
    storeName: String(item?.store_name || item?.name || 'Cửa hàng').trim(),
    ownerName: String(item?.owner_name || '').trim(),
    phone: String(item?.phone || '').trim(),
    taxCode: String(item?.tax_code || '').trim(),
    storeType: String(item?.store_type || 'Cửa hàng').trim(),
    address: String(item?.address || '').trim(),
    latitude: Number.isFinite(latitude) ? latitude : null,
    longitude: Number.isFinite(longitude) ? longitude : null,
    status: String(item?.status || 'active').trim(),
    loginKey: String(item?.login_key || '').trim(),
    qrPayload: String(item?.qr_payload || '').trim(),
    totalSales: Number(item?.total_sales || 0),
    orderCount: Number(item?.order_count || 0),
  };
}

export async function loadProducts() {
  const result = await requestApi('get_products');
  if (result?.status !== 'success' || !Array.isArray(result?.data)) {
    throw new Error(result?.message || 'Không đọc được kho sản phẩm.');
  }

  return result.data.map(normalizeProduct).filter(Boolean);
}

export async function createOrder(payload) {
  const result = await requestApi('create_order', {
    method: 'post',
    data: payload,
  });

  if (result?.status !== 'success') {
    throw new Error(result?.message || 'Không thể tạo đơn hàng.');
  }

  return result;
}

export async function loginStore(payload) {
  const result = await requestApi('app_store_login_qr', {
    method: 'post',
    data: payload,
  });

  if (result?.status !== 'success' || !result?.data) {
    throw new Error(result?.message || 'Không thể đồng bộ cửa hàng.');
  }

  return normalizeStore(result.data, 0);
}

export async function registerStore(payload) {
  const result = await requestApi('app_store_register', {
    method: 'post',
    data: payload,
  });

  if (result?.status !== 'success' || !result?.data) {
    throw new Error(result?.message || 'Không thể gửi đăng ký cửa hàng.');
  }

  return {
    message: result.message,
    approvalStatus: result.approval_status || 'pending',
    store: normalizeStore(result.data, 0),
  };
}

export async function loadStoreCounts() {
  const result = await requestApi('app_store_counts');
  if (result?.status !== 'success' || !result?.data) {
    throw new Error(result?.message || 'Không tải được thống kê cửa hàng.');
  }

  return {
    activeTotal: Number(result.data.active_total || 0),
    pendingTotal: Number(result.data.pending_total || 0),
    types: result.data.types || {},
  };
}

export async function loadMapPins() {
  const result = await requestApi('app_get_map_pins');
  if (result?.status !== 'success' || !Array.isArray(result?.data)) {
    throw new Error(result?.message || 'Không tải được danh sách cửa hàng.');
  }

  return result.data.map(normalizeStore).filter((store) => (
    store.latitude !== null && store.longitude !== null
  ));
}

export async function loadStoreProducts(loginKey) {
  const result = await requestApi('app_store_get_products', {
    method: 'post',
    data: { login_key: loginKey },
  });
  if (result?.status !== 'success' || !Array.isArray(result?.data)) {
    throw new Error(result?.message || 'Không tải được danh sách hàng hóa.');
  }
  return result.data.map(normalizeProduct).filter(Boolean);
}

export async function saveStoreProduct(loginKey, payload) {
  const result = await requestApi('app_store_save_product', {
    method: 'post',
    data: { login_key: loginKey, ...payload },
  });
  if (result?.status !== 'success') {
    throw new Error(result?.message || 'Không lưu được hàng hóa.');
  }
  return result;
}

export async function deleteStoreProduct(loginKey, productId) {
  const result = await requestApi('app_store_delete_product', {
    method: 'post',
    data: { login_key: loginKey, id: productId },
  });
  if (result?.status !== 'success') {
    throw new Error(result?.message || 'Không xóa được hàng hóa.');
  }
  return result;
}

export async function scanStoreMenu(loginKey, base64Image) {
  const result = await requestApi('app_store_scan_menu', {
    method: 'post',
    data: { login_key: loginKey, image_base64: base64Image },
  });
  if (result?.status !== 'success') {
    throw new Error(result?.message || 'Không quét được menu.');
  }
  return result;
}

