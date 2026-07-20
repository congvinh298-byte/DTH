import React, { useEffect, useState } from "react";
import {
  openWebview,
  closeLoading,
  getNetworkType,
  onNetworkStatusChange,
  getSystemInfo,
  configAppView
} from "zmp-sdk/apis";

const WEBSITE_URL = "https://dienmayhieu.com/";
const SHOP_NAME = "Điện Máy Hiếu";
const SHOP_ADDRESS = "166, Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp";

function HomePage() {
  const [network, setNetwork] = useState("unknown");
  const [error, setError] = useState(null);

  // Ẩn splash loading và cấu hình view
  useEffect(() => {
    closeLoading().catch(() => {
      // ignore nếu splash không bật
    });

    configAppView({
      headerColor: "#0f172a",
      statusBar: "transparent",
      actionBar: "hide",
      hideBottomNavigationBar: true
    }).catch(() => {
      // ignore nếu API không hỗ trợ
    });
  }, []);

  // Lấy thông tin mạng
  useEffect(() => {
    getNetworkType()
      .then((res) => setNetwork(res.networkType || "unknown"))
      .catch(() => setNetwork("unknown"));

    const unsubscribe = onNetworkStatusChange((res) => {
      setNetwork(res.isConnected ? (res.networkType || "unknown") : "none");
    });

    return () => {
      if (typeof unsubscribe === "function") unsubscribe();
    };
  }, []);

  // Log thông tin hệ thống (dev only)
  useEffect(() => {
    getSystemInfo()
      .then((info) => console.log("[DMH] System info:", info))
      .catch((err) => console.warn("[DMH] getSystemInfo failed:", err));
  }, []);

  const handleOpen = () => {
    if (network === "none") {
      setError("Không có kết nối mạng. Vui lòng kiểm tra lại.");
      return;
    }

    setError(null);

    openWebview({
      url: WEBSITE_URL,
      title: SHOP_NAME
    })
      .then(() => console.log("[DMH] openWebview success"))
      .catch((err) => {
        console.error("[DMH] openWebview error:", err);
        setError("Không thể mở website. Vui lòng thử lại.");
      });
  };

  // Tự động mở webview sau khi load
  useEffect(() => {
    const timer = setTimeout(() => {
      handleOpen();
    }, 800);

    return () => clearTimeout(timer);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [network]);

  return (
    <div className="launch-page">
      <div className="brand">⚡ {SHOP_NAME}</div>
      <div className="address">{SHOP_ADDRESS}</div>
      <button className="open-btn" onClick={handleOpen}>
        Mở website
      </button>
      {error && (
        <div className="error" style={{ marginTop: 16, color: "#f87171", fontSize: 13, textAlign: "center" }}>
          {error}
        </div>
      )}
      <div className="hint">
        Nếu website không tự mở, hãy bấm nút bên trên.
      </div>
    </div>
  );
}

export default HomePage;
