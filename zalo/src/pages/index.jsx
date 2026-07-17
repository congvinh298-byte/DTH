import React, { useState, useEffect } from 'react';
import { Page } from 'zmp-ui';

const HomePage = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    // Inject spinner keyframes nếu chưa có
    if (!document.getElementById('dmh-spinner-style')) {
      const style = document.createElement('style');
      style.id = 'dmh-spinner-style';
      style.textContent = `
        @keyframes dmh-spin { to { transform: rotate(360deg); } }
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
      `;
      document.head.appendChild(style);
    }

    // Ẩn loader sau 8 giây dù iframe có load hay chưa
    const timer = setTimeout(() => setLoading(false), 8000);
    return () => clearTimeout(timer);
  }, []);

  return (
    <Page className="page" hideScrollbar style={{ padding: 0, margin: 0, background: '#0f172a', height: '100vh', width: '100vw' }}>
      {loading && (
        <div style={{
          position: 'fixed', inset: 0,
          display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
          background: '#0f172a', color: '#38bdf8', zIndex: 9999
        }}>
          <div style={{ fontSize: 26, fontWeight: 900 }}>⚡ Điện Máy Hiếu</div>
          <div style={{ marginTop: 12, fontSize: 13, color: '#94a3b8' }}>Đang tải hệ sinh thái...</div>
          <div style={{
            marginTop: 24, width: 32, height: 32,
            border: '3px solid rgba(56,189,248,0.2)', borderTopColor: '#38bdf8', borderRadius: '50%',
            animation: 'dmh-spin 1s linear infinite'
          }} />
        </div>
      )}

      {error && !loading && (
        <div style={{
          position: 'fixed', inset: 0,
          display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
          background: '#0f172a', color: '#fff', padding: 30, textAlign: 'center', zIndex: 10000
        }}>
          <div style={{ fontSize: 18 }}>⚠️ Không thể tải trang</div>
          <div style={{ marginTop: 12, fontSize: 14, color: '#94a3b8' }}>
            Vui lòng kiểm tra kết nối hoặc liên hệ hotline.
          </div>
        </div>
      )}

      <iframe
        src="https://dienmayhieu.com/"
        style={{ width: '100vw', height: '100vh', border: 'none', display: 'block', background: '#0f172a' }}
        title="Điện Máy Hiếu"
        allow="geolocation; microphone; camera"
        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads allow-modals allow-top-navigation"
        referrerPolicy="origin"
        loading="eager"
        onLoad={() => setLoading(false)}
        onError={() => { setLoading(false); setError(true); }}
      />
    </Page>
  );
};

export default HomePage;
