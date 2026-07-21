import React from "react";
import { createRoot } from "react-dom/client";
import { App, ZMPRouter, AnimationRoutes, SnackbarProvider } from "zmp-ui";
import { Route } from "react-router-dom";

import "./css/app.scss";

import appConfig from "../app-config.json";
import HomePage from "./pages/index";
import ProductsPage from "./pages/products";
import ProductDetailPage from "./pages/product-detail";
import CartPage from "./pages/cart";
import ContactPage from "./pages/contact";
import BottomNav from "./components/bottom-nav";

if (!window.APP_CONFIG) {
  window.APP_CONFIG = appConfig;
}

function AppRoot() {
  return (
    <App>
      <ZMPRouter>
        <AnimationRoutes>
          <Route path="/" element={<HomePage />} />
          <Route path="/products" element={<ProductsPage />} />
          <Route path="/product/:id" element={<ProductDetailPage />} />
          <Route path="/cart" element={<CartPage />} />
          <Route path="/contact" element={<ContactPage />} />
        </AnimationRoutes>
        <BottomNav />
      </ZMPRouter>
      <SnackbarProvider />
    </App>
  );
}

const root = createRoot(document.getElementById("app"));
root.render(
  <React.StrictMode>
    <AppRoot />
  </React.StrictMode>
);
