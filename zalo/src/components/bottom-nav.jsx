import React from "react";
import { BottomNavigation, Icon } from "zmp-ui";
import { useNavigate, useLocation } from "react-router-dom";

const tabs = [
  { label: "Trang chủ", icon: "HomePage", path: "/" },
  { label: "Sản phẩm", icon: "Store", path: "/products" },
  { label: "Giỏ hàng", icon: "Cart", path: "/cart" },
  { label: "Liên hệ", icon: "Phone", path: "/contact" },
];

function BottomNav() {
  const navigate = useNavigate();
  const location = useLocation();

  const activeIndex = tabs.findIndex((tab) => tab.path === location.pathname);

  const handleChange = (index) => {
    navigate(tabs[index].path, { replace: true });
  };

  return (
    <BottomNavigation
      fixed
      activeIndex={activeIndex >= 0 ? activeIndex : 0}
      onChange={handleChange}
    >
      {tabs.map((tab) => (
        <BottomNavigation.Item
          key={tab.path}
          label={tab.label}
          icon={<Icon icon={tab.icon} />}
          activeIcon={<Icon icon={tab.icon} />}
        />
      ))}
    </BottomNavigation>
  );
}

export default BottomNav;
