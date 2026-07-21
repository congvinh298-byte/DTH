import React, { useEffect, useState } from "react";
import { Page, Swiper, Box, Text, Button, Spinner } from "zmp-ui";
import { useNavigate } from "react-router-dom";
import { closeLoading, configAppView } from "zmp-sdk/apis";

import { getFeaturedProducts } from "../services/api";
import ProductCard from "../components/product-card";

const banners = [
  "https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800",
  "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800",
  "https://images.unsplash.com/photo-1574269909862-7e0d70c7c7a5?w=800",
];

const categories = [
  { id: "all", name: "Tất cả" },
  { id: "Tivi", name: "Tivi" },
  { id: "Tu lanh", name: "Tủ lạnh" },
  { id: "May giat", name: "Máy giặt" },
  { id: "Dieu hoa", name: "Điều hòa" },
  { id: "May loc nuoc", name: "Máy lọc nước" },
  { id: "Gia dung", name: "Gia dụng" },
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
      <Box className="banner-swiper" p={2}>
        <Swiper autoplay duration={4000} loop>
          {banners.map((src, idx) => (
            <Swiper.Item key={idx}>
              <img src={src} alt={`banner-${idx}`} className="banner-img" />
            </Swiper.Item>
          ))}
        </Swiper>
      </Box>

      <Box p={2} pb={0}>
        <Text size="large" bold className="section-title">
          Khám phá danh mục
        </Text>
      </Box>
      <Box className="category-list" p={2} flex flexWrap="wrap">
        {categories.map((cat) => (
          <Button
            key={cat.id}
            size="small"
            variant="secondary"
            onClick={() => navigate(`/products?category=${encodeURIComponent(cat.id)}`)}
            className="category-item"
          >
            {cat.name}
          </Button>
        ))}
      </Box>

      <Box p={2} pb={0} flex justifyContent="space-between" alignItems="center">
        <Text size="large" bold className="section-title">
          Sản phẩm nổi bật
        </Text>
        <Text size="xSmall" className="product-count" onClick={() => navigate("/products")}>
          Xem tất cả →
        </Text>
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
        <Box className="product-grid" p={2} flex flexWrap="wrap">
          {products.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </Box>
      )}
    </Page>
  );
}

export default HomePage;
