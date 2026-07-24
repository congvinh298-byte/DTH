import React, { useState } from 'react';
import { thoLogin } from '../../services/thoApi';
import './tho.scss';

export default function ThoLogin({ onSuccess, onBackToHome }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!username || !password) {
      setError('Vui lòng nhập tài khoản và mật khẩu');
      return;
    }

    setLoading(true);
    setError('');

    const res = await thoLogin(username, password);
    setLoading(false);

    if (res.status === 'success') {
      onSuccess(res.user);
    } else {
      setError(res.msg || 'Đăng nhập thất bại');
    }
  };

  return (
    <div className="tho-portal">
      <div className="tho-header">
        <div className="brand">
          <div className="logo-icon">🛠</div>
          <div>
            <div className="brand-title">Điện Máy Hiếu</div>
            <div className="brand-sub">Cổng Dành Cho Thợ Kỹ Thuật</div>
          </div>
        </div>
        {onBackToHome && (
          <button
            onClick={onBackToHome}
            style={{
              background: 'none',
              border: 'none',
              color: '#38bdf8',
              fontSize: '13px',
              cursor: 'pointer',
              fontWeight: '700',
            }}
          >
            ← Cổng Khách
          </button>
        )}
      </div>

      <div className="login-card">
        <div className="login-title">🔐 Đăng nhập Thợ</div>
        <div className="login-desc">Nhập tài khoản được cấp bởi Admin để bắt đầu làm việc</div>

        {error && (
          <div
            style={{
              background: 'rgba(239, 68, 68, 0.15)',
              border: '1px solid rgba(239, 68, 68, 0.3)',
              color: '#fca5a5',
              padding: '10px 14px',
              borderRadius: '8px',
              fontSize: '13px',
              marginBottom: '16px',
            }}
          >
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Tài khoản / Số điện thoại</label>
            <input
              type="text"
              placeholder="Nhập tên đăng nhập thợ"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label>Mật khẩu</label>
            <input
              type="password"
              placeholder="Nhập mật khẩu"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          <button type="submit" className="btn-login" disabled={loading}>
            {loading ? 'Đang xử lý...' : 'ĐĂNG NHẬP THỢ'}
          </button>
        </form>

        <p style={{ textAlign: 'center', color: '#64748b', fontSize: '12px', marginTop: '20px' }}>
          Chưa có tài khoản? Liên hệ Quản trị viên để được cấp mật khẩu.
        </p>
      </div>
    </div>
  );
}
