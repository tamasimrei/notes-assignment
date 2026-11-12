import React from "react"
import {Button, Col, Row} from "react-bootstrap"
import NoteTag from "./NoteTag"

export default function Note(props) {

    const tags = props.note.tags || []

    const formattedDate = (new Date(props.note.createdAt)).toLocaleString()

    return (
        <Row>
            <Col>
                <Row className="align-items-baseline pb-1">
                    <Col xs={6} className="fs-4 fw-bold">
                        {props.note.title}
                    </Col>
                    <Col xs={5} className="text-end pe-4 fs-6 fst-italic text-muted">
                        Created {formattedDate}
                    </Col>
                </Row>
                <Row className="align-items-baseline">
                    <Col xs={9} className="fs-6">
                        {props.note.description}
                    </Col>
                    <Col xs={2} className="text-end pe-4">
                        <Button
                            variant="link"
                            onClick={() => props.onDelete(props.note.id)}
                            className="link-danger pe-0 py-0 border-0 align-baseline"
                        >
                            delete
                        </Button>
                    </Col>
                </Row>
                <Row className="mt-2 fs-6">
                    <Col>
                        {tags.map(tag =>
                            <NoteTag key={tag.id} tag={tag} />
                        )}
                    </Col>
                </Row>
                <Row>
                    <Col xs={11}><hr /></Col>
                </Row>
            </Col>
        </Row>
    )
}
