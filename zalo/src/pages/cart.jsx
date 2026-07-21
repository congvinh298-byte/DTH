import { useEffect, useState } from 'react';
import { cartApi, formatPrice, getDeviceId } from '../services/api';

export default function CartPage({ onBack }) {
  const [items, setItems] = useState([]);
  const [count, setCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [form, setForm] = useState({ name: '', phone: '', address: '', note: '' });
  const [message, setMessage] = useState('');

  async function loadCart() {
    const res = await cartApi('get');
    if (res.status === 'success') {
      setItems(res.data || []);
      setCount(res.count || 0);
    }
    setLoading(false);
  }

  useEffect(() => {
    loadCart();
  }, []);

  const total = items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);

  async function updateQty(id, qty) {
    await cartApi('update', { cart_id: id, qty });
    loadCart();
  }

  async function removeItem(id) {
    await cartApi('remove', { cart_id: id });
    loadCart();
  }

  async function checkout() {
    if (!form.name || !form.phone || !form.address) {
      setMessage('Vui lòng nhập đủ thông tin giao hàng');
      return;
    }
    setMessage('Đang xử lý...');
    const res = await cartApi('checkout', form);
    if (res.status === 'success') {
      setMessage('Đặt hàng thành công! Mã đơn: ' + res.order_id);
      setForm({ name: '', phone: '', address: '', note: '' });
      loadCart();
    } else {
      setMessage(res.msg || 'Đặt hàng thất bại');
    }
  }

  if (loading) return <div className="wrap" style={{ padding: '40px 20px', textAlign: 'center' }}>Đang tải giỏ hàng...</div>;

  return (
    <div className="wrap" style={{ padding: '20px 16px 80px' }}>
      <div className="title" style={{ marginBottom: '24px' }}>
        <button onClick={onBack} className="btn" style={{ marginBottom: '12px', background: 'rgba(0,0,0,0.1)', padding: '8px 14px', fontSize: '13px' }}><i className="fa-solid fa-arrow-left"></i> Tiếp tục mua</button>
        <h2><i className="fa-solid fa-cart-shopping"></i> Giỏ Hàng</h2>
        <span className="muted">{count} sản phẩm</span>
      </div>

      {items.length === 0 ? (
        <div className="empty" style={{ padding: '48px 20px' }}>
          <i className="fa-solid fa-cart-arrow-down"></i>
          Giỏ hàng trống
        </div>
      ) : (
        <>
          <div style={{ display: 'grid', gap: '12px', marginBottom: '24px' }}>
            {items.map(item => (
              <div key={item.id} style={{ display: 'flex', gap: '12px', background: 'var(--panel)', borderRadius: '14px', padding: '12px', border: '1px solid var(--line)' }}>
                <img src={item.image || '/public/assets/no-product.png'} alt={item.name} style={{ width: '80px', height: '80px', objectFit: 'contain', borderRadius: '10px', background: '#f1f5f9' }} />
                <div style={{ flex: 1 }}>
                  <div style={{ fontWeight: 800, fontSize: '14px', marginBottom: '4px' }}>{item.name}</div>
                  <div style={{ color: 'var(--brand-accent)', fontWeight: 900, fontSize: '15px' }}>{formatPrice(item.price)}</div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginTop: '8px' }}>
                    <button onClick={() => updateQty(item.id, item.quantity - 1)} style={{ width: '30px', height: '30px', borderRadius: '50%', border: '1px solid var(--line)', background: '#fff', fontWeight: 'bold' }}>-</button>
                    <span style={{ fontWeight: 700, minWidth: '24px', textAlign: 'center' }}>{item.quantity}</span>
                    <button onClick={() => updateQty(item.id, item.quantity + 1)} style={{ width: '30px', height: '30px', borderRadius: '50%', border: '1px solid var(--line)', background: '#fff', fontWeight: 'bold' }}>+</button>
                    <button onClick={() => removeItem(item.id)} style={{ marginLeft: 'auto', color: 'var(--danger)', background: 'transparent', border: 'none', fontSize: '13px', fontWeight: 700 }}><i className="fa-solid fa-trash"></i> Xóa</button>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div style={{ background: 'var(--panel)', borderRadius: '16px', padding: '16px', border: '1px solid var(--line)', marginBottom: '20px' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '18px', fontWeight: 900, marginBottom: '16px' }}>
              <span>Tổng tiền:</span>
              <span style={{ color: 'var(--danger)' }}>{formatPrice(total)}</span>
            </div>

            <div style={{ display: 'grid', gap: '12px' }}>
              <input placeholder="Họ tên người nhận" value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} />
              <input placeholder="Số điện thoại" inputMode="tel" value={form.phone} onChange={e => setForm({ ...form, phone: e.target.value })} />
              <input placeholder="Địa chỉ giao hàng" value={form.address} onChange={e => setForm({ ...form, address: e.target.value })} />
              <textarea placeholder="Ghi chú (không bắt buộc)" value={form.note} onChange={e => setForm({ ...form, note: e.target.value })} style={{ minHeight: '80px' }} />
            </div>

            {message && <div style={{ marginTop: '12px', padding: '10px', borderRadius: '8px', background: message.includes('thành công') ? '#d1fae5' : '#fee2e2', color: message.includes('thành công') ? '#065f46' : '#991b1b', fontWeight: 700, fontSize: '14px' }}>{message}</div>}

            <button onClick={checkout} className="btn accent" style={{ width: '100%', marginTop: '16px', padding: '14px' }}><i className="fa-solid fa-check"></i> ĐẶT HÀNG NGAY</button>
          </div>
        </>
      )}
    </div>
  );
}
