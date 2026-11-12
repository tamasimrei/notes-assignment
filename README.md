# A Simple Web Application for Notes

Start everything with Docker Compose:

`docker compose up -d`

The committed `.env` file holds defaults that work with this Docker setup, so no extra configuration is needed.
To override a value on your machine (database DSN, CORS origin, app secret), copy `.env.local.sample` to `.env.local` and edit it there; `.env.local` is not committed.

The database schema is created by Doctrine migrations when the `web` container starts. To load sample data:
`docker compose exec web php bin/console doctrine:fixtures:load`

`docker compose logs`

`docker compose down --rmi all -v`
