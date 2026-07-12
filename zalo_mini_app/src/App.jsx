import React, { useState } from 'react';
import { Route } from 'react-router-dom';
import { App, ZMPRouter, AnimationRoutes, SnackbarProvider, BottomNavigation, Icon } from 'zmp-ui';

// Import Pages
import HomePage from './pages/HomePage';
import BookingPage from './pages/BookingPage';
import CatalogPage from './pages/CatalogPage';
import Print3DPage from './pages/3DPrintPage';

const MyApp = () => {
  const [activeTab, setActiveTab] = useState('/');

  return (
    <App>
      <SnackbarProvider>
        <ZMPRouter>
          <AnimationRoutes>
            <Route path="/" element={<HomePage />} />
            <Route path="/booking" element={<BookingPage />} />
            <Route path="/catalog" element={<CatalogPage />} />
            <Route path="/3dprint" element={<Print3DPage />} />
          </AnimationRoutes>
          
          <BottomNavigation
            fixed
            activeKey={activeTab}
            onChange={(key) => setActiveTab(key)}
          >
            <BottomNavigation.Item
              key="/"
              label="Trang chủ"
              icon={<Icon icon="zi-home" />}
              activeIcon={<Icon icon="zi-home-solid" />}
              linkTo="/"
            />
            <BottomNavigation.Item
              key="/catalog"
              label="Bán Hàng"
              icon={<Icon icon="zi-store" />}
              activeIcon={<Icon icon="zi-store-solid" />}
              linkTo="/catalog"
            />
            <BottomNavigation.Item
              key="/booking"
              label="Sửa Chữa"
              icon={<Icon icon="zi-call" />}
              activeIcon={<Icon icon="zi-call-solid" />}
              linkTo="/booking"
            />
            <BottomNavigation.Item
              key="/3dprint"
              label="In 3D"
              icon={<Icon icon="zi-box" />}
              activeIcon={<Icon icon="zi-box-solid" />}
              linkTo="/3dprint"
            />
          </BottomNavigation>
        </ZMPRouter>
      </SnackbarProvider>
    </App>
  );
};

export default MyApp;
