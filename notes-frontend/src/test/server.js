import { http, HttpResponse } from 'msw'
import { setupServer } from 'msw/node'

/**
 * The backend, mocked at the network level with MSW. The tests don't know about axios or
 * httpClient.js: they only describe the HTTP contract of the Symfony API (/api/note, /api/tag).
 * When the backend contract changes, this file and the fixtures in the tests change with it.
 */

export const server = setupServer()

/** Filled by the onUnhandledRequest callback in setup.js, checked after every test. */
export const unhandledRequests = []

export const apiUrl = (path) => import.meta.env.VITE_API_BASE_URL + path

/** An error response in the backend's format (see JsonApiProblemNormalizer). */
export const apiError = (code, message, errors = []) =>
    HttpResponse.json({ code, message, errors }, { status: code })

/**
 * Installs a small in-memory version of the API, seeded with the given notes and tags.
 * Returns the recorded requests (`{method, path, body}`), so tests can check what was sent.
 * To simulate a failure, override one endpoint afterwards with `server.use(...)`.
 */
export function mockApi({ notes = [], tags = [] } = {}) {
    const db = { notes: [...notes], tags: [...tags] }
    const requests = []
    const nextId = (rows) => Math.max(0, ...rows.map(row => row.id)) + 1

    const record = async (request) => {
        const text = await request.clone().text()
        requests.push({
            method: request.method,
            path: new URL(request.url).pathname.replace(/^\/api/, ''),
            body: text ? JSON.parse(text) : null,
        })
        return requests.at(-1)
    }

    const remove = (rows, name) => async ({ request, params }) => {
        await record(request)
        const index = rows.findIndex(row => row.id === Number(params.id))
        if (index === -1) {
            return apiError(404, `${name} not found`)
        }
        rows.splice(index, 1)
        return new HttpResponse(null, { status: 204 })
    }

    server.use(
        http.get(apiUrl('/note'), async ({ request }) => {
            await record(request)
            return HttpResponse.json(db.notes)
        }),
        http.post(apiUrl('/note'), async ({ request }) => {
            const { body } = await record(request)
            const note = {
                id: nextId(db.notes),
                title: body.title,
                description: body.description,
                createdAt: new Date().toISOString(),
                tags: body.tags ?? [],
            }
            db.notes.push(note)
            return HttpResponse.json(note)
        }),
        http.delete(apiUrl('/note/:id'), remove(db.notes, 'Note')),

        http.get(apiUrl('/tag'), async ({ request }) => {
            await record(request)
            return HttpResponse.json(db.tags)
        }),
        http.post(apiUrl('/tag'), async ({ request }) => {
            const { body } = await record(request)
            const tag = { id: nextId(db.tags), name: body.name }
            db.tags.push(tag)
            return HttpResponse.json(tag)
        }),
        http.delete(apiUrl('/tag/:id'), remove(db.tags, 'Tag')),
    )

    return { requests }
}

/** The recorded requests that change data (everything except GET). */
export const writes = (requests) => requests.filter(request => request.method !== 'GET')
