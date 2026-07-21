import React from "react";
import { Box, Text, Button } from "zmp-ui";
import { useNavigate } from "react-router-dom";

const PLACEHOLDER_IMAGE = "https://via.placeholder.com/400x300?text=San+pham";

function ProductCard({ product }) {
  const navigate = useNavigate();
  const imageUrl = product.image || PLACEHOLDER_IMAGE;

  return (
    <Box className="product-card" p={2}>
      <div className="product-image">
        <img src={imageUrl} alt={product.name} loading="lazy" onError={(e) => { e.target.src = PLACEHOLDER_IMAGE; }} />
      </div>
      <Text className="product-name" size="small" bold>
        {product.name}
      </Text>
      {product.category && (
        <Text className="product-category" size="xSmall">{product.category}</Text>
      )}
      <Text className="product-price" size="xSmall">
        {product.price.toLocaleString("vi-VN")}đ
      </Text>
      <Button
        size="small"
        variant="primary"
        onClick={() => navigate(`/product/${product.id}`)}
        fullWidth
      >
        Xem chi tiết
      </Button>
    </Box>
  );
}

export default ProductCard;
