import React, {useContext, useEffect, useMemo, useState} from 'react'
import axios from "axios"
import {Button, Col, Row} from "react-bootstrap"
import AlertContext from "../Alert/AlertContext"
import LoadingSpinner from "../App/LoadingSpinner"
import Note from "./Note"
import AddNoteModal from "./AddNoteModal"

export default function Notes() {

    const { addAlert, addHttpErrorAlert } = useContext(AlertContext)
    const [isLoading, setIsLoading] = useState(true)
    const [isAddNoteShown, setAddNoteShown] = useState(false)
    const [notesData, setNotesData] = useState([])
    const [tagsAvailable, setTagsAvailable] = useState([])

    const httpClient = useMemo(() => {
        return axios.create({
            // TODO store API base url in env
            baseURL: 'http://localhost:8080/api',
            timeout: 20000
        })
    }, [])

    const compareNoteDateStrings = (a, b) => {
        let aDate = new Date(a.createdAt)
        let bDate = new Date(b.createdAt)

        if (aDate < bDate) {
            return -1
        }

        if (aDate > bDate) {
            return 1
        }

        return 0
    }

    const showAddNoteModal = async () => {
        setIsLoading(true)
        try {
            let tagsResponse = await httpClient.get('/tag')
            if (!tagsResponse || !tagsResponse.data) {
                throw new Error("No tags received")
            }
            setTagsAvailable(tagsResponse.data)
        } catch(error) {
            addHttpErrorAlert(error, 'Error loading tags')
        }
        setIsLoading(false)
        setAddNoteShown(true)
    }

    const onAddNoteModalClose = async (newNote) => {
        setAddNoteShown(false)

        if (! newNote) {
            return
        }

        try {
            let noteAddedResponse = await httpClient.post('/note', newNote)
            if (!noteAddedResponse || !noteAddedResponse.data) {
                throw new Error("No created note returned")
            }

            let noteCreated = noteAddedResponse.data
            let updatedNotesData = [...notesData, noteCreated]
            updatedNotesData.sort((a, b) => compareNoteDateStrings(b, a)) // note: sorting reverse
            setNotesData(updatedNotesData)
            addAlert('success', 'Note added')
        } catch (error) {
            addHttpErrorAlert(error, 'Error creating note')
        }
    }

    useEffect(() => {
        const getNotesDataAsync = async () => {
            try {
                let notesResponse = await httpClient.get('/note')
                if (!notesResponse || !notesResponse.data) {
                    throw new Error("No notes received")
                }
                setNotesData(notesResponse.data)
                addAlert('info', 'Notes loaded')
            } catch(error) {
                setNotesData([])
                addHttpErrorAlert(error, 'Error loading notes')
            }
        }

        getNotesDataAsync().then(() => setIsLoading(false))
    }, [])

    if (isLoading) {
        return (
            <LoadingSpinner />
        )
    }

    return (
        <>
            <AddNoteModal
                show={isAddNoteShown}
                tagsAvailable={tagsAvailable}
                handleClose={onAddNoteModalClose}
            />
            <Row className="pt-4 pb-5">
                <Col xs={3}>
                    <Button
                        className="px-4"
                        variant="outline-dark"
                        onClick={showAddNoteModal}
                    >
                        Add Note
                    </Button>
                </Col>
            </Row>

            {notesData.map(note =>
                <Note key={note.id} note={note} />
            )}
        </>
    )
}
