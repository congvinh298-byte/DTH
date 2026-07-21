import React, { useEffect, useState } from "react";
import { Page, Box, Text, Button, Spinner } from "zmp-ui";
import { useNavigate } from "react-router-dom";
import { closeLoading, configAppView } from "zmp-sdk/apis";

import { getFeaturedProducts } from "../services/api";
import ProductCard from "../components/product-card";

const categories = [
  { id: "all", name: "Tất cả" },
  { id: "Gia dung", name: "Gia dụng" },
  { id: "San pham 3D", name: "In 3D" },
  { id: "Dien may", name: "Điện máy" },
];

function HomePage() {
  const navigate = useNavigate();
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    closeLoading().catch(() => {});
    configAppView({
      headerColor: "#0f172a",
      statusBar: "transparent",
      actionBar: "hide",
      hideBottomNavigationBar: false,
    }).catch(() => {});

    getFeaturedProducts()
      .then((data) => {
        setProducts(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("[DMH] getFeaturedProducts error:", err);
        setError("Không tải được sản phẩm. Vui lòng thử lại.");
        setLoading(false);
      });
  }, []);

  return (
    <Page className="page home-page">
      {/* Hero Section */}
      <Box className="hero-section">
        <Text className="hero-title">Điện Máy Hiếu</Text>
        <Text className="hero-subtitle">
          Hệ sinh thái bán lẻ, dịch vụ sửa chữa & in mô hình 3D trên nền tảng công nghệ số.
        </Text>
        <Box className="hero-actions">
          <Button className="btn-primary" fullWidth onClick={() => navigate("/products")}>
            Khám phá sản phẩm
          </Button>
          <Button className="btn-secondary" fullWidth onClick={() => navigate("/contact")}>
            Đặt lịch gọi thợ
          </Button>
        </Box>
      </Box>

      {/* Categories */}
      <Box className="section-header">
        <Text className="section-title">
          Danh mục <span>nổi bật</span>
        </Text>
      </Box>
      <Box className="category-chips">
        {categories.map((cat) => (
          <Button
            key={cat.id}
            className={`category-chip ${cat.id === "all" ? "" : ""}`}
            variant="tertiary"
            onClick={() => navigate(`/products?category=${encodeURIComponent(cat.id)}`)}
          >
            {cat.name}
          </Button>
        ))}
      </Box>

      {/* Featured Products */}
      <Box className="section-header">
        <Text className="section-title">
          Sản phẩm <span>nổi bật</span>
        </Text>
        <Text className="section-link" onClick={() => navigate("/products")}>Xem tất cả →</Text>
      </Box>

      {loading ? (
        <Box className="center" p={4} flex justifyContent="center">
          <Spinner />
        </Box>
      ) : error ? (
        <Box p={4}>
          <Text className="center error-text">{error}</Text>
          <Box mt={2}>
            <Button fullWidth variant="primary" onClick={() => window.location.reload()}>
              Thử lại
            </Button>
          </Box>
        </Box>
      ) : (
        <Box className="product-grid">
          {products.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </Box>
      )}
    </Page>
  );
}

export default HomePage;
