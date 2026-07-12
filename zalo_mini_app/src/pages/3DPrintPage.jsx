import React from 'react';
import { Page, Header, Box, Text, Button, useNavigate } from 'zmp-ui';
import { openChat } from 'zmp-sdk/apis';

const Print3DPage = () => {
  const navigate = useNavigate();

  const handleRequestDesign = () => {
    openChat({
      type: 'oa',
      id: '959293661751808012',
      message: 'Tôi có nhu cầu thiết kế và in 3D mô hình theo yêu cầu. Vui lòng tư vấn cho tôi.'
    });
  };

  return (
    <Page className="page-container">
      <Header title="Xưởng In 3D" className="zmp-bg-primary" textColor="white" />
      
      <Box p={4}>
        <div className="print-3d-card">
          <div style={{ fontSize: '40px', marginBottom: '12px' }}>🖨️</div>
          <h3>Biến Ý Tưởng Thành Hiện Thực</h3>
          <p>Dịch vụ in 3D công nghệ FDM/SLA độ nét cao. Báo giá minh bạch, giao hàng siêu tốc tại Đồng Tháp.</p>
        </div>

        <Text className="section-title">Bảng Giá Tham Khảo</Text>
        <div className="card" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <div>
            <Text bold>In bản mẫu có sẵn (STL)</Text>
            <Text size="small" className="text-muted">Chỉ việc in, không thiết kế</Text>
          </div>
          <Text bold style={{ color: '#10b981', fontSize: '16px' }}>400đ/g</Text>
        </div>
        <div className="card" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <div>
            <Text bold>Thiết kế & In trọn gói</Text>
            <Text size="small" className="text-muted">Vẽ lại theo ý tưởng hoặc vật thật</Text>
          </div>
          <Text bold style={{ color: '#a855f7', fontSize: '16px' }}>500đ/g</Text>
        </div>

        <Button fullWidth className="btn-accent" size="large" onClick={() => navigate('/booking')} style={{ marginBottom: '12px' }}>
          ĐIỀN FORM BÁO GIÁ NHANH
        </Button>
        <Button fullWidth variant="secondary" onClick={handleRequestDesign}>
          Chat Zalo Tư Vấn Trực Tiếp
        </Button>
      </Box>
    </Page>
  );
};

export default Print3DPage;
