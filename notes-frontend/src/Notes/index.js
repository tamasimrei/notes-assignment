import React, {useEffect, useState} from 'react'
import axios from "axios"
import {Button, Col, Row} from "react-bootstrap"
import LoadingSpinner from "../App/LoadingSpinner"
import Note from "./Note"
import AddNoteModal from "./AddNoteModal";

export default function Notes() {

    const [isLoading, setIsLoading] = useState(true)

    const [isAddNoteShown, setAddNoteShown] = useState(false)

    const [notesData, setNotesData] = useState([])

    const httpClient = axios.create({
        // TODO store API base url in env
        //baseURL: 'http://localhost:3000/test_notes.json',
        baseURL: 'http://localhost:8080/api/note',
        timeout: 20000
    });

    const onAddNoteModalClose = async (saveNote) => {



        // FIXME DEBUG
        console.log(saveNote)



        setAddNoteShown(false)
    }

    useEffect(() => {
        const getNotesDataAsync = async () => {
            try {
                let notesData = await httpClient.get('')
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
    }, [])

    if (isLoading) {
        return (
            <LoadingSpinner show={isLoading} />
        )
    }

    return (
        <>
            <AddNoteModal show={isAddNoteShown} handleClose={onAddNoteModalClose} />
            <Row className="pt-4 pb-5">
                <Col xs={3}>
                    <Button className="px-4" variant="outline-dark" onClick={() => setAddNoteShown(true)}>
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
