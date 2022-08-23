import React from 'react'
import {Button, Col, Row} from "react-bootstrap";
import Note from "./Note"

export default function Notes() {

    // TODO load this from the API
    const notesData = [
        {
            id: 123,
            createdAt: "11:15 am Aug 12, 2022",
            title: "Note Title Something 3",
            description: "Note 3 Description - Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation commodo consequat.",
            tags: [{id:1, name: "foo"},{id:2, name: "bar"}],
        },
        {
            id: 124,
            createdAt: "11:14 am Aug 12, 2022",
            title: "Note Title Something 2",
            description: "Note 2 Description - Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation commodo consequat.",
            tags: [{id:4, name: "qux"}],
        },
        {
            id: 125,
            createdAt: "11:13 am Aug 12, 2022",
            title: "Note Title Something 1",
            description: "Note 1 Description - Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation commodo consequat.",
            tags: [{id:1, name: "foo"},{id:3, name: "baz"}],
        },
    ]

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
