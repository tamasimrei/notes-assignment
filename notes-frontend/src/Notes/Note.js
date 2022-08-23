import React from "react"
import {Col, Row} from "react-bootstrap";
import NoteTag from "./NoteTag";

export default function Note(props) {

    const tags = props.note.tags || []

    return (
        <Row>
            <Col>
                <Row className="align-items-baseline pb-1">
                    <Col xs={6} className="fs-4 fw-bold">
                        {props.note.title}
                    </Col>
                    <Col xs={5} className="text-end pe-4 fs-6 fst-italic text-muted">
                        Created {props.note.createdAt}
                    </Col>
                </Row>
                <Row>
                    <Col xs={11} className="fs-6">
                        {props.note.description}
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
