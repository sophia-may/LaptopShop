import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost/LaptopWeb/backend/index.php',
        changeOrigin: true,
      },
      '/uploads': {
        target: 'http://localhost/LaptopWeb/backend',
        changeOrigin: true,
      }
    }
  }
})
