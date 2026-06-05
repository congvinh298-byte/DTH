import { requestApi } from './client';

export function normalizeCustomer(item = {}) {
  return {
    id: String(item.id || ''),
    name: String(item.fullname || item.name || '').trim(),
    phone: String(item.phone || '').trim(),
    rank: String(item.member_rank || '').trim(),
    totalSpent: Number(item.total_spent || 0),
    loyaltyPoints: Number(item.loyalty_points || 0),
    loginKey: String(item.login_key || '').trim(),
    qrPayload: String(item.qr_payload || '').trim(),
    qrImageUrl: String(item.qr_image_url || '').trim(),
  };
}

export async function registerCustomer(payload) {
  const result = await requestApi('app_customer_register', {
    method: 'post',
    data: payload,
  });

  if (result?.status !== 'success' || !result?.data) {
    throw new Error(result?.message || 'Không thể đăng ký khách hàng.');
  }

  return {
    message: result.message,
    customer: normalizeCustomer(result.data),
  };
}

export async function loginCustomer(payload) {
  const result = await requestApi('app_customer_login_qr', {
    method: 'post',
    data: payload,
  });

  if (result?.status !== 'success' || !result?.data) {
    throw new Error(result?.message || 'Không thể đăng nhập khách hàng.');
  }

  return normalizeCustomer(result.data);
}
