import { defineConfig } from 'vite';
import reactRefresh from '@vitejs/plugin-react';
import zmpVitePlugin from 'zmp-vite-plugin';

export default () => {
  return defineConfig({
    base: '',
    plugins: [reactRefresh(), zmpVitePlugin()]
  });
};
