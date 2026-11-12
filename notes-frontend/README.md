# Notes frontend

React 18 app (react-bootstrap, axios), built with [Vite](https://vite.dev/).

It runs in the `node` service of the root `docker-compose.yml`, which runs `npm install && npm start` on startup.
Run npm commands inside that container, because `node_modules` holds Linux-specific native binaries:

```sh
docker compose exec node npm start        # dev server on http://localhost:3000
docker compose exec node npm run build    # production build into build/
docker compose exec node npm run preview  # serve the production build
```

## API base URL

The app reads the API base URL from the `VITE_API_BASE_URL` environment variable (used by `src/api/httpClient.js`).
The default, `http://localhost:8080/api`, is set in `.env`. To point the app at another backend:

```sh
echo 'VITE_API_BASE_URL=https://api.example.com/api' > .env.local             # local override, not committed
VITE_API_BASE_URL=https://api.example.com/api docker compose up -d            # dev server through Compose
docker compose exec -e VITE_API_BASE_URL=https://api.example.com/api node npm run build   # production build
```

A real environment variable takes priority over the `.env` files. Vite inlines the value at build time, so a
production build is tied to the URL it was built with. The backend's `CORS_ALLOW_ORIGIN` must allow the origin
the frontend is served from.
