import { useState, useEffect } from 'react';
import HomePage from './pages/index';
import CartPage from './pages/cart';
import ThoLogin from './pages/tho/ThoLogin';
import ThoDashboard from './pages/tho/ThoDashboard';
import { getThoToken, fetchThoProfile } from './services/thoApi';
import './css/app.scss';

function App() {
  const [page, setPage] = useState('home'); // 'home' | 'cart' | 'tho'
  const [thoUser, setThoUser] = useState(null);
  const [checkingAuth, setCheckingAuth] = useState(true);

  useEffect(() => {
    async function checkAuth() {
      const token = getThoToken();
      if (token) {
        const res = await fetchThoProfile();
        if (res.status === 'success' && res.user) {
          setThoUser(res.user);
        }
      }
      setCheckingAuth(false);
    }
    checkAuth();
  }, []);

  // Nếu đang kiểm tra auth token thợ
  if (checkingAuth) {
    return (
      <div style={{ minHeight: '100vh', background: '#0f172a', color: '#94a3b8', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        Đang khởi động ứng dụng...
      </div>
    );
  }

  // Routing Cổng Thợ
  if (page === 'tho') {
    if (thoUser) {
      return (
        <ThoDashboard
          user={thoUser}
          onLogout={() => {
            setThoUser(null);
            setPage('home');
          }}
        />
      );
    }
    return (
      <ThoLogin
        onSuccess={(user) => {
          setThoUser(user);
        }}
        onBackToHome={() => setPage('home')}
      />
    );
  }

  if (page === 'cart') {
    return <CartPage onBack={() => setPage('home')} />;
  }

  return (
    <div style={{ position: 'relative' }}>
      <HomePage onCart={() => setPage('cart')} />

      {/* Button chuyển sang Cổng Thợ ở góc dưới */}
      <div
        style={{
          position: 'fixed',
          bottom: '16px',
          right: '16px',
          zIndex: 99,
        }}
      >
        <button
          onClick={() => setPage('tho')}
          style={{
            background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            color: '#0f172a',
            border: 'none',
            padding: '10px 16px',
            borderRadius: '30px',
            fontWeight: '800',
            fontSize: '13px',
            boxShadow: '0 4px 14px rgba(245, 158, 11, 0.4)',
            cursor: 'pointer',
            display: 'flex',
            alignItems: 'center',
            gap: '6px',
          }}
        >
          🛠 CỔNG THỢ
        </button>
      </div>
    </div>
  );
}

export default App;
