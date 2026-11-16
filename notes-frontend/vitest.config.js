import { defineConfig } from 'vitest/config'
import react from '@vitejs/plugin-react'

// Kept separate from vite.config.js on purpose: that config uses public/ as the Vite root,
// while the tests live in src/ and need the project folder as the root.
export default defineConfig({
  plugins: [react()],
  test: {
    environment: 'jsdom',
    include: ['src/**/*.test.{js,jsx}'],
    setupFiles: ['./src/test/setup.js'],
    // The page and the API share one origin here, because jsdom enforces CORS on
    // XMLHttpRequest (which axios uses) and the mocked responses carry no CORS headers.
    // CORS is the business of the browser and the backend (nelmio_cors), not of these tests.
    environmentOptions: {
      jsdom: { url: 'http://localhost:3000' },
    },
    // Fixed, so the tests don't depend on .env.local or the host environment.
    // Nothing has to listen here: MSW answers every request (see src/test/server.js).
    env: {
      VITE_API_BASE_URL: 'http://localhost:3000/api',
    },
    restoreMocks: true,
  },
})
