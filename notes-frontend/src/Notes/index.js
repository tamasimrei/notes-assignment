import React, {useEffect, useState} from 'react'
import axios from "axios"
import {Button, Col, Row} from "react-bootstrap"
import LoadingSpinner from "../App/LoadingSpinner"
import Note from "./Note"

export default function Notes() {

    const [isLoading, setIsLoading] = useState(true)

    const [notesData, setNotesData] = useState([])

    // TODO replace this with the API
    const notesApiUrl = 'http://localhost:3000/test_notes.json'

    useEffect(() => {
        const getNotesDataAsync = async () => {
            try {



                // FIXME DEBUG
                await new Promise(r => setTimeout(r, 1000))



                const notesData = await axios.get(
                    notesApiUrl,
                    {
                        method: "GET",
                        timeout: 2000
                    }
                )

                if (!notesData || !notesData.data) {
                    throw new Error("No Notes data found")
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
            <LoadingSpinner />
        )
    }

    return (
        <>
            <Row className="pt-4 pb-5">
                <Col xs={3}>
                    <Button className="px-4">Add Note</Button>
                </Col>
            </Row>

            {notesData.map(note =>
                <Note key={note.id} note={note} />
            )}
        </>
    )
}
