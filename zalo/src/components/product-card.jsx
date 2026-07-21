import React from "react";
import { Box, Text, Button } from "zmp-ui";
import { useNavigate } from "react-router-dom";

const PLACEHOLDER_IMAGE = "https://via.placeholder.com/400x300?text=San+pham";

function ProductCard({ product }) {
  const navigate = useNavigate();
  const imageUrl = product.image || PLACEHOLDER_IMAGE;

  return (
    <Box className="product-card">
      <div className="product-image">
        <img
          src={imageUrl}
          alt={product.name}
          loading="lazy"
          onError={(e) => { e.target.src = PLACEHOLDER_IMAGE; }}
        />
      </div>
      <div className="product-body">
        {product.category && (
          <Text className="product-category">{product.category}</Text>
        )}
        <Text className="product-name">{product.name}</Text>
        <Text className="product-price">{product.price.toLocaleString("vi-VN")}đ</Text>
        <Button
          size="small"
          variant="primary"
          fullWidth
          onClick={() => navigate(`/product/${product.id}`)}
        >
          Xem chi tiết
        </Button>
      </div>
    </Box>
  );
}

export default ProductCard;
