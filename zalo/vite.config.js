import { defineConfig } from "vite";
import zaloMiniApp from "zmp-vite-plugin";
import react from "@vitejs/plugin-react";

export default () => {
  return defineConfig({
    root: "./src",
    base: "",
    plugins: [zaloMiniApp(), react()],
    build: {
      outDir: "../dist",
      emptyOutDir: true,
      assetsInlineLimit: 0,
      polyfillModulePreload: false
    },
    resolve: {
      alias: {
        "@": "/src"
      }
    }
  });
};
