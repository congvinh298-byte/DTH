import React, { useState, useEffect } from 'react';
import {
  fetchThoOrders,
  acceptOrder,
  submitQuote,
  completeOrder,
  uploadNghiemThu,
  checkNewOrders,
  clearThoAuth,
} from '../../services/thoApi';
import './tho.scss';

export default function ThoDashboard({ user, onLogout }) {
  const [tab, setTab] = useState('cho'); // 'cho' | 'cuatoi' | 'done'
  const [orders, setOrders] = useState([]);
  const [counts, setCounts] = useState({ cho: 0, cuatoi: 0 });
  const [loading, setLoading] = useState(false);

  // State cho Báo Giá Phát Sinh Modal
  const [quoteModal, setQuoteModal] = useState({ open: false, orderId: null, mota: '', gia: '' });

  // State cho Nghiệm Thu Modal
  const [nghiemThuModal, setNghiemThuModal] = useState({ open: false, orderId: null, note: '', file: null, preview: '' });

  const loadOrders = async (currentTab = tab) => {
    setLoading(true);
    const res = await fetchThoOrders(currentTab);
    setLoading(false);
    if (res.status === 'success') {
      setOrders(res.orders || []);
      if (res.counts) {
        setCounts(res.counts);
      }
    }
  };

  useEffect(() => {
    loadOrders(tab);
  }, [tab]);

  // Polling 30s kiểm tra đơn mới
  useEffect(() => {
    const timer = setInterval(async () => {
      const res = await checkNewOrders();
      if (res.status === 'success' && typeof res.count === 'number') {
        setCounts((prev) => ({ ...prev, cho: res.count }));
        if (tab === 'cho') {
          loadOrders('cho');
        }
      }
    }, 30000);
    return () => clearInterval(timer);
  }, [tab]);

  const handleAccept = async (id) => {
    if (!window.confirm(`Chốt nhận đơn #${id}?`)) return;
    const res = await acceptOrder(id);
    if (res.status === 'success') {
      alert(res.msg);
      setTab('cuatoi');
    } else {
      alert(res.msg || 'Lỗi khi nhận đơn');
    }
  };

  const handleOpenQuote = (id) => {
    setQuoteModal({ open: true, orderId: id, mota: '', gia: '' });
  };

  const handleSubmitQuote = async () => {
    const { orderId, mota, gia } = quoteModal;
    if (!mota || !gia || Number(gia) <= 0) {
      alert('Vui lòng nhập mô tả và giá phát sinh hợp lệ');
      return;
    }
    const res = await submitQuote(orderId, mota, Number(gia));
    if (res.status === 'success') {
      alert(res.msg);
      setQuoteModal({ open: false, orderId: null, mota: '', gia: '' });
      loadOrders(tab);
    } else {
      alert(res.msg || 'Gửi báo giá thất bại');
    }
  };

  const handleOpenNghiemThu = (id) => {
    setNghiemThuModal({ open: true, orderId: id, note: '', file: null, preview: '' });
  };

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setNghiemThuModal((prev) => ({ ...prev, file, preview: reader.result }));
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSaveNghiemThu = async () => {
    const { orderId, file, note } = nghiemThuModal;
    if (!file) {
      alert('Vui lòng chọn hoặc chụp ảnh nghiệm thu');
      return;
    }
    const res = await uploadNghiemThu(orderId, file, note);
    if (res.status === 'success') {
      alert('Đã tải ảnh nghiệm thu thành công!');
      setNghiemThuModal({ open: false, orderId: null, note: '', file: null, preview: '' });
      loadOrders(tab);
    } else {
      alert(res.msg || 'Upload thất bại');
    }
  };

  const handleComplete = async (id) => {
    if (!window.confirm(`Xác nhận hoàn thành đơn #${id}? Trừ 20.000đ phí nền tảng.`)) return;
    const res = await completeOrder(id);
    if (res.status === 'success') {
      alert(res.msg);
      loadOrders(tab);
    } else {
      alert(res.msg || 'Hoàn thành đơn thất bại');
    }
  };

  const handleLogout = () => {
    clearThoAuth();
    if (onLogout) onLogout();
  };

  return (
    <div className="tho-portal">
      {/* Header */}
      <div className="tho-header">
        <div className="brand">
          <div className="logo-icon">🛠</div>
          <div>
            <div className="brand-title">Cổng Thợ Mini App</div>
            <div className="brand-sub">Số dư: <span style={{ color: user?.money < 0 ? '#ef4444' : '#10b981', fontWeight: '800' }}>{new Intl.NumberFormat('vi-VN').format(user?.money || 0)}đ</span></div>
          </div>
        </div>
        <div className="user-pill">
          <span className="name">{user?.name || user?.username}</span>
          <button className="btn-logout" onClick={handleLogout} title="Đăng xuất">✕</button>
        </div>
      </div>

      {/* Tabs */}
      <div className="tho-tabs">
        <button className={`tab-item ${tab === 'cho' ? 'active' : ''}`} onClick={() => setTab('cho')}>
          Đơn Mới {counts.cho > 0 && <span className="badge">{counts.cho}</span>}
        </button>
        <button className={`tab-item ${tab === 'cuatoi' ? 'active' : ''}`} onClick={() => setTab('cuatoi')}>
          Của Tôi {counts.cuatoi > 0 && <span className="badge">{counts.cuatoi}</span>}
        </button>
        <button className={`tab-item ${tab === 'done' ? 'active' : ''}`} onClick={() => setTab('done')}>
          Lịch Sử
        </button>
      </div>

      {/* Orders */}
      <div className="order-list">
        {loading ? (
          <div style={{ textAlign: 'center', padding: '40px', color: '#64748b' }}>Đang tải danh sách đơn...</div>
        ) : orders.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '40px', color: '#64748b' }}>
            {tab === 'cho' ? 'Hiện chưa có đơn mới nào.' : tab === 'cuatoi' ? 'Bạn chưa nhận đơn nào.' : 'Chưa có lịch sử hoàn thành.'}
          </div>
        ) : (
          orders.map((don) => (
            <div className="order-card" key={don.id}>
              <div className="card-top">
                <span className="order-id">Đơn #{don.id}</span>
                <span className="status-tag">
                  {don.trangthai === 'CHO_XU_LY' ? 'Chờ nhận' : don.trangthai === 'DANG_XU_LY' ? 'Đang xử lý' : 'Hoàn thành'}
                </span>
              </div>

              <div className="info-row"><strong>Dịch vụ:</strong> <span style={{ color: '#f59e0b', fontWeight: '700' }}>{don.dichvu}</span></div>
              <div className="info-row"><strong>Khách hàng:</strong> {don.ten} — <a href={`tel:${don.sdt}`} style={{ color: '#38bdf8' }}>{don.sdt}</a></div>
              <div className="info-row"><strong>Địa chỉ:</strong> {don.diachi}</div>
              {don.yeucau && <div className="info-row"><strong>Yêu cầu:</strong> {don.yeucau}</div>}

              {don.phatsinh_gia > 0 && (
                <div style={{ background: 'rgba(139,92,246,0.1)', border: '1px solid rgba(139,92,246,0.3)', padding: '8px 12px', borderRadius: '8px', margin: '10px 0', fontSize: '12px' }}>
                  <strong>Phát sinh:</strong> {new Intl.NumberFormat('vi-VN').format(don.phatsinh_gia)}đ ({don.phatsinh_mota})
                  <span style={{ marginLeft: '8px', fontWeight: '800', color: don.phatsinh_duyet ? '#10b981' : '#f59e0b' }}>
                    [{don.phatsinh_duyet ? '✔ Đã duyệt' : '⏳ Chờ Admin duyệt'}]
                  </span>
                </div>
              )}

              {don.nghiemthu_anh && (
                <div style={{ marginTop: '8px' }}>
                  <span style={{ fontSize: '12px', color: '#94a3b8' }}>Ảnh nghiệm thu:</span>
                  <br />
                  <img src={don.nghiemthu_anh} alt="nghiemthu" style={{ width: '100px', height: '80px', objectFit: 'cover', borderRadius: '6px', marginTop: '4px' }} />
                </div>
              )}

              <div className="card-actions">
                {don.trangthai === 'CHO_XU_LY' && (
                  <button className="btn-accept" onClick={() => handleAccept(don.id)}>CHỐT NHẬN ĐƠN</button>
                )}
                {don.trangthai === 'DANG_XU_LY' && (
                  <>
                    <button className="btn-quote" onClick={() => handleOpenQuote(don.id)}>BÁO GIÁ</button>
                    <button style={{ background: '#3b82f6', color: '#fff' }} onClick={() => handleOpenNghiemThu(don.id)}>📸 ẢNH</button>
                    <button className="btn-complete" onClick={() => handleComplete(don.id)}>✔ HOÀN THÀNH</button>
                  </>
                )}
              </div>
            </div>
          ))
        )}
      </div>

      {/* Quote Modal */}
      {quoteModal.open && (
        <div className="portal-modal-backdrop">
          <div className="portal-modal-box">
            <h4>📝 Báo Giá Phát Sinh — Đơn #{quoteModal.orderId}</h4>
            <div className="form-group" style={{ marginBottom: '12px' }}>
              <label style={{ fontSize: '12px', color: '#cbd5e1' }}>Mô tả phát sinh</label>
              <textarea
                rows="2"
                placeholder="Vật tư / phụ phí..."
                value={quoteModal.mota}
                onChange={(e) => setQuoteModal({ ...quoteModal, mota: e.target.value })}
                style={{ width: '100%', padding: '8px', background: '#0f172a', border: '1px solid rgba(255,255,255,0.1)', color: '#fff', borderRadius: '6px' }}
              />
            </div>
            <div className="form-group">
              <label style={{ fontSize: '12px', color: '#cbd5e1' }}>Số tiền phát sinh (VND)</label>
              <input
                type="number"
                placeholder="Vd: 150000"
                value={quoteModal.gia}
                onChange={(e) => setQuoteModal({ ...quoteModal, gia: e.target.value })}
                style={{ width: '100%', padding: '8px', background: '#0f172a', border: '1px solid rgba(255,255,255,0.1)', color: '#fff', borderRadius: '6px' }}
              />
            </div>
            <div className="modal-actions">
              <button className="btn-cancel" onClick={() => setQuoteModal({ open: false })}>Hủy</button>
              <button className="btn-submit" onClick={handleSubmitQuote}>Gửi Báo Giá</button>
            </div>
          </div>
        </div>
      )}

      {/* Nghiem Thu Modal */}
      {nghiemThuModal.open && (
        <div className="portal-modal-backdrop">
          <div className="portal-modal-box">
            <h4>📸 Chụp Ảnh Nghiệm Thu — Đơn #{nghiemThuModal.orderId}</h4>
            <input
              type="file"
              accept="image/*"
              capture="environment"
              onChange={handleFileChange}
              style={{ margin: '10px 0' }}
            />
            {nghiemThuModal.preview && (
              <img src={nghiemThuModal.preview} alt="preview" style={{ width: '100%', height: '160px', objectFit: 'cover', borderRadius: '8px', margin: '10px 0' }} />
            )}
            <textarea
              rows="2"
              placeholder="Ghi chú công việc..."
              value={nghiemThuModal.note}
              onChange={(e) => setNghiemThuModal({ ...nghiemThuModal, note: e.target.value })}
              style={{ width: '100%', padding: '8px', background: '#0f172a', border: '1px solid rgba(255,255,255,0.1)', color: '#fff', borderRadius: '6px', margin: '6px 0' }}
            />
            <div className="modal-actions">
              <button className="btn-cancel" onClick={() => setNghiemThuModal({ open: false })}>Hủy</button>
              <button className="btn-submit" onClick={handleSaveNghiemThu}>Lưu Ảnh</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
