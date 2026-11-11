import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

const projectDir = fileURLToPath(new URL('.', import.meta.url))
const fromProject = (path) => fileURLToPath(new URL(path, import.meta.url))

export default defineConfig(({ command }) => ({
  plugins: [react()],
  resolve: {
    // index.html (in public/) loads /src/index.jsx: map /src/ to the project's src/ folder.
    alias: [{ find: /^\/src\//, replacement: fromProject('./src/') }],
  },
  // Serve and build from public/: index.html, favicon.ico, manifest.json and robots.txt live there.
  root: fromProject('./public'),
  // The dev server already serves every file in the root. A build copies public/ as-is
  // (favicon.ico, manifest.json, robots.txt). A separate public dir in dev would serve
  // index.html untransformed at /index.html.
  publicDir: command === 'build' ? fromProject('./public') : false,
  // Keep these in the project folder; by default Vite resolves them from `root`.
  cacheDir: fromProject('./node_modules/.vite'),
  envDir: projectDir,
  server: {
    host: true, // listen on all interfaces so the Docker port mapping works
    port: 3000,
    strictPort: true,
    fs: {
      allow: [projectDir], // src/ and node_modules/ are outside the root
    },
  },
  preview: {
    port: 3000,
  },
  build: {
    outDir: fromProject('./build'), // same output folder as Create React App, already gitignored
    emptyOutDir: true,
  },
}))
