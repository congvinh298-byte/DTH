import React, { useState } from 'react';
import { Page, Header, Box, Text, Input, Select, Button, useSnackbar } from 'zmp-ui';

const { Option } = Select;

const BookingPage = () => {
  const { openSnackbar } = useSnackbar();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    ten: '',
    sdt: '',
    diachi: '',
    dichvu: '',
    yeucau: ''
  });

  const services = [
    { value: 'Vệ sinh máy lạnh', label: 'Vệ sinh máy lạnh' },
    { value: 'Sửa chữa điện lạnh', label: 'Sửa chữa điện lạnh' },
    { value: 'Lắp đặt máy lạnh', label: 'Lắp đặt máy lạnh' },
    { value: 'Sửa điện gia dụng', label: 'Sửa điện gia dụng' },
    { value: 'Lắp máy giặt/lọc nước', label: 'Lắp máy giặt/lọc nước' },
    { value: 'Khác', label: 'Khác (Ghi chú chi tiết)' }
  ];

  const handleChange = (key, value) => {
    setForm(prev => ({ ...prev, [key]: value }));
  };

  const handleSubmit = async () => {
    if (!form.ten || !form.sdt || !form.diachi || !form.dichvu) {
      openSnackbar({ type: 'warning', text: 'Vui lòng điền đủ Tên, SĐT, Địa chỉ và Dịch vụ.' });
      return;
    }

    setLoading(true);
    try {
      // In production, domain should be absolute if hosted on Zalo, e.g., https://dienmayhieu.com/api/dat_lich.php
      const res = await fetch('https://dienmayhieu.com/api/dat_lich.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form)
      });
      const data = await res.json();
      
      if (data.status === 'success') {
        openSnackbar({ type: 'success', text: 'Đặt lịch thành công!' });
        setForm({ ten: '', sdt: '', diachi: '', dichvu: '', yeucau: '' });
      } else {
        openSnackbar({ type: 'error', text: data.msg || 'Có lỗi xảy ra.' });
      }
    } catch (e) {
      openSnackbar({ type: 'error', text: 'Mất kết nối máy chủ.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page className="page-container">
      <Header title="Đặt Lịch Gọi Thợ" className="zmp-bg-primary" textColor="white" />
      
      <Box p={4}>
        <div className="card">
          <Text className="section-title">Thông tin yêu cầu</Text>
          <Box mb={4}>
            <Select
              label="Chọn dịch vụ"
              placeholder="-- Vui lòng chọn --"
              value={form.dichvu}
              onChange={(val) => handleChange('dichvu', val)}
            >
              {services.map(s => <Option key={s.value} value={s.value} title={s.label} />)}
            </Select>
          </Box>
          <Box mb={4}>
            <Input
              type="text"
              label="Tên của bạn"
              placeholder="Ví dụ: Anh Minh"
              value={form.ten}
              onChange={(e) => handleChange('ten', e.target.value)}
            />
          </Box>
          <Box mb={4}>
            <Input
              type="text"
              label="Số điện thoại Zalo"
              placeholder="09xx.xxx.xxx"
              value={form.sdt}
              onChange={(e) => handleChange('sdt', e.target.value)}
            />
          </Box>
          <Box mb={4}>
            <Input
              type="text"
              label="Địa chỉ chi tiết"
              placeholder="Số nhà, đường, khu vực Lấp Vò..."
              value={form.diachi}
              onChange={(e) => handleChange('diachi', e.target.value)}
            />
          </Box>
          <Box mb={4}>
            <Input.TextArea
              label="Mô tả sự cố (Tùy chọn)"
              placeholder="Máy bị lỗi gì, hiện tượng ra sao..."
              value={form.yeucau}
              onChange={(e) => handleChange('yeucau', e.target.value)}
              showCount
              maxLength={200}
            />
          </Box>
          
          <Button fullWidth onClick={handleSubmit} loading={loading} className="btn-accent" size="large" style={{ marginTop: '20px' }}>
            GỬI YÊU CẦU NGAY
          </Button>
        </div>
      </Box>
    </Page>
  );
};

export default BookingPage;
