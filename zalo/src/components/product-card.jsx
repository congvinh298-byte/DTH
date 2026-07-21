import React from "react";
import { Box, Text, Button } from "zmp-ui";
import { useNavigate } from "react-router-dom";

function ProductCard({ product }) {
  const navigate = useNavigate();

  return (
    <Box className="product-card" p={2}>
      <div className="product-image">
        <img src={product.image} alt={product.name} />
      </div>
      <Text className="product-name" size="small" bold>
        {product.name}
      </Text>
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
