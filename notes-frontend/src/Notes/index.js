import React, {useEffect, useMemo, useState} from 'react'
import axios from "axios"
import {Button, Col, Row} from "react-bootstrap"
import LoadingSpinner from "../App/LoadingSpinner"
import Note from "./Note"
import AddNoteModal from "./AddNoteModal"

export default function Notes() {

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
            let tagData = await httpClient.get('/tag')
            if (!tagData || !tagData.data) {
                throw new Error("No Tags received")
            }
            setTagsAvailable(tagData.data)
        } catch(error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)
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
            let response = await httpClient.post('/note', newNote)
            if (!response || !response.data) {
                throw new Error("No created Note returned")
            }

            let noteCreated = response.data
            let updatedNotesData = [...notesData, noteCreated]
            updatedNotesData.sort((a, b) => compareNoteDateStrings(b, a)) // sorting reverse
            setNotesData(updatedNotesData)
        } catch (error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)
        }
    }

    useEffect(() => {
        const getNotesDataAsync = async () => {
            try {
                let notesData = await httpClient.get('/note')
                if (!notesData || !notesData.data) {
                    throw new Error("No Notes received")
                }
                setNotesData(notesData.data)
            } catch(error) {
                // TODO implement error handling
                // see error.response.status
                console.log(error)
                setNotesData([])
            }
        }

        getNotesDataAsync().then(() => setIsLoading(false))
    }, [httpClient])

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
