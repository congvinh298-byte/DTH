import React, { useEffect, useState } from "react";
import { Page, Header, Box, Text, Spinner, Input, Button } from "zmp-ui";
import { useSearchParams } from "react-router-dom";

import { getProducts } from "../services/api";
import ProductCard from "../components/product-card";

const categories = [
  { id: "all", name: "Tất cả" },
  { id: "Gia dung", name: "Gia dụng" },
  { id: "San pham 3D", name: "In 3D" },
  { id: "Dien may", name: "Điện máy" },
];

function ProductsPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const initialCategory = searchParams.get("category") || "all";

  const [activeCategory, setActiveCategory] = useState(initialCategory);
  const [search, setSearch] = useState("");
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    setLoading(true);
    setError(null);
    getProducts(activeCategory, search)
      .then((data) => {
        setProducts(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("[DMH] getProducts error:", err);
        setError("Không tải được sản phẩm.");
        setLoading(false);
      });
  }, [activeCategory, search]);

  const handleCategoryChange = (id) => {
    setActiveCategory(id);
    const params = {};
    if (id !== "all") params.category = id;
    setSearchParams(params);
  };

  return (
    <Page className="page products-page">
      <Header title="Sản phẩm" showBackIcon={false} />

      <Box p={2}>
        <Input
          placeholder="Tìm sản phẩm..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          clearable
        />
      </Box>

      <Box className="category-chips">
        {categories.map((cat) => (
          <Button
            key={cat.id}
            className={`category-chip ${activeCategory === cat.id ? "active" : ""}`}
            variant="tertiary"
            onClick={() => handleCategoryChange(cat.id)}
          >
            {cat.name}
          </Button>
        ))}
      </Box>

      {loading ? (
        <Box className="center" p={4} flex justifyContent="center">
          <Spinner />
        </Box>
      ) : error ? (
        <Box p={4}>
          <Text className="center error-text">{error}</Text>
        </Box>
      ) : products.length === 0 ? (
        <Box p={4}>
          <Text className="center">Chưa có sản phẩm trong danh mục này.</Text>
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

export default ProductsPage;
