const API_BASE = "https://dienmayhieu.com/api";

async function fetchJson(url, options = {}) {
  const res = await fetch(url, {
    ...options,
    headers: {
      "Accept": "application/json",
      ...options.headers,
    },
  });

  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`);
  }

  return res.json();
}

export async function getFeaturedProducts() {
  const data = await fetchJson(`${API_BASE}/featured-products.php`);
  return Array.isArray(data) ? data : data.data || [];
}

export async function getProducts(category = "", search = "") {
  const params = new URLSearchParams();
  if (category && category !== "all") params.append("category", category);
  if (search) params.append("search", search);
  const data = await fetchJson(`${API_BASE}/miniapp-products.php?${params.toString()}`);
  return data.data || [];
}

export async function getProductById(id) {
  const data = await fetchJson(`${API_BASE}/miniapp-product.php?id=${id}`);
  return data.data || null;
}
