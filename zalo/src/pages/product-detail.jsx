import React from "react";
import { Page, Header, Box, Text, Button } from "zmp-ui";
import { useParams, useNavigate } from "react-router-dom";

const allProducts = [
  { id: 1, category: "tivi", name: "Tivi Samsung 55 inch 4K", price: 8990000, image: "https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400", description: "Smart Tivi Samsung 4K, màn hình 55 inch, hỗ trợ HDR, điều khiển giọng nói." },
  { id: 2, category: "tu-lanh", name: "Tủ lạnh Toshiba Inverter", price: 7590000, image: "https://images.unsplash.com/photo-1571175443880-49e1d58b2f17?w=400", description: "Tủ lạnh inverter tiết kiệm điện, dung tích 300L, công nghệ làm lạnh đa chiều." },
  { id: 3, category: "may-giat", name: "Máy giặt LG 10kg", price: 6290000, image: "https://images.unsplash.com/photo-1626806775351-538068a21838?w=400", description: "Máy giặt cửa trước 10kg, công nghệ AI DD, giặt hơi nước, inverter tiết kiệm điện." },
  { id: 4, category: "dieu-hoa", name: "Điều hòa Daikin 1.5HP", price: 8290000, image: "https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400", description: "Điều hòa inverter 1.5HP, làm lạnh nhanh, lọc khí, tiết kiệm điện." },
  { id: 5, category: "may-loc-nuoc", name: "Máy lọc nước Kangaroo", price: 3490000, image: "https://images.unsplash.com/photo-1585776245991-cf89dd7fc73a?w=400", description: "Máy lọc nước RO 9 lõi, công suất 10L/giờ, vòi nóng lạnh." },
  { id: 6, category: "tivi", name: "Tivi Sony 65 inch OLED", price: 22900000, image: "https://images.unsplash.com/photo-1593784991095-a205069470b6?w=400", description: "Tivi OLED 65 inch, độ phân giải 4K, Android TV, âm thanh Dolby Atmos." },
];

function ProductDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const product = allProducts.find((p) => String(p.id) === id);

  if (!product) {
    return (
      <Page className="page">
        <Header title="Không tìm thấy" />
        <Box p={4}>
          <Text>Sản phẩm không tồn tại.</Text>
          <Button onClick={() => navigate("/products")}>Quay lại</Button>
        </Box>
      </Page>
    );
  }

  return (
    <Page className="page product-detail-page">
      <Header title={product.name} />
      <Box p={2}>
        <img src={product.image} alt={product.name} className="detail-image" />
      </Box>
      <Box p={2}>
        <Text size="xLarge" bold>{product.name}</Text>
        <Text className="detail-price" size="large" bold>
          {product.price.toLocaleString("vi-VN")}đ
        </Text>
        <Text className="detail-desc" size="small">{product.description}</Text>
      </Box>
      <Box p={2}>
        <Button variant="primary" fullWidth onClick={() => navigate("/cart")}>
          Liên hệ mua hàng
        </Button>
      </Box>
    </Page>
  );
}

export default ProductDetailPage;
