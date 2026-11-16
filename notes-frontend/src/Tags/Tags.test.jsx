import { describe, expect, it } from 'vitest'
import { screen, waitFor } from '@testing-library/react'
import { http, HttpResponse } from 'msw'
import { apiError, apiUrl, mockApi, server, writes } from '../test/server'
import { expectInOrder, renderApp } from '../test/renderApp'

const tags = [
    { id: 1, name: 'home' },
    { id: 2, name: 'work' },
]

const tagNameInput = () => screen.findByPlaceholderText('Enter tag name')
const addTagButton = () => screen.getByRole('button', { name: 'Add Tag' })
const deleteButtons = () => screen.getAllByRole('button', { name: 'delete' })

describe('Tags page', () => {
    describe('listing', () => {
        it('shows a spinner, then the tags', async () => {
            mockApi({ tags })
            renderApp('/tags')

            expect(screen.getByRole('status')).toHaveTextContent('Loading...')

            expectInOrder(await screen.findByText('home'), screen.getByText('work'))
            expect(screen.queryByRole('status')).not.toBeInTheDocument()
            expect(deleteButtons()).toHaveLength(2)
            expect(await screen.findByText('Tags loaded')).toBeInTheDocument()
        })

        it('reports an API error and still shows the form', async () => {
            mockApi()
            server.use(http.get(apiUrl('/tag'), () => apiError(500, 'Internal Server Error')))
            renderApp('/tags')

            expect(await screen.findByText('Error loading tags: 500 Internal Server Error')).toBeInTheDocument()
            expect(addTagButton()).toBeInTheDocument()
        })

        it('reports an error response that is not in the API error format', async () => {
            mockApi()
            server.use(http.get(apiUrl('/tag'), () => new HttpResponse('Bad Gateway', { status: 502 })))
            renderApp('/tags')

            expect(await screen.findByText('Error loading tags: Request failed with status code 502')).toBeInTheDocument()
        })

        it('reports a response that is not a list of tags', async () => {
            mockApi()
            server.use(http.get(apiUrl('/tag'), () => HttpResponse.json({ tags: [] })))
            renderApp('/tags')

            expect(await screen.findByText('Error loading tags: No tags received')).toBeInTheDocument()
        })
    })

    describe('adding', () => {
        it('creates a tag, lists it in alphabetical order and clears the input', async () => {
            const { requests } = mockApi({ tags })
            const { user } = renderApp('/tags')

            const input = await tagNameInput()
            await user.type(input, 'office')
            await user.click(addTagButton())

            const created = await screen.findByText('office')
            expectInOrder(screen.getByText('home'), created, screen.getByText('work'))
            expect(writes(requests)).toEqual([{ method: 'POST', path: '/tag', body: { name: 'office' } }])
            expect(input).toHaveValue('')
            expect(await screen.findByText('Tag added')).toBeInTheDocument()
        })

        it('submits with the Enter key', async () => {
            const { requests } = mockApi({ tags })
            const { user } = renderApp('/tags')

            await user.type(await tagNameInput(), 'urgent{Enter}')

            expect(await screen.findByText('urgent')).toBeInTheDocument()
            expect(writes(requests)).toHaveLength(1)
        })

        it('does not submit an empty name', async () => {
            const { requests } = mockApi({ tags })
            const { user } = renderApp('/tags')

            const input = await tagNameInput()
            await user.click(addTagButton())

            expect(input).toBeRequired()
            expect(input).toBeInvalid()
            expect(screen.getByText('Please provide a tag name')).toBeInTheDocument()
            expect(writes(requests)).toEqual([])
        })

        it('reports the error when the API rejects the tag', async () => {
            mockApi({ tags })
            server.use(http.post(apiUrl('/tag'), () => apiError(422, 'Tag Validation Failed', [
                { field: 'name', message: 'This value is already used.' },
            ])))
            const { user } = renderApp('/tags')

            await user.type(await tagNameInput(), 'home')
            await user.click(addTagButton())

            expect(await screen.findByText('Error creating tag: 422 Tag Validation Failed')).toBeInTheDocument()
            expect(screen.getAllByText('home')).toHaveLength(1)
        })

        it('reports a response that does not contain the created tag', async () => {
            mockApi({ tags })
            server.use(http.post(apiUrl('/tag'), () => HttpResponse.json({})))
            const { user } = renderApp('/tags')

            await user.type(await tagNameInput(), 'ghost')
            await user.click(addTagButton())

            expect(await screen.findByText('Error creating tag: Created tag not received')).toBeInTheDocument()
            expect(screen.queryByText('ghost')).not.toBeInTheDocument()
        })
    })

    describe('deleting', () => {
        it('deletes a tag and removes it from the list', async () => {
            const { requests } = mockApi({ tags })
            const { user } = renderApp('/tags')
            await screen.findByText('home')

            await user.click(deleteButtons()[0])

            await waitFor(() => expect(screen.queryByText('home')).not.toBeInTheDocument())
            expect(screen.getByText('work')).toBeInTheDocument()
            expect(writes(requests)).toEqual([{ method: 'DELETE', path: '/tag/1', body: null }])
            expect(await screen.findByText('Tag deleted')).toBeInTheDocument()
        })

        it('keeps the tag and reports the error when deleting fails', async () => {
            mockApi({ tags })
            server.use(http.delete(apiUrl('/tag/:id'), () => apiError(404, 'Tag not found')))
            const { user } = renderApp('/tags')
            await screen.findByText('home')

            await user.click(deleteButtons()[0])

            expect(await screen.findByText('Error deleting tag: 404 Tag not found')).toBeInTheDocument()
            expect(screen.getByText('home')).toBeInTheDocument()
        })

        it('treats a success status other than 204 as an error', async () => {
            mockApi({ tags })
            server.use(http.delete(apiUrl('/tag/:id'), () => HttpResponse.json({})))
            const { user } = renderApp('/tags')
            await screen.findByText('home')

            await user.click(deleteButtons()[0])

            expect(await screen.findByText('Error deleting tag: Unexpected HTTP status received: 200')).toBeInTheDocument()
            expect(screen.getByText('home')).toBeInTheDocument()
        })
    })
})
