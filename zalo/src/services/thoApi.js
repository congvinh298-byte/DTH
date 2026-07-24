const API_BASE = window.location.origin.includes('localhost')
  ? 'https://dienmayhieu.com/api/v1/tho'
  : '/api/v1/tho';

export function getThoToken() {
  return localStorage.getItem('tho_token') || '';
}

export function setThoToken(token) {
  if (token) {
    localStorage.setItem('tho_token', token);
  } else {
    localStorage.removeItem('tho_token');
  }
}

export function clearThoAuth() {
  localStorage.removeItem('tho_token');
  localStorage.removeItem('tho_info');
}

async function request(endpoint, options = {}) {
  const token = getThoToken();
  const headers = {
    ...(options.headers || {}),
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  let body = options.body;
  if (body && typeof body === 'object' && !(body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    body = JSON.stringify(body);
  }

  try {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
      ...options,
      headers,
      body,
    });
    const data = await res.json();
    if (res.status === 401) {
      clearThoAuth();
    }
    return data;
  } catch (err) {
    console.error(`API Error (${endpoint}):`, err);
    return { status: 'error', msg: 'Lỗi kết nối máy chủ' };
  }
}

export async function thoLogin(username, password) {
  const res = await request('login.php', {
    method: 'POST',
    body: { username, password, platform: 'zalo_miniapp' },
  });
  if (res.status === 'success' && res.token) {
    setThoToken(res.token);
    localStorage.setItem('tho_info', JSON.stringify(res.user));
  }
  return res;
}

export async function fetchThoProfile() {
  const res = await request('me.php', { method: 'GET' });
  if (res.status === 'success' && res.user) {
    localStorage.setItem('tho_info', JSON.stringify(res.user));
  }
  return res;
}

export async function fetchThoOrders(tab = 'cho') {
  return await request(`orders.php?tab=${tab}`, { method: 'GET' });
}

export async function acceptOrder(id) {
  return await request('accept.php', {
    method: 'POST',
    body: { id },
  });
}

export async function submitQuote(id, mota, gia) {
  return await request('quote.php', {
    method: 'POST',
    body: { id, mota, gia },
  });
}

export async function completeOrder(id) {
  return await request('complete.php', {
    method: 'POST',
    body: { id },
  });
}

export async function uploadNghiemThu(id, file, note = '') {
  const formData = new FormData();
  formData.append('id', id);
  formData.append('anh', file);
  formData.append('note', note);

  return await request('upload.php', {
    method: 'POST',
    body: formData,
  });
}

export async function checkNewOrders() {
  return await request('checknew.php', { method: 'GET' });
}
