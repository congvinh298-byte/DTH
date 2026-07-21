import React, { useState } from "react";
import { Page, Header, Box, Text, Button, Input, List } from "zmp-ui";
import { openPhone, followOA } from "zmp-sdk/apis";
import { useSnackbar } from "zmp-ui";

function ContactPage() {
  const [phone, setPhone] = useState("");
  const [note, setNote] = useState("");
  const { openSnackbar } = useSnackbar();

  const handleCall = () => {
    openPhone({ phoneNumber: "0941234567" }).catch(() => {
      openSnackbar({ text: "Không thể gọi điện." });
    });
  };

  const handleSubmit = () => {
    if (!phone) {
      openSnackbar({ text: "Vui lòng nhập số điện thoại." });
      return;
    }
    openSnackbar({ text: "Cảm ơn anh/chị! Chúng tôi sẽ liên hệ sớm." });
    setPhone("");
    setNote("");
  };

  return (
    <Page className="page contact-page">
      <Header title="Liên hệ" showBackIcon={false} />

      <Box p={2}>
        <Text size="large" bold>Điện Máy Hiếu</Text>
        <Text size="small">166, Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp</Text>
      </Box>

      <List>
        <List.Item title="Hotline" subTitle="0941.234.567" />
        <List.Item title="Website" subTitle="dienmayhieu.com" />
      </List>

      <Box p={2}>
        <Button variant="primary" fullWidth onClick={handleCall}>
          Gọi ngay
        </Button>
      </Box>

      <Box p={2}>
        <Text size="normal" bold>Để lại thông tin</Text>
        <Input
          label="Số điện thoại"
          type="number"
          placeholder="Nhập số điện thoại"
          value={phone}
          onChange={(e) => setPhone(e.target.value)}
        />
        <Box mt={2}>
          <Input
            label="Nội dung cần tư vấn"
            placeholder="Anh/chị cần mua sản phẩm gì?"
            value={note}
            onChange={(e) => setNote(e.target.value)}
          />
        </Box>
        <Box mt={2}>
          <Button variant="primary" fullWidth onClick={handleSubmit}>
            Gửi yêu cầu
          </Button>
        </Box>
      </Box>
    </Page>
  );
}

export default ContactPage;
