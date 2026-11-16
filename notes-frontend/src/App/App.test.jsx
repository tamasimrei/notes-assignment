import { describe, expect, it } from 'vitest'
import { screen } from '@testing-library/react'
import { mockApi } from '../test/server'
import { renderApp } from '../test/renderApp'

const seed = {
    notes: [{
        id: 1,
        title: 'Buy milk',
        description: 'Two litres',
        createdAt: '2024-05-01T10:00:00+00:00',
        tags: [],
    }],
    tags: [{ id: 1, name: 'home' }],
}

describe('App shell', () => {
    it('shows the navigation and the notes page at /', async () => {
        mockApi(seed)
        renderApp('/')

        expect(screen.getByRole('link', { name: 'Notes' })).toHaveAttribute('href', '/')
        expect(screen.getByRole('link', { name: 'Tags' })).toHaveAttribute('href', '/tags')
        expect(await screen.findByText('Buy milk')).toBeInTheDocument()
        expect(screen.getByRole('button', { name: 'Add Note' })).toBeInTheDocument()
    })

    it('shows the tags page at /tags', async () => {
        mockApi(seed)
        renderApp('/tags')

        expect(await screen.findByRole('button', { name: 'Add Tag' })).toBeInTheDocument()
        expect(screen.getByText('home')).toBeInTheDocument()
        expect(screen.queryByText('Buy milk')).not.toBeInTheDocument()
    })

    it('navigates between the pages and marks the active link', async () => {
        const { requests } = mockApi(seed)
        const { user } = renderApp('/')
        await screen.findByText('Buy milk')
        expect(screen.getByRole('link', { name: 'Notes' })).toHaveClass('active')

        await user.click(screen.getByRole('link', { name: 'Tags' }))

        expect(await screen.findByRole('button', { name: 'Add Tag' })).toBeInTheDocument()
        expect(window.location.pathname).toBe('/tags')
        expect(screen.getByRole('link', { name: 'Tags' })).toHaveClass('active')
        expect(screen.getByRole('link', { name: 'Notes' })).not.toHaveClass('active')
        expect(screen.queryByText('Buy milk')).not.toBeInTheDocument()

        await user.click(screen.getByRole('link', { name: 'Notes' }))

        expect(await screen.findByText('Buy milk')).toBeInTheDocument()
        expect(window.location.pathname).toBe('/')
        // Each page loads its data again when it is opened.
        expect(requests.map(request => request.path)).toEqual(['/note', '/tag', '/note'])
    })
})
