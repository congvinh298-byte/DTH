import React from "react";
import { createRoot } from "react-dom/client";

import "./css/app.scss";

import appConfig from "../app-config.json";
import HomePage from "./pages/index";

if (!window.APP_CONFIG) {
  window.APP_CONFIG = appConfig;
}

const root = createRoot(document.getElementById("app"));
root.render(
  <React.StrictMode>
    <HomePage />
  </React.StrictMode>
);
