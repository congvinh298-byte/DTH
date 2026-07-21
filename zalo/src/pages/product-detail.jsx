import React, { useEffect, useState } from "react";
import { Page, Header, Box, Text, Button, Spinner } from "zmp-ui";
import { useParams, useNavigate } from "react-router-dom";

import { getProductById } from "../services/api";

function ProductDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    setLoading(true);
    setError(null);
    getProductById(id)
      .then((data) => {
        if (!data) {
          setError("Sản phẩm không tồn tại.");
        } else {
          setProduct(data);
        }
        setLoading(false);
      })
      .catch((err) => {
        console.error("[DMH] getProductById error:", err);
        setError("Không tải được sản phẩm.");
        setLoading(false);
      });
  }, [id]);

  if (loading) {
    return (
      <Page className="page">
        <Header title="Đang tải..." />
        <Box className="center" p={4} flex justifyContent="center">
          <Spinner />
        </Box>
      </Page>
    );
  }

  if (error) {
    return (
      <Page className="page">
        <Header title="Lỗi" />
        <Box p={4}>
          <Text className="center error-text">{error}</Text>
          <Box mt={2}>
            <Button fullWidth onClick={() => navigate("/products")}>Quay lại</Button>
          </Box>
        </Box>
      </Page>
    );
  }

  return (
    <Page className="page product-detail-page">
      <Header title={product.name} />
      <Box p={2}>
        {product.image ? (
          <img src={product.image} alt={product.name} className="detail-image" />
        ) : (
          <Box className="detail-image-placeholder" p={4} flex justifyContent="center" alignItems="center">
            <Text size="small">Không có ảnh</Text>
          </Box>
        )}
      </Box>
      <Box p={2}>
        <Text size="xLarge" bold>{product.name}</Text>
        <Text className="detail-price" size="large" bold>
          {product.price.toLocaleString("vi-VN")}đ
        </Text>
        {product.category && (
          <Text size="small" className="detail-category">Danh mục: {product.category}</Text>
        )}
        <Text className="detail-desc" size="small">
          {product.description || "Liên hệ cửa hàng để biết thêm chi tiết về sản phẩm."}
        </Text>
      </Box>
      <Box p={2}>
        <Button variant="primary" fullWidth onClick={() => navigate("/contact")}>
          Liên hệ mua hàng
        </Button>
      </Box>
    </Page>
  );
}

export default ProductDetailPage;
