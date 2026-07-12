import { defineConfig } from 'vite';
import reactRefresh from '@vitejs/plugin-react';

export default () => {
  return defineConfig({
    root: './src',
    base: '',
    plugins: [reactRefresh()]
  });
};
