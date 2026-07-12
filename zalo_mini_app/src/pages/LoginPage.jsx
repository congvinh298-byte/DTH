import React, { useState } from 'react';
import { Page, Header, Box, Text, Button, Icon, useNavigate } from 'zmp-ui';
import { getPhoneNumber } from 'zmp-sdk/apis';

const LoginPage = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleZaloLogin = async () => {
    setLoading(true);
    setError(null);
    try {
      // Gọi API lấy số điện thoại của Zalo
      getPhoneNumber({
        success: async (data) => {
          let token = data.token;
          // Gửi token này lên Backend PHP (api/zalo_auth.php)
          try {
            const formData = new FormData();
            formData.append('action', 'login_with_phone');
            formData.append('phone', '0901234567'); // Giả lập số điện thoại sau khi giải mã
            formData.append('zalo_id', 'zalo_id_example');
            
            // Note: Cần trỏ đúng URL backend thực tế của Điện Máy Hiếu
            const response = await fetch('http://localhost:8000/api/zalo_auth.php', {
              method: 'POST',
              body: formData,
            });
            const result = await response.json();
            
            if (result.status === 'success') {
              navigate('/'); // Quay về trang chủ
            } else {
              setError(result.msg || 'Đăng nhập thất bại');
            }
          } catch (e) {
            setError('Lỗi kết nối máy chủ');
          } finally {
            setLoading(false);
          }
        },
        fail: (error) => {
          console.log(error);
          setError('Không thể lấy thông tin số điện thoại từ Zalo');
          setLoading(false);
        }
      });
    } catch (e) {
      setLoading(false);
      setError('Đã có lỗi xảy ra');
    }
  };

  return (
    <Page style={{ backgroundColor: '#f8fafc', height: '100vh' }}>
      <Header title="Đăng Nhập" style={{ backgroundColor: '#0f172a', color: '#fff' }} />
      
      <Box p={4} style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', height: '80%' }}>
        <div style={{ width: '80px', height: '80px', background: '#fbbf24', borderRadius: '50%', border: '4px solid #0f172a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '40px', marginBottom: '20px', boxShadow: '4px 4px 0px #0f172a' }}>
          👋
        </div>
        
        <Text size="xxLarge" bold style={{ color: '#0f172a', textTransform: 'uppercase', marginBottom: '8px' }}>
          Chào Mừng
        </Text>
        <Text size="normal" style={{ color: '#64748b', textAlign: 'center', marginBottom: '40px' }}>
          Vui lòng cấp quyền truy cập Số Điện Thoại để tiếp tục đặt lịch gọi thợ hoặc xem điểm.
        </Text>
        
        {error && (
          <Text style={{ color: '#ef4444', marginBottom: '20px', fontWeight: 'bold' }}>{error}</Text>
        )}

        <Button 
          fullWidth 
          size="large" 
          onClick={handleZaloLogin}
          loading={loading}
          style={{ backgroundColor: '#2563eb', color: '#fff', fontWeight: '900', border: '2px solid #0f172a', borderRadius: '12px', boxShadow: '4px 4px 0px #0f172a' }}
        >
          ĐĂNG NHẬP QUA ZALO
        </Button>
      </Box>
    </Page>
  );
};

export default LoginPage;
