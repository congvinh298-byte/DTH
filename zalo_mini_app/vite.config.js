import { defineConfig } from 'vite';
import reactRefresh from '@vitejs/plugin-react';

export default () => {
  return defineConfig({
    base: '',
    plugins: [reactRefresh()]
  });
};
