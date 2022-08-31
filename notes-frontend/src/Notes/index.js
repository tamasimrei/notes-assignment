import React, {useEffect, useMemo, useState} from 'react'
import axios from "axios"
import {Button, Col, Row} from "react-bootstrap"
import LoadingSpinner from "../App/LoadingSpinner"
import Note from "./Note"
import AddNoteModal from "./AddNoteModal"

export default function Notes() {


    // TODO load tags from API
    const tagsAvailable = [
        {"id": 1, "name": "tag 1"},
        {"id": 2, "name": "tag 2"},
        {"id": 3, "name": "tag 3"},
        {"id": 4, "name": "tag 4"},
        {"id": 5, "name": "tag 5"}
    ]



    const [isLoading, setIsLoading] = useState(true)
    const [isAddNoteShown, setAddNoteShown] = useState(false)
    const [notesData, setNotesData] = useState([])

    const httpClient = useMemo(() => {
        return axios.create({
            // TODO store API base url in env
            baseURL: 'http://localhost:8080/api',
            timeout: 20000
        })
    }, [])

    const onAddNoteModalClose = async (newNote) => {

        // TODO implement saving new note


        // FIXME DEBUG
        console.log(newNote)



        setAddNoteShown(false)
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
                        onClick={() => setAddNoteShown(true)}
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
