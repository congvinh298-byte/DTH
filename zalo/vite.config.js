import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import zmpVitePlugin from 'zmp-vite-plugin';

export default defineConfig({
  root: './src',
  base: './',
  plugins: [
    react(),
    zmpVitePlugin()
  ],
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    polyfillModulePreload: false,
    rollupOptions: {
      input: {
        index: './src/index.html'
      },
      output: {
        entryFileNames: 'assets/[name].[hash].module.js',
        chunkFileNames: 'assets/[name].[hash].module.js'
      }
    }
  }
});
