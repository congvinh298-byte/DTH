import React, { useState, useEffect } from "react";
import { Page, Header, Box, Text, Spinner } from "zmp-ui";

import CategoryList from "../components/category-list";
import ProductCard from "../components/product-card";

const allProducts = [
  { id: 1, category: "tivi", name: "Tivi Samsung 55 inch 4K", price: 8990000, image: "https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400" },
  { id: 2, category: "tu-lanh", name: "Tủ lạnh Toshiba Inverter", price: 7590000, image: "https://images.unsplash.com/photo-1571175443880-49e1d58b2f17?w=400" },
  { id: 3, category: "may-giat", name: "Máy giặt LG 10kg", price: 6290000, image: "https://images.unsplash.com/photo-1626806775351-538068a21838?w=400" },
  { id: 4, category: "dieu-hoa", name: "Điều hòa Daikin 1.5HP", price: 8290000, image: "https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400" },
  { id: 5, category: "may-loc-nuoc", name: "Máy lọc nước Kangaroo", price: 3490000, image: "https://images.unsplash.com/photo-1585776245991-cf89dd7fc73a?w=400" },
  { id: 6, category: "tivi", name: "Tivi Sony 65 inch OLED", price: 22900000, image: "https://images.unsplash.com/photo-1593784991095-a205069470b6?w=400" },
];

function ProductsPage() {
  const [activeCategory, setActiveCategory] = useState("all");
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const t = setTimeout(() => setLoading(false), 500);
    return () => clearTimeout(t);
  }, []);

  const filtered = activeCategory === "all"
    ? allProducts
    : allProducts.filter((p) => p.category === activeCategory);

  return (
    <Page className="page products-page">
      <Header title="Sản phẩm" showBackIcon={false} />

      <CategoryList active={activeCategory} onChange={setActiveCategory} />

      {loading ? (
        <Box className="center" p={4} flex justifyContent="center">
          <Spinner />
        </Box>
      ) : filtered.length === 0 ? (
        <Box p={4}>
          <Text className="center">Chưa có sản phẩm trong danh mục này.</Text>
        </Box>
      ) : (
        <Box className="product-grid" p={2} flex flexWrap="wrap">
          {filtered.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </Box>
      )}
    </Page>
  );
}

export default ProductsPage;
