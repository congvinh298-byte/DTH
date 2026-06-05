import axios from 'axios';

export const API_URL =
  process.env.EXPO_PUBLIC_API_URL?.trim() || 'https://dienmayhieu.com/api_master.php';

const api = axios.create({
  baseURL: API_URL,
  timeout: 20000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

export async function requestApi(action, { method = 'get', params = {}, data } = {}) {
  const response = await api.request({
    url: '',
    method,
    params: { action, ...params },
    data,
  });

  return response.data;
}

export function getApiErrorMessage(error, fallback = 'Không thể kết nối máy chủ.') {
  const serverMessage = error?.response?.data?.message;
  if (typeof serverMessage === 'string' && serverMessage.trim()) {
    return serverMessage.trim();
  }

  if (error?.response?.status === 404) {
    return 'Chức năng này chưa được máy chủ hỗ trợ. Hãy cập nhật api_master.php trên hosting.';
  }

  if (error?.code === 'ECONNABORTED') {
    return 'Máy chủ phản hồi quá lâu. Vui lòng thử lại.';
  }

  if (!error?.response) {
    if (!error?.isAxiosError && typeof error?.message === 'string' && error.message.trim()) {
      return error.message.trim();
    }
    return 'Không có kết nối đến máy chủ. Hãy kiểm tra mạng và thử lại.';
  }

  return fallback;
}

export default api;
