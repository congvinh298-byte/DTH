import React, { useEffect } from 'react';
import { Page, Button } from 'zmp-ui';
import { openWebview } from 'zmp-sdk';

const HomePage = () => {
  useEffect(() => {
    // Tự động mở webview website chính ngay khi Mini App khởi động
    openWebview({
      url: 'https://dienmayhieu.com/',
    }).catch(() => {
      // Nếu tự động bị chặn, người dùng sẽ bấm nút bên dưới
    });
  }, []);

  return (
    <Page className="page" hideScrollbar style={{
      display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      height: '100vh', padding: 20, textAlign: 'center', background: '#0f172a', color: '#fff'
    }}>
      <div style={{ fontSize: 48, marginBottom: 16 }}>⚡</div>
      <div style={{ fontSize: 22, fontWeight: 700, marginBottom: 8 }}>Điện Máy Hiếu</div>
      <div style={{ fontSize: 14, color: '#94a3b8', marginBottom: 24 }}>Mini App</div>

      <Button
        variant="primary"
        size="large"
        onClick={() => openWebview({ url: 'https://dienmayhieu.com/' })}
        style={{ background: '#38bdf8', color: '#0f172a', fontWeight: 700 }}
      >
        Mở cửa hàng
      </Button>
    </Page>
  );
};

export default HomePage;
