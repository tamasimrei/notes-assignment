import '@testing-library/jest-dom/vitest'
import { afterAll, afterEach, beforeAll, expect, vi } from 'vitest'
import { cleanup } from '@testing-library/react'
import TimeAgo from 'javascript-time-ago'
import timeLocaleEn from 'javascript-time-ago/locale/en.json'
import { server, unhandledRequests } from './server'

// Same as src/index.jsx: the alert toasts render <ReactTimeAgo>.
TimeAgo.addDefaultLocale(timeLocaleEn)

beforeAll(() => {
    server.listen({
        // A request no handler covers must never reach a real backend. Fail it, and remember
        // it so the test fails too (the app itself would only show an error alert).
        onUnhandledFrame({ frame, defaults }) {
            const request = frame.data?.request
            unhandledRequests.push(request ? `${request.method} ${request.url}` : frame.protocol)
            defaults.error()
        },
    })
})

afterEach(() => {
    cleanup()
    vi.useRealTimers()
    server.resetHandlers()

    const unhandled = unhandledRequests.splice(0)
    expect(unhandled, 'requests without a mock handler').toEqual([])
})

afterAll(() => {
    server.close()
})
