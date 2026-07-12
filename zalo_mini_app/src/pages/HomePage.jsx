import React from 'react';
import { Page, Header, Box, Text, Button, Icon, useNavigate } from 'zmp-ui';
import { openChat, openPhone } from 'zmp-sdk/apis';

const HomePage = () => {
  const navigate = useNavigate();

  const handleOpenChat = () => {
    openChat({
      type: 'oa',
      id: '959293661751808012',
      message: 'Xin chào Điện Máy Hiếu, tôi cần hỗ trợ.'
    });
  };

  const handleCall = () => {
    openPhone({
      phoneNumber: '0979553289'
    });
  };

  return (
    <Page className="page-container">
      <Header title="Điện Máy Hiếu" showBackIcon={false} className="zmp-bg-primary" textColor="white" />
      
      <Box p={4}>
        {/* Banner */}
        <div style={{
          background: 'linear-gradient(135deg, #0ea5e9, #0284c7)',
          borderRadius: '16px',
          padding: '24px 20px',
          color: 'white',
          marginBottom: '20px',
          boxShadow: '0 4px 12px rgba(14, 165, 233, 0.3)'
        }}>
          <h2 style={{ margin: '0 0 8px 0', fontSize: '24px', fontWeight: '900' }}>Gọi Thợ Trọn Gói</h2>
          <p style={{ margin: '0 0 16px 0', fontSize: '14px', opacity: 0.9 }}>15 Phút Có Mặt Tại Lấp Vò</p>
          <Button variant="secondary" onClick={() => navigate('/booking')} style={{ borderRadius: '20px', fontWeight: 'bold' }}>
            Đặt lịch sửa chữa ngay <Icon icon="zi-arrow-right" />
          </Button>
        </div>

        {/* Quick Actions Grid (3 Pillars) */}
        <Text className="section-title">Hệ Sinh Thái Dịch Vụ</Text>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '10px', marginBottom: '24px' }}>
          
          <div className="card" onClick={() => navigate('/booking')} style={{ textAlign: 'center', cursor: 'pointer', padding: '16px 8px' }}>
            <div style={{ background: '#e0f2fe', color: '#0ea5e9', width: '40px', height: '40px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 8px' }}>
              <Icon icon="zi-call" size={20} />
            </div>
            <Text bold size="xSmall">Sửa Chữa</Text>
          </div>

          <div className="card" onClick={() => navigate('/catalog')} style={{ textAlign: 'center', cursor: 'pointer', padding: '16px 8px' }}>
            <div style={{ background: '#fef3c7', color: '#f59e0b', width: '40px', height: '40px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 8px' }}>
              <Icon icon="zi-store" size={20} />
            </div>
            <Text bold size="xSmall">Bán Hàng</Text>
          </div>
          
          <div className="card" onClick={() => navigate('/3dprint')} style={{ textAlign: 'center', cursor: 'pointer', padding: '16px 8px' }}>
            <div style={{ background: '#e0e7ff', color: '#4f46e5', width: '40px', height: '40px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 8px' }}>
              <Icon icon="zi-box" size={20} />
            </div>
            <Text bold size="xSmall">In 3D</Text>
          </div>
          
        </div>

        {/* Contact Section */}
        <Text className="section-title">Hỗ Trợ Nhanh 24/7</Text>
        <div className="card">
          <Text size="small" className="text-muted" style={{ marginBottom: '16px' }}>Đội ngũ CSKH luôn sẵn sàng hỗ trợ bạn qua hệ thống Zalo OA.</Text>
          <div style={{ display: 'flex', gap: '12px' }}>
            <Button fullWidth onClick={handleOpenChat} style={{ background: '#0068ff', color: 'white' }} prefixIcon={<Icon icon="zi-chat" />}>
              Chat Zalo
            </Button>
            <Button fullWidth onClick={handleCall} className="btn-accent" style={{ marginTop: 0 }} prefixIcon={<Icon icon="zi-call" />}>
              Gọi điện
            </Button>
          </div>
        </div>
      </Box>
    </Page>
  );
};

export default HomePage;
