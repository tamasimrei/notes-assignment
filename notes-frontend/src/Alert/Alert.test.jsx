import React, { useContext } from 'react'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { act, fireEvent, render, screen } from '@testing-library/react'
import Alert from './index'
import AlertContext, { AlertProvider } from './AlertContext'

// These tests are about timing (auto-hide delays), so they use fake timers and a small
// harness around the alert context; no API is involved.

function Harness({ httpError }) {
    const { addAlert, addHttpErrorAlert } = useContext(AlertContext)

    return (
        <>
            {['info', 'success', 'warning', 'error'].map(severity =>
                <button key={severity} onClick={() => addAlert(severity, `${severity} message`)}>
                    {severity}
                </button>
            )}
            <button onClick={() => addHttpErrorAlert(httpError, 'Request failed')}>http error</button>
        </>
    )
}

function renderAlerts(httpError = new Error('boom')) {
    render(
        <AlertProvider>
            <Alert />
            <Harness httpError={httpError} />
        </AlertProvider>
    )
}

const trigger = (name) => fireEvent.click(screen.getByRole('button', { name }))
const advance = (ms) => act(() => vi.advanceTimersByTime(ms))

// Time for the toast's fade-out and the provider's delayed clean-up (5 s) to finish.
const CLEAN_UP = 6000

describe('Alerts', () => {
    beforeEach(() => {
        vi.useFakeTimers()
    })

    it('shows the severity as caption with the message', () => {
        renderAlerts()

        trigger('success')

        expect(screen.getByText('Success')).toBeInTheDocument()
        expect(screen.getByText('success message')).toBeInTheDocument()
    })

    it.each([
        ['info', 2000],
        ['success', 3000],
        ['warning', 10000],
    ])('hides an %s alert by itself after %d ms', (severity, delay) => {
        renderAlerts()
        trigger(severity)

        advance(delay - 100)
        expect(screen.getByText(`${severity} message`)).toBeInTheDocument()

        advance(100 + CLEAN_UP)
        expect(screen.queryByText(`${severity} message`)).not.toBeInTheDocument()
    })

    it('keeps an error alert until it is closed', () => {
        renderAlerts()
        trigger('error')

        advance(60000)
        expect(screen.getByText('error message')).toBeInTheDocument()

        fireEvent.click(screen.getByRole('button', { name: 'Close' }))
        advance(CLEAN_UP)
        expect(screen.queryByText('error message')).not.toBeInTheDocument()
    })

    it('shows the newest alert first', () => {
        renderAlerts()

        trigger('error')
        trigger('warning')

        const newest = screen.getByText('warning message')
        const oldest = screen.getByText('error message')
        expect(newest.compareDocumentPosition(oldest) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy()
    })

    describe('HTTP error messages', () => {
        it.each([
            [
                'the code and message of an API error response',
                { message: 'Request failed with status code 404', response: { data: { code: 404, message: 'Note not found' } } },
                'Request failed: 404 Note not found',
            ],
            [
                'the client message when the response is not an API error',
                { message: 'Request failed with status code 502', response: { data: 'Bad Gateway' } },
                'Request failed: Request failed with status code 502',
            ],
            [
                'a fixed text when no response arrived',
                { message: 'Network Error', request: {} },
                'Request failed: Unable to send request',
            ],
            [
                'the message of any other error',
                new Error('boom'),
                'Request failed: boom',
            ],
            [
                'a fallback when nothing is known',
                {},
                'Request failed: Reason unknown',
            ],
        ])('uses %s', (_, httpError, expected) => {
            renderAlerts(httpError)

            trigger('http error')

            expect(screen.getByText('Error')).toBeInTheDocument()
            expect(screen.getByText(expected)).toBeInTheDocument()
        })
    })
})
