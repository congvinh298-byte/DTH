import { defineConfig } from 'vite';
import reactRefresh from '@vitejs/plugin-react';
import zmpVitePlugin from 'zmp-vite-plugin';

export default defineConfig({
  root: './src',
  base: '',
  plugins: [
    reactRefresh(),
    zmpVitePlugin()
  ],
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        index: './src/index.html'
      }
    }
  }
});
