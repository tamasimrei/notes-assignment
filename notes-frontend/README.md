# Notes frontend

React 19 app (react-bootstrap, axios), built with [Vite](https://vite.dev/).

It runs in the `node` service of the root `docker-compose.yml`, which runs `npm install && npm start` on startup.
Run npm commands inside that container, because `node_modules` holds Linux-specific native binaries:

```sh
docker compose exec node npm start        # dev server on http://localhost:3000
docker compose exec node npm run build    # production build into build/
docker compose exec node npm run preview  # serve the production build
```

## Tests

```sh
docker compose exec node npm test                              # run everything once
docker compose exec node npm run test:watch                    # re-run on change
docker compose exec node npx vitest run src/Notes              # one folder or file
docker compose exec node npx vitest run -t "creates a note"    # by test name
docker compose run --rm --no-deps node npm test                # without the stack running
```

The tests use [Vitest](https://vitest.dev/) with jsdom and [Testing Library](https://testing-library.com/).
They render the whole app (`src/test/renderApp.jsx`) and use it through the DOM, the way a user does: by
button names, texts and form fields. The backend is replaced at the network level by
[MSW](https://mswjs.io/): `mockApi()` in `src/test/server.js` is a small in-memory version of `/api/note`
and `/api/tag`, and it records the requests so a test can check what was sent. Nothing mocks React,
axios or `httpClient.js`, so the tests keep working when those are upgraded or replaced.

- Test files sit next to the feature: `src/Notes/Notes.test.jsx`, `src/Tags/Tags.test.jsx`,
  `src/App/App.test.jsx` (navigation) and `src/Alert/Alert.test.jsx` (auto-hide timing, with fake timers).
- To simulate a failure, override one endpoint after `mockApi()` with `server.use(...)`; `apiError()`
  builds an error body in the backend's `{code, message, errors}` format.
- A request that no handler covers fails the test.
- When the API contract changes, update `src/test/server.js` and the fixtures at the top of the test files.
  The mock describes what the frontend expects from the backend; it is not checked against the real one.
- Vitest has its own config, `vitest.config.js`, because `vite.config.js` uses `public/` as its root.

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
