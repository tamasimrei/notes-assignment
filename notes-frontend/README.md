# Notes frontend

React 18 app (react-bootstrap, axios), built with [Vite](https://vite.dev/).

It runs in the `node` service of the root `docker-compose.yml`, which runs `npm install && npm start` on startup.
Run npm commands inside that container, because `node_modules` holds Linux-specific native binaries:

```sh
docker compose exec node npm start        # dev server on http://localhost:3000
docker compose exec node npm run build    # production build into build/
docker compose exec node npm run preview  # serve the production build
```

The API base URL `http://localhost:8080/api` is hardcoded in `src/Notes/index.jsx` and `src/Tags/index.jsx`.
