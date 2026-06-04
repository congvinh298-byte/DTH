import { requestApi } from './client';

function normalizeProduct(item, index) {
  const name = String(item?.name || '').trim();
  const price = Number(item?.price || item?.gia_ban || 0);
  if (!name || !Number.isFinite(price) || price <= 0) {
    return null;
  }

  const category = String(item?.category || 'Sản phẩm').trim();
  return {
    id: String(item?.id || `product-${index}`),
    name,
    price,
    priceFormatted: String(item?.gia_ban_fm || '').trim(),
    stock: Math.max(0, Number(item?.stock_quantity || 0)),
    category,
    source: String(item?.src || 'product').trim(),
    orderType: category.toLowerCase() === 'sim' ? 'sim' : 'product',
    imageUri: String(item?.image_url || item?.image || '').trim(),
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
