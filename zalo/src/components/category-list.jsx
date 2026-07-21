import React from "react";
import { Box, Button } from "zmp-ui";

const categories = [
  { id: "all", name: "Tất cả" },
  { id: "tivi", name: "Tivi" },
  { id: "tu-lanh", name: "Tủ lạnh" },
  { id: "may-giat", name: "Máy giặt" },
  { id: "dieu-hoa", name: "Điều hòa" },
  { id: "may-loc-nuoc", name: "Máy lọc nước" },
];

function CategoryList({ active, onChange }) {
  return (
    <Box className="category-list" flex p={2}>
      {categories.map((cat) => (
        <Button
          key={cat.id}
          size="small"
          variant={active === cat.id ? "primary" : "secondary"}
          onClick={() => onChange(cat.id)}
          className="category-item"
        >
          {cat.name}
        </Button>
      ))}
    </Box>
  );
}

export default CategoryList;
