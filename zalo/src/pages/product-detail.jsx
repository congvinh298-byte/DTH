import React, { useEffect, useState } from "react";
import { Page, Header, Box, Text, Button, Spinner } from "zmp-ui";
import { useParams, useNavigate } from "react-router-dom";

import { getProductById } from "../services/api";

const PLACEHOLDER_IMAGE = "https://via.placeholder.com/600x400?text=San+pham";

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

      {product.image ? (
        <img src={product.image} alt={product.name} className="detail-image" onError={(e) => { e.target.src = PLACEHOLDER_IMAGE; }} />
      ) : (
        <Box className="detail-placeholder">
          <Text size="small">Không có ảnh</Text>
        </Box>
      )}

      <Box className="detail-body">
        {product.category && <Text className="detail-category">{product.category}</Text>}
        <Text className="detail-name">{product.name}</Text>
        <Text className="detail-price">{product.price.toLocaleString("vi-VN")}đ</Text>
        <Text className="detail-desc">
          {product.description || "Liên hệ cửa hàng để biết thêm chi tiết về sản phẩm."}
        </Text>
        {product.stock > 0 && (
          <Text className="detail-stock">✓ Còn hàng ({product.stock} sản phẩm)</Text>
        )}
        <Button fullWidth onClick={() => navigate("/contact")}>
          Liên hệ mua hàng
        </Button>
      </Box>
    </Page>
  );
}

export default ProductDetailPage;
