import React, {useState} from "react"
import {Button, Col, Container, Form, Modal, Row} from "react-bootstrap"

export default function AddNoteModal(props) {

    const emptyNote = {
        title: '',
        description: '',
        tags: []
    }

    const [note, setNote] = useState(emptyNote)

    function handleChange(event) {
        const target = event.target
        const inputName = target.name

        setNote({
            ...note,
            [inputName]: target.value
        })
    }

    // TODO implement form validation

    return (
        <>
            <Modal
                centered
                show={props.show}
                onHide={() => props.handleClose(null)}
            >
                <Form
                    onSubmit={(e) => e.preventDefault()}
                >
                    <Modal.Header closeButton>
                        <Modal.Title
                            className="text-uppercase fw-bold fs-4"
                        >
                            Add Note
                        </Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Container>
                            <Row className="p-2 fs-5">
                                <Col md={4}>
                                    <Form.Label className="fw-bold">
                                        Title
                                    </Form.Label>
                                </Col>
                                <Col md={8}>
                                    <Form.Control
                                        name="title"
                                        onChange={handleChange}
                                    />
                                </Col>
                            </Row>
                            <Row className="p-2 fs-5">
                                <Col md={4}>
                                    <Form.Label className="fw-bold">
                                        Description
                                    </Form.Label>
                                </Col>
                                <Col md={8}>
                                    <Form.Control
                                        as="textarea"
                                        name="description"
                                        onChange={handleChange}
                                        style={{height: "7em"}}
                                    />
                                </Col>
                            </Row>
                            <Row className="p-2 fs-5">
                                <Col md={4}>
                                    <Form.Label className="fw-bold">
                                        Tags
                                    </Form.Label>
                                </Col>
                                <Col md={8}>
                                    <Form.Select
                                        multiple
                                        size={3}
                                        name="tags"
                                    />
                                </Col>
                            </Row>
                        </Container>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button
                            variant="outline-dark"
                            className="px-5"
                            onClick={() => props.handleClose(null)}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            variant="outline-dark"
                            className="px-5"
                            onClick={() => props.handleClose(note)}
                        >
                            Add
                        </Button>
                    </Modal.Footer>
                </Form>
            </Modal>
        </>
    )
}
