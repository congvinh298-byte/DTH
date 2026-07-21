const API_BASE = 'https://dienmayhieu.com/api';

export async function fetchProducts(options = {}) {
  const params = new URLSearchParams();
  if (options.featured) params.set('featured', '1');
  if (options.category) params.set('category', options.category);
  if (options.search) params.set('search', options.search);

  const url = `${API_BASE}/miniapp-products.php${params.toString() ? '?' + params.toString() : ''}`;
  try {
    const res = await fetch(url, { cache: 'no-cache' });
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (err) {
    console.error('fetchProducts error:', err);
    return { status: 'error', data: [] };
  }
}

export async function fetchProduct(id) {
  try {
    const res = await fetch(`${API_BASE}/miniapp-product.php?id=${id}`, { cache: 'no-cache' });
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (err) {
    console.error('fetchProduct error:', err);
    return { status: 'error', data: null };
  }
}

export function formatPrice(price) {
  if (!price || isNaN(Number(price))) return 'Liên hệ';
  return new Intl.NumberFormat('vi-VN').format(Number(price)) + 'đ';
}
