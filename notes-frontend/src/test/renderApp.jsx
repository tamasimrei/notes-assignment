import React from 'react'
import { render } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { expect } from 'vitest'
import App from '../App'
import { AlertProvider } from '../Alert/AlertContext'

/**
 * Renders the whole app the way src/index.jsx does (minus StrictMode), at the given URL.
 * The tests drive it like a user would: through the DOM, with the API mocked by MSW.
 */
export function renderApp(route = '/') {
    window.history.pushState({}, '', route)

    return {
        user: userEvent.setup(),
        ...render(
            <AlertProvider>
                <App />
            </AlertProvider>
        ),
    }
}

/** Asserts that the given elements appear in the document in this order. */
export function expectInOrder(...elements) {
    for (let i = 1; i < elements.length; i++) {
        const position = elements[i - 1].compareDocumentPosition(elements[i])
        expect(
            position & Node.DOCUMENT_POSITION_FOLLOWING,
            `"${elements[i - 1].textContent}" should come before "${elements[i].textContent}"`
        ).toBeTruthy()
    }
}
