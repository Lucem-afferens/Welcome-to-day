import { defineConfig } from 'vite'

export default defineConfig({
  root: '.',
  publicDir: 'public',
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    rollupOptions: {
      output: {
        entryFileNames: 'assets/main.js', // ⬅️ Без хеша
      }
    }
  },
  server: {
    port: 3000,
    host: true
  },
}) 