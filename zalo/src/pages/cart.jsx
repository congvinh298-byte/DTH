import React from "react";
import { Page, Header, Box, Text, Button, List } from "zmp-ui";
import { useNavigate } from "react-router-dom";

const cartItems = [
  { id: 1, name: "Tivi Samsung 55 inch 4K", price: 8990000, qty: 1 },
];

function CartPage() {
  const navigate = useNavigate();
  const total = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);

  return (
    <Page className="page cart-page">
      <Header title="Giỏ hàng" showBackIcon={false} />

      {cartItems.length === 0 ? (
        <Box p={4}>
          <Text className="center">Giỏ hàng của anh/chị đang trống.</Text>
          <Box mt={2}>
            <Button fullWidth variant="primary" onClick={() => navigate("/products")}>
              Tiếp tục mua sắm
            </Button>
          </Box>
        </Box>
      ) : (
        <>
          <List>
            {cartItems.map((item) => (
              <List.Item
                key={item.id}
                title={item.name}
                subTitle={`Số lượng: ${item.qty}`}
                suffix={<Text bold>{(item.price * item.qty).toLocaleString("vi-VN")}đ</Text>}
              />
            ))}
          </List>
          <Box p={2}>
            <Text size="large" bold>
              Tổng: {total.toLocaleString("vi-VN")}đ
            </Text>
          </Box>
          <Box p={2}>
            <Button fullWidth variant="primary" onClick={() => navigate("/contact")}>
              Liên hệ đặt hàng
            </Button>
          </Box>
        </>
      )}
    </Page>
  );
}

export default CartPage;
