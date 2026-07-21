import React, { useEffect } from "react";
import { Page, Swiper, Box, Text, Button } from "zmp-ui";
import { useNavigate } from "react-router-dom";
import { closeLoading, configAppView } from "zmp-sdk/apis";

import CategoryList from "../components/category-list";
import ProductCard from "../components/product-card";

const banners = [
  "https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800",
  "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800",
  "https://images.unsplash.com/photo-1574269909862-7e0d70c7c7a5?w=800",
];

const featuredProducts = [
  { id: 1, name: "Tivi Samsung 55 inch 4K", price: 8990000, image: "https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400" },
  { id: 2, name: "Tủ lạnh Toshiba Inverter", price: 7590000, image: "https://images.unsplash.com/photo-1571175443880-49e1d58b2f17?w=400" },
  { id: 3, name: "Máy giặt LG 10kg", price: 6290000, image: "https://images.unsplash.com/photo-1626806775351-538068a21838?w=400" },
  { id: 4, name: "Điều hòa Daikin 1.5HP", price: 8290000, image: "https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400" },
];

function HomePage() {
  const navigate = useNavigate();

  useEffect(() => {
    closeLoading().catch(() => {});
    configAppView({
      headerColor: "#0f172a",
      statusBar: "transparent",
      actionBar: "hide",
      hideBottomNavigationBar: false,
    }).catch(() => {});
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
          Danh mục sản phẩm
        </Text>
      </Box>
      <CategoryList active="all" onChange={(id) => navigate(`/products?category=${id}`)} />

      <Box p={2} pb={0}>
        <Text size="large" bold className="section-title">
          Sản phẩm nổi bật
        </Text>
      </Box>
      <Box className="product-grid" p={2} flex flexWrap="wrap">
        {featuredProducts.map((p) => (
          <ProductCard key={p.id} product={p} />
        ))}
      </Box>

      <Box p={2}>
        <Button fullWidth variant="primary" onClick={() => navigate("/products")}>
          Xem tất cả sản phẩm
        </Button>
      </Box>
    </Page>
  );
}

export default HomePage;
