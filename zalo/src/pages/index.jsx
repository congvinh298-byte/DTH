import { useEffect, useMemo, useRef, useState } from 'react';
import { fetchProducts, formatPrice } from '../services/api';

const DEFAULT_LOCATION = [10.357422, 105.522124];

const SERVICES = [
  { group: 'Thợ điện lạnh', color: '#38bdf8', items: [
    { name: 'Vệ sinh máy lạnh', price: '150.000đ', note: 'Trọn gói (Không phí ẩn)', emoji: '❄️' },
    { name: 'Lắp đặt máy lạnh 1HP / 1.5HP', price: '400.000đ', note: 'Chưa gồm vật tư phát sinh', emoji: '🛠️' },
    { name: 'Lắp đặt máy lạnh 2HP / 3HP', price: '500.000đ', note: 'Chưa gồm vật tư phát sinh', emoji: '🛠️' },
    { name: 'Máy lạnh âm trần', price: 'Khảo sát', note: 'Khảo sát và báo giá riêng', emoji: '🏢' },
    { name: 'Sửa chữa điện lạnh', price: '200.000đ', note: 'Công thợ + linh kiện công khai', emoji: '🔧' },
  ]},
  { group: 'Thợ tivi', color: '#fbbf24', items: [
    { name: 'Treo tivi (32-43")', price: '150.000đ', note: 'Công thợ + giá khung treo công khai', emoji: '📺' },
    { name: 'Treo tivi (50-55")', price: '200.000đ', note: 'Công thợ + giá khung treo công khai', emoji: '📺' },
    { name: 'Treo tivi (65-75")', price: '300.000đ', note: 'Công thợ + giá khung treo công khai', emoji: '📺' },
  ]},
  { group: 'Thợ máy lọc nước', color: '#10b981', items: [
    { name: 'Lắp máy lọc nước', price: '200.000đ', note: 'Công thợ + phụ kiện', emoji: '💧' },
  ]},
  { group: 'Thợ gia dụng', color: '#f43f5e', items: [
    { name: 'Lắp máy giặt', price: '200.000đ', note: 'Công thợ + phụ kiện', emoji: '🧺' },
    { name: 'Sửa điện gia dụng', price: '100.000đ', note: 'Công thợ kiểm tra sửa chữa', emoji: '🔌' },
  ]},
  { group: 'Thợ điện thoại', color: '#a855f7', items: [
    { name: 'Kiểm tra / sửa điện thoại', price: '100.000đ', note: 'Công thợ + linh kiện nếu có', emoji: '📱' },
  ]},
];

export default function HomePage() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [menuOpen, setMenuOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const [form, setForm] = useState({ name: '', phone: '', address: '', note: '', vat: false, lat: '', lng: '', mapLocation: '' });
  const [modalProduct, setModalProduct] = useState(null);
  const [cartCount, setCartCount] = useState(0);
  const productsRef = useRef(null);

  useEffect(() => {
    fetchProducts({ featured: true }).then(res => {
      if (res?.data) setProducts(res.data);
      setLoading(false);
    });
  }, []);

  const filteredProducts = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return products;
    return products.filter(p =>
      (p.name || '').toLowerCase().includes(q) ||
      (p.category || '').toLowerCase().includes(q)
    );
  }, [products, search]);

  function scrollTo(id) {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  }

  function handleServiceSelect(svc) {
    setSelectedService(svc);
    setForm(f => ({ ...f, note: f.note || `Tôi cần ${svc.name}` }));
  }

  function useGPS() {
    if (!navigator.geolocation) {
      alert('Trình duyệt không hỗ trợ định vị GPS');
      return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      setForm(f => ({ ...f, lat, lng, mapLocation: `${lat},${lng}` }));
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=vi&lat=${lat}&lon=${lng}`)
        .then(r => r.json())
        .then(data => {
          if (data.display_name) setForm(f => ({ ...f, address: data.display_name }));
        })
        .catch(() => {});
    }, () => alert('Không thể lấy vị trí. Vui lòng nhập địa chỉ thủ công.'));
  }

  function clearLocation() {
    setForm(f => ({ ...f, lat: '', lng: '', mapLocation: '', address: '' }));
  }

  async function addToCart(productId) {
    try {
      const res = await fetch('/controller/client/CartAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'add_to_cart', product_id: productId }),
        credentials: 'include'
      });
      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch { data = {}; }
      if (data.status === 'success') {
        updateCartCount();
        alert('Đã thêm vào giỏ hàng!');
      } else {
        alert(data.msg || 'Không thể thêm vào giỏ hàng');
      }
    } catch (e) {
      alert('Lỗi kết nối');
    }
  }

  async function buyNow(productId) {
    await addToCart(productId);
    window.location.href = '/GioHang.php';
  }

  async function updateCartCount() {
    try {
      const res = await fetch('/controller/client/CartAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'cart_count' }),
        credentials: 'include'
      });
      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch { data = {}; }
      setCartCount(data.count || 0);
    } catch (e) {}
  }

  function submitBooking() {
    if (!selectedService) { alert('Vui lòng chọn dịch vụ'); return; }
    if (!form.name || !form.phone || !form.address) {
      alert('Vui lòng nhập đầy đủ tên, số điện thoại và địa chỉ');
      return;
    }
    const payload = new URLSearchParams({
      action: 'booking',
      service_type: selectedService.group,
      selected_service_name: selectedService.name,
      customer_name: form.name,
      phone: form.phone,
      address: form.address,
      issue_description: form.note,
      map_lat: form.lat,
      map_lng: form.lng,
      map_location: form.mapLocation,
      vat_request_booking: form.vat ? '1' : '0'
    });
    fetch('/api/booking.php', { method: 'POST', body: payload })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Đã gửi yêu cầu! Chúng tôi sẽ liên hệ sớm.');
          setForm({ name: '', phone: '', address: '', note: '', vat: false, lat: '', lng: '', mapLocation: '' });
          setSelectedService(null);
        } else {
          alert(data.msg || 'Gửi thất bại');
        }
      })
      .catch(() => alert('Lỗi kết nối. Vui lòng thử lại.'));
  }

  return (
    <>
      <header>
        <div className="wrap head">
          <a className="logo" href="/">
            <img src="/public/assets/logo.png" alt="Logo Điện Máy Hiếu" />
            <div>Điện Máy Hiếu<small>Mua hàng nhanh - Gọi thợ nhanh</small></div>
          </a>

          <form className="search" id="searchForm" onSubmit={e => { e.preventDefault(); scrollTo('products'); }}>
            <input id="searchInput" name="q" type="search" placeholder="Tìm sản phẩm, dịch vụ..." value={search} onChange={e => setSearch(e.target.value)} />
            <button type="submit">Tìm</button>
          </form>

          <div style={{ display: 'flex', gap: '8px', alignItems: 'center', position: 'relative' }}>
            <a className="btn dark" href="/dien-may">Cửa hàng</a>
            <a className="btn dark" href="/in-3d.php">In 3D</a>
            <a className="btn dark" href="/goi-tho.php">Gọi thợ</a>
            <a className="btn dark" href="/tra-cuu-don.php">Tra cứu đơn</a>
            <a className="btn" href="/GioHang.php" style={{ background: 'var(--brand-accent)', color: 'white', position: 'relative', padding: '10px 15px' }}>
              <i className="fa-solid fa-cart-shopping"></i> Giỏ Hàng
              {cartCount > 0 && (
                <span id="cartCountBadge" style={{ position: 'absolute', top: '-8px', right: '-8px', background: '#f43f5e', color: 'white', borderRadius: '50%', width: '22px', height: '22px', fontSize: '12px', fontWeight: 'bold', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 2px 5px rgba(0,0,0,0.3)' }}>{cartCount}</span>
              )}
            </a>

            <button id="topMenuToggle" onClick={() => setMenuOpen(o => !o)} style={{ background: 'transparent', border: 'none', color: 'white', fontSize: '20px', cursor: 'pointer', padding: '6px 10px', borderRadius: '8px' }}>
              <i className="fa-solid fa-bars"></i>
            </button>
            {menuOpen && (
              <div id="topMenuDropdown" style={{ position: 'absolute', right: 0, top: 'calc(100% + 8px)', background: '#0f172a', border: '1px solid rgba(255,255,255,0.15)', borderRadius: '10px', minWidth: '200px', zIndex: 1000, padding: '8px 0', boxShadow: '0 10px 25px rgba(0,0,0,0.3)' }}>
                <a href="/login.php" style={{ display: 'block', padding: '10px 16px', color: 'white', fontWeight: 600, textDecoration: 'none', whiteSpace: 'nowrap' }}><i className="fa-solid fa-sign-in-alt" style={{ width: '22px' }}></i> Đăng nhập / Đăng ký</a>
              </div>
            )}
          </div>
        </div>
      </header>

      <main>
        <div className="wrap storefront fade-in">
          <section className="hero">
            <div className="hero-main">
              <div className="blob"></div>
              <h1>Điện Máy Hiếu</h1>
              <p>Hệ sinh thái bán lẻ, dịch vụ sửa chữa, in mô hình 3D dựa trên nền tảng công nghệ số tư nhân do chính công ty phát hành.</p>
              <div className="hero-actions">
                <button className="btn light" onClick={() => scrollTo('products')}><i className="fa-solid fa-shopping-cart"></i> Khám phá Sản phẩm</button>
                <button className="btn accent" onClick={() => scrollTo('goi-tho')}><i className="fa-solid fa-tools"></i> Đặt lịch Gọi Thợ</button>
                <a className="btn outline" href="/in-3d.php"><i className="fa-solid fa-cube"></i> Dịch vụ In 3D</a>
              </div>
            </div>
          </section>

          <section className="section" id="products" ref={productsRef}>
            <div className="title">
              <h2>Sản phẩm Nổi bật</h2>
              <span className="muted">{filteredProducts.length} sản phẩm</span>
            </div>
            <div className="grid" id="productGrid">
              {loading ? (
                <div className="empty" style={{ gridColumn: '1/-1' }}>Đang tải sản phẩm...</div>
              ) : filteredProducts.length === 0 ? (
                <div className="empty" style={{ gridColumn: '1/-1' }}>Không tìm thấy sản phẩm nào.</div>
              ) : filteredProducts.map(p => (
                <article className="product" key={p.id} data-name={(p.name || '').toLowerCase()} data-category={(p.category || '').toLowerCase()} onClick={() => setModalProduct(p)}>
                  <div className="img">
                    <img src={p.image || '/public/assets/no-product.png'} alt={p.name} onError={e => { e.target.onerror = null; e.target.src = '/public/assets/no-product.png'; }} />
                  </div>
                  <div className="body">
                    <div className="cat">{p.category || 'Sản phẩm'}</div>
                    <div className="name" title={p.name}>{p.name}</div>
                    <div className="price">{formatPrice(p.price)}</div>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '8px', marginTop: '10px' }}>
                      <button onClick={e => { e.stopPropagation(); addToCart(p.id); }} className="btn outline" style={{ width: '100%', padding: '8px', fontSize: '13px', borderColor: 'rgba(255,255,255,0.2)' }}><i className="fa-solid fa-cart-plus"></i> Thêm giỏ</button>
                      <button onClick={e => { e.stopPropagation(); buyNow(p.id); }} className="btn accent" style={{ width: '100%', padding: '8px', fontSize: '13px' }}><i className="fa-solid fa-bolt"></i> Mua Ngay</button>
                    </div>
                  </div>
                </article>
              ))}
            </div>
          </section>

          <section className="section" id="bang-gia" style={{ marginTop: '40px' }}>
            <div className="title" style={{ textAlign: 'center', marginBottom: '40px' }}>
              <h2 style={{ fontSize: '32px', fontWeight: 900, letterSpacing: '-1px', color: 'var(--brand-accent)' }}>BẢNG GIÁ DỊCH VỤ</h2>
              <span className="muted" style={{ display: 'block', marginTop: '8px', fontSize: '16px' }}>Minh bạch - Trọn gói - Không phí ẩn</span>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '16px', padding: '0 16px' }}>
              {SERVICES.flatMap(g => g.items.map(item => (
                <div key={item.name} className="service-card" onClick={() => { handleServiceSelect({ ...item, group: g.group }); scrollTo('goi-tho'); }} style={{ background: 'rgba(15, 23, 42, 0.8)', border: '1px solid rgba(255,255,255,0.1)', borderLeft: `4px solid ${g.color}`, borderRadius: '12px', padding: '16px', display: 'flex', flexDirection: 'column', justifyContent: 'space-between', gap: '12px', transition: 'all 0.2s ease', cursor: 'pointer', boxShadow: '0 4px 6px rgba(0,0,0,0.2)' }}>
                  <div>
                    <div style={{ fontSize: '11px', fontWeight: 800, textTransform: 'uppercase', letterSpacing: '1px', color: g.color, marginBottom: '6px' }}>{g.group}</div>
                    <div style={{ fontSize: '16px', fontWeight: 700, color: '#fff', lineHeight: 1.3 }}>{item.name}</div>
                  </div>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
                    <div style={{ fontSize: '12px', color: '#94a3b8', maxWidth: '60%', lineHeight: 1.3 }}><i className="fa-solid fa-circle-info" style={{ fontSize: '10px', marginRight: '4px', opacity: 0.7 }}></i>{item.note}</div>
                    <div style={{ fontSize: '17px', fontWeight: 900, color: g.color, background: 'rgba(0,0,0,0.3)', padding: '4px 10px', borderRadius: '6px' }}>{item.price}</div>
                  </div>
                </div>
              )))}
            </div>
          </section>

          <section className="section booking-shell" id="goi-tho" style={{ marginTop: '60px', maxWidth: '800px', marginInline: 'auto' }}>
            <div className="title" style={{ textAlign: 'center', marginBottom: '30px' }}>
              <h2 style={{ fontSize: '32px', fontWeight: 900, letterSpacing: '-1px' }}>GỌI THỢ NGAY</h2>
              <span className="muted" style={{ display: 'block', marginTop: '8px' }}>Điền thông tin - 15 phút thợ có mặt</span>
            </div>

            <div id="bookingForm">
              <h3>Thông tin Yêu cầu Dịch vụ</h3>
              <div className="form">
                <div className="field full">
                  <label>1. Bạn cần dịch vụ gì? <span style={{ color: 'var(--brand-accent)' }}>*</span></label>
                  <div id="custom-service-selector">
                    {SERVICES.map(g => (
                      <div key={g.group} style={{ marginBottom: '12px' }}>
                        <div style={{ fontSize: '13px', fontWeight: 800, color: g.color, marginBottom: '6px', textTransform: 'uppercase' }}>{g.group}</div>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
                          {g.items.map(item => {
                            const active = selectedService?.name === item.name && selectedService?.group === g.group;
                            return (
                              <button key={item.name} type="button" className="cute-btn" onClick={() => handleServiceSelect({ ...item, group: g.group })}
                                style={{ background: active ? g.color : 'rgba(255,255,255,0.05)', border: '2px solid rgba(255,255,255,0.1)', color: '#fff', padding: '8px 14px', borderRadius: '20px', fontSize: '14px', fontWeight: 600, cursor: 'pointer', transition: 'all 0.2s', display: 'flex', alignItems: 'center', gap: '6px' }}>
                                <span style={{ fontSize: '16px' }}>{item.emoji}</span> {item.name}
                              </button>
                            );
                          })}
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="field full">
                  <label>Giá tham khảo (Đã gồm VAT)</label>
                  <input className="readonly-price" type="text" readOnly placeholder="Chọn dịch vụ ở trên để xem giá" value={selectedService ? `${selectedService.price} - ${selectedService.note}` : ''} style={{ color: 'var(--brand-accent)', fontWeight: 'bold', background: 'rgba(0,0,0,0.3)' }} />
                </div>

                <div className="field">
                  <label>2. Tên của bạn <span style={{ color: 'var(--brand-accent)' }}>*</span></label>
                  <input name="customer_name" required maxLength="150" placeholder="VD: Anh Minh" value={form.name} onChange={e => setForm(f => ({ ...f, name: e.target.value }))} />
                </div>

                <div className="field">
                  <label>3. Số điện thoại liên hệ <span style={{ color: 'var(--brand-accent)' }}>*</span></label>
                  <input name="phone" type="tel" inputMode="numeric" pattern="[0-9]{8,15}" required maxLength="15" placeholder="09xx.xxx.xxx" value={form.phone} onChange={e => setForm(f => ({ ...f, phone: e.target.value }))} />
                </div>

                <div className="field full">
                  <label>4. Địa chỉ chính xác <span style={{ color: 'var(--brand-accent)' }}>*</span></label>
                  <input name="address" required maxLength="500" placeholder="Số nhà, tên đường, khu vực..." value={form.address} onChange={e => setForm(f => ({ ...f, address: e.target.value }))} />
                  <div className="map-actions">
                    <button className="btn accent" type="button" style={{ padding: '10px 16px', fontSize: '13px' }} onClick={useGPS}><i className="fa-solid fa-location-crosshairs"></i> Lấy tọa độ GPS hiện tại</button>
                    <button className="btn" type="button" style={{ padding: '10px 16px', fontSize: '13px', background: 'rgba(255,255,255,0.1)' }} onClick={clearLocation}><i className="fa-solid fa-eraser"></i> Xóa</button>
                  </div>
                  {form.mapLocation && <div style={{ marginTop: '8px', fontSize: '13px', color: '#94a3b8' }}>Tọa độ: {form.mapLocation}</div>}
                </div>

                <div className="field full">
                  <label>5. Mô tả chi tiết vấn đề <span style={{ color: 'var(--brand-accent)' }}>*</span></label>
                  <textarea name="issue_description" required maxLength="2000" placeholder="Máy bị lỗi gì, hiện tượng như thế nào..." value={form.note} onChange={e => setForm(f => ({ ...f, note: e.target.value }))} />
                </div>

                <div className="field full" style={{ marginTop: '10px' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '10px', background: 'rgba(56, 189, 248, 0.1)', padding: '12px', borderRadius: '8px', border: '1px dashed rgba(56, 189, 248, 0.3)' }}>
                    <input type="checkbox" id="vat_request_booking" style={{ width: '20px', height: '20px', accentColor: '#38bdf8', cursor: 'pointer' }} checked={form.vat} onChange={e => setForm(f => ({ ...f, vat: e.target.checked }))} />
                    <label htmlFor="vat_request_booking" style={{ cursor: 'pointer', fontWeight: 'bold', color: '#38bdf8', userSelect: 'none', margin: 0 }}>Yêu cầu xuất hóa đơn VAT</label>
                  </div>
                </div>
              </div>

              <div style={{ marginTop: '32px', paddingTop: '24px', borderTop: '1px solid rgba(255,255,255,0.1)' }}>
                <p style={{ color: '#94a3b8', fontSize: '14px', marginBottom: '20px' }}><i className="fa-solid fa-circle-info"></i> Giá báo trên web là giá công khai. Nếu có phát sinh vật tư linh kiện, thợ sẽ báo giá chi tiết và xin phép bạn trước khi tiến hành sửa chữa.</p>
                <button id="btnDatLich" className="btn accent" type="button" style={{ width: '100%', padding: '18px', fontSize: '18px', fontWeight: 800, letterSpacing: '0.5px', boxShadow: '0 10px 30px rgba(56,189,248,0.3)' }} onClick={submitBooking}><i className="fa-solid fa-paper-plane"></i> GỬI YÊU CẦU NGAY</button>
              </div>
            </div>
          </section>
        </div>
      </main>

      <footer>
        <div className="wrap">
          <div className="footer-grid">
            <div>
              <h3><i className="fa-solid fa-store"></i> ĐIỆN MÁY HIẾU</h3>
              <p>Hệ sinh thái bán lẻ điện máy, dịch vụ sửa chữa và in mô hình 3D tại Lấp Vò, Đồng Tháp.</p>
              <p><i className="fa-solid fa-location-dot"></i> Lấp Vò, Đồng Tháp</p>
              <p><i className="fa-solid fa-phone"></i> Hotline: <a href="tel:0900000000">0900 000 000</a></p>
            </div>
            <div>
              <h3>DỊCH VỤ</h3>
              <a href="/goi-tho.php">Gọi thợ điện lạnh</a><br/>
              <a href="/goi-tho.php">Sửa chữa điện máy</a><br/>
              <a href="/in-3d.php">In mô hình 3D</a><br/>
              <a href="/dien-may.php">Mua sắm điện máy</a>
            </div>
            <div>
              <h3>HỖ TRỢ</h3>
              <a href="/tra-cuu-don.php">Tra cứu đơn hàng</a><br/>
              <a href="/login.php">Tài khoản</a><br/>
              <a href="/lien-he.php">Liên hệ</a>
            </div>
            <div>
              <h3>KẾT NỐI</h3>
              <div className="social-links">
                <a href="#"><i className="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i className="fa-brands fa-zalo"></i></a>
                <a href="#"><i className="fa-brands fa-youtube"></i></a>
              </div>
            </div>
          </div>
          <div className="footer-bottom">
            <div>© 2025 Điện Máy Hiếu. All rights reserved.</div>
            <div>Designed for Zalo Mini App</div>
          </div>
        </div>
      </footer>

      {modalProduct && (
        <div className="product-modal-overlay active" onClick={() => setModalProduct(null)}>
          <div className="product-modal" onClick={e => e.stopPropagation()}>
            <button className="product-modal-close" onClick={() => setModalProduct(null)}>×</button>
            <div className="product-modal-img">
              <img src={modalProduct.image || '/public/assets/no-product.png'} alt={modalProduct.name} onError={e => { e.target.onerror = null; e.target.src = '/public/assets/no-product.png'; }} />
            </div>
            <div className="product-modal-body">
              <div className="product-modal-cat">{modalProduct.category || 'Sản phẩm'}</div>
              <div className="product-modal-title">{modalProduct.name}</div>
              <div className="product-modal-price">{formatPrice(modalProduct.price)}</div>
              {modalProduct.description && <div className="product-modal-note">{modalProduct.description}</div>}
              <div className="product-modal-actions">
                <button className="btn-addcart" onClick={() => { addToCart(modalProduct.id); }}>Thêm giỏ</button>
                <button className="btn-buynow" onClick={() => { buyNow(modalProduct.id); }}>Mua Ngay</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
