import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/graphql': {
        target: 'http://localhost/sw-backend/public',
        changeOrigin: true,
        rewrite: path => '/index.php',
      },
    },
  },
});