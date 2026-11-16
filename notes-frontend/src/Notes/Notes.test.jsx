import { describe, expect, it } from 'vitest'
import { screen, waitFor, within } from '@testing-library/react'
import { http, HttpResponse } from 'msw'
import { apiError, apiUrl, mockApi, server, writes } from '../test/server'
import { expectInOrder, renderApp } from '../test/renderApp'

const tags = [
    { id: 1, name: 'home' },
    { id: 2, name: 'work' },
]

// Newest first, in the format the Symfony serializer produces.
const notes = [
    {
        id: 2,
        title: 'Quarterly report',
        description: 'Send it to finance',
        createdAt: '2024-05-02T09:30:00+00:00',
        tags: [{ id: 2, name: 'work' }],
    },
    {
        id: 1,
        title: 'Buy milk',
        description: 'Two litres, semi-skimmed',
        createdAt: '2024-05-01T10:00:00+00:00',
        tags: [{ id: 1, name: 'home' }, { id: 2, name: 'work' }],
    },
]

const deleteButtons = () => screen.getAllByRole('button', { name: 'delete' })

async function openAddNoteDialog(user) {
    await user.click(await screen.findByRole('button', { name: 'Add Note' }))
    const dialog = await screen.findByRole('dialog')

    return {
        dialog,
        title: dialog.querySelector('[name="title"]'),
        description: dialog.querySelector('[name="description"]'),
        submit: within(dialog).getByRole('button', { name: 'Add' }),
        cancel: within(dialog).getByRole('button', { name: 'Cancel' }),
    }
}

const expectDialogClosed = () =>
    waitFor(() => expect(screen.queryByRole('dialog')).not.toBeInTheDocument())

describe('Notes page', () => {
    describe('listing', () => {
        it('shows a spinner, then the notes with their details', async () => {
            mockApi({ notes, tags })
            renderApp('/')

            expect(screen.getByRole('status')).toHaveTextContent('Loading...')

            const first = await screen.findByText('Quarterly report')
            const second = screen.getByText('Buy milk')
            expectInOrder(first, second)
            expect(screen.queryByRole('status')).not.toBeInTheDocument()

            expect(screen.getByText('Send it to finance')).toBeInTheDocument()
            expect(screen.getByText('Two litres, semi-skimmed')).toBeInTheDocument()
            for (const note of notes) {
                const created = new Date(note.createdAt).toLocaleString()
                expect(screen.getByText(`Created ${created}`)).toBeInTheDocument()
            }
            expect(screen.getAllByText('work')).toHaveLength(2)
            expect(screen.getAllByText('home')).toHaveLength(1)
            expect(deleteButtons()).toHaveLength(2)

            expect(await screen.findByText('Notes loaded')).toBeInTheDocument()
        })

        it('shows only the Add Note button when there are no notes', async () => {
            mockApi()
            renderApp('/')

            expect(await screen.findByRole('button', { name: 'Add Note' })).toBeInTheDocument()
            expect(screen.queryByRole('button', { name: 'delete' })).not.toBeInTheDocument()
        })

        it('renders a note that has no tags field', async () => {
            mockApi({ notes: [{ id: 5, title: 'Untagged', description: 'Plain', createdAt: notes[0].createdAt }] })
            renderApp('/')

            expect(await screen.findByText('Untagged')).toBeInTheDocument()
        })

        it('reports an API error and stays usable', async () => {
            mockApi()
            server.use(http.get(apiUrl('/note'), () => apiError(500, 'Internal Server Error')))
            renderApp('/')

            expect(await screen.findByText('Error loading notes: 500 Internal Server Error')).toBeInTheDocument()
            expect(screen.getByRole('button', { name: 'Add Note' })).toBeInTheDocument()
        })

        it('reports when the API cannot be reached', async () => {
            mockApi()
            server.use(http.get(apiUrl('/note'), () => HttpResponse.error()))
            renderApp('/')

            expect(await screen.findByText('Error loading notes: Unable to send request')).toBeInTheDocument()
        })
    })

    describe('deleting', () => {
        it('deletes a note and removes it from the list', async () => {
            const { requests } = mockApi({ notes, tags })
            const { user } = renderApp('/')
            await screen.findByText('Quarterly report')

            await user.click(deleteButtons()[0])

            await waitFor(() => expect(screen.queryByText('Quarterly report')).not.toBeInTheDocument())
            expect(screen.getByText('Buy milk')).toBeInTheDocument()
            expect(writes(requests)).toEqual([{ method: 'DELETE', path: '/note/2', body: null }])
            expect(await screen.findByText('Note deleted')).toBeInTheDocument()
        })

        it('keeps the note and reports the error when deleting fails', async () => {
            mockApi({ notes, tags })
            server.use(http.delete(apiUrl('/note/:id'), () => apiError(404, 'Note not found')))
            const { user } = renderApp('/')
            await screen.findByText('Quarterly report')

            await user.click(deleteButtons()[0])

            expect(await screen.findByText('Error deleting note: 404 Note not found')).toBeInTheDocument()
            expect(screen.getByText('Quarterly report')).toBeInTheDocument()
        })
    })

    describe('adding', () => {
        it('offers the available tags in the dialog', async () => {
            mockApi({ notes, tags })
            const { user } = renderApp('/')

            const { dialog, title, description } = await openAddNoteDialog(user)

            expect(within(dialog).getByText('Add Note')).toBeInTheDocument()
            expect(title).toBeRequired()
            expect(description).toBeRequired()
            expect(within(dialog).getByRole('checkbox', { name: 'home' })).not.toBeChecked()
            expect(within(dialog).getByRole('checkbox', { name: 'work' })).not.toBeChecked()
        })

        it('creates a note with the selected tags and shows it first', async () => {
            const { requests } = mockApi({ notes, tags })
            const { user } = renderApp('/')

            const { dialog, title, description, submit } = await openAddNoteDialog(user)
            await user.type(title, 'Call the plumber')
            await user.type(description, 'Kitchen tap is dripping')
            // Selected in reverse order, then one deselected again: tags are sent sorted by name.
            await user.click(within(dialog).getByRole('checkbox', { name: 'work' }))
            await user.click(within(dialog).getByRole('checkbox', { name: 'home' }))
            await user.click(submit)

            await expectDialogClosed()
            expect(writes(requests)).toEqual([{
                method: 'POST',
                path: '/note',
                body: {
                    title: 'Call the plumber',
                    description: 'Kitchen tap is dripping',
                    tags: [{ id: 1, name: 'home' }, { id: 2, name: 'work' }],
                },
            }])

            const created = await screen.findByText('Call the plumber')
            expectInOrder(created, screen.getByText('Quarterly report'), screen.getByText('Buy milk'))
            expect(screen.getByText('Kitchen tap is dripping')).toBeInTheDocument()
            expect(screen.getAllByText('home')).toHaveLength(2)
            expect(await screen.findByText('Note added')).toBeInTheDocument()
        })

        it('does not send a tag that was unchecked again', async () => {
            const { requests } = mockApi({ notes, tags })
            const { user } = renderApp('/')

            const { dialog, title, description, submit } = await openAddNoteDialog(user)
            await user.type(title, 'No tags')
            await user.type(description, 'Changed my mind')
            await user.click(within(dialog).getByRole('checkbox', { name: 'home' }))
            await user.click(within(dialog).getByRole('checkbox', { name: 'home' }))
            await user.click(submit)

            await expectDialogClosed()
            expect(writes(requests)[0].body.tags).toEqual([])
        })

        it('does not submit while required fields are empty', async () => {
            const { requests } = mockApi({ notes, tags })
            const { user } = renderApp('/')

            const { dialog, title, description, submit } = await openAddNoteDialog(user)
            await user.type(title, 'Only a title')
            await user.click(submit)

            expect(dialog).toBeInTheDocument()
            expect(title).toBeValid()
            expect(description).toBeInvalid()
            expect(within(dialog).getAllByText('This field is required')).toHaveLength(2)
            expect(dialog.querySelector('form')).toHaveClass('was-validated')
            expect(writes(requests)).toEqual([])
        })

        it('closes without saving on Cancel', async () => {
            const { requests } = mockApi({ notes, tags })
            const { user } = renderApp('/')

            const { title, description, cancel } = await openAddNoteDialog(user)
            await user.type(title, 'Never saved')
            await user.type(description, 'Discarded')
            await user.click(cancel)

            await expectDialogClosed()
            expect(writes(requests)).toEqual([])
            expect(screen.queryByText('Never saved')).not.toBeInTheDocument()
        })

        it('reports the error when the API rejects the note', async () => {
            mockApi({ notes, tags })
            server.use(http.post(apiUrl('/note'), () => apiError(422, 'Note Validation Failed', [
                { field: 'title', message: 'This value is too long.' },
            ])))
            const { user } = renderApp('/')

            const { title, description, submit } = await openAddNoteDialog(user)
            await user.type(title, 'Rejected note')
            await user.type(description, 'Whatever')
            await user.click(submit)

            expect(await screen.findByText('Error creating note: 422 Note Validation Failed')).toBeInTheDocument()
            await expectDialogClosed()
            expect(screen.queryByText('Rejected note')).not.toBeInTheDocument()
        })

        it('still opens the dialog, without tags, when the tags cannot be loaded', async () => {
            mockApi({ notes, tags })
            server.use(http.get(apiUrl('/tag'), () => apiError(500, 'Internal Server Error')))
            const { user } = renderApp('/')

            const { dialog } = await openAddNoteDialog(user)

            expect(within(dialog).queryByRole('checkbox')).not.toBeInTheDocument()
            expect(await screen.findByText('Error loading tags: 500 Internal Server Error')).toBeInTheDocument()
        })
    })
})
