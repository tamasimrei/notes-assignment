import React, {useState} from "react"
import {Button, Col, Container, Form, Modal, Row} from "react-bootstrap"

export default function AddNoteModal(props) {

    const emptyNote = {
        title: '',
        description: '',
        tags: []
    }

    const [note, setNote] = useState(emptyNote)
    const [tags, setTags] = useState([])

    const [validated, setValidated] = useState(false)

    function addTag(tagId, tagName) {
        let newTagData = [...tags, {id: tagId, name: tagName}]
        newTagData.sort((a, b) => ('' + a.name).localeCompare(b.name))
        setTags(newTagData)
    }

    function removeTag(tagId) {
        setTags(tags => tags.filter(tag => tag.id !== tagId))
    }

    function handleInputChange(event) {
        const target = event.target
        const inputName = target.name

        if (target.type === "checkbox") {
            if (target.checked) {
                addTag(parseInt(target.dataset.tagId), target.dataset.tagName)
            } else {
                removeTag(parseInt(target.dataset.tagId))
            }
            return
        }

        setNote({
            ...note,
            [inputName]: target.value
        })
    }

    function handleModalClose() {
        setValidated(false)
        setTags([])
        props.handleClose(null)
    }

    function handleFormSubmit(event) {
        event.preventDefault()

        if (event.target.checkValidity() !== true) {
            setValidated(true)
            event.stopPropagation()
            return
        }

        setValidated(false)
        setTags([])

        props.handleClose({
            ...note,
            tags: tags
        })
    }

    return (
        <>
            <Modal
                show={props.show}
                centered
                onSubmit={handleFormSubmit}
                onHide={() => handleModalClose()}
            >
                <Form
                    onSubmit={(e) => e.preventDefault()}
                    noValidate
                    validated={validated}
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
                                        onChange={handleInputChange}
                                        required
                                    />
                                    <Form.Control.Feedback type="invalid" className="fs-6">
                                        This field is required
                                    </Form.Control.Feedback>
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
                                        onChange={handleInputChange}
                                        required
                                        style={{height: "7em"}}
                                    />
                                    <Form.Control.Feedback type="invalid" className="fs-6">
                                        This field is required
                                    </Form.Control.Feedback>
                                </Col>
                            </Row>
                            <Row className="p-2 fs-5">
                                <Col md={4}>
                                    <Form.Label className="fw-bold">
                                        Tags
                                    </Form.Label>
                                </Col>
                                <Col md={8}>
                                    <div
                                        className="px-2 py-1"
                                        style={{height: "5.75em", overflowY: "auto", border: "var(--bs-modal-header-border-width) solid var(--bs-modal-header-border-color)"}}
                                    >
                                        {props.tagsAvailable.map(tag =>
                                            <Form.Check
                                                onChange={handleInputChange}
                                                key={tag.id}
                                                id={"new_note_tag-" + tag.id}
                                                type="checkbox"
                                                label={tag.name}
                                                data-tag-id={tag.id}
                                                data-tag-name={tag.name}
                                            />
                                        )}
                                    </div>
                                </Col>
                            </Row>
                        </Container>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button
                            variant="outline-dark"
                            className="px-5"
                            onClick={handleModalClose}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            variant="outline-dark"
                            className="px-5"
                        >
                            Add
                        </Button>
                    </Modal.Footer>
                </Form>
            </Modal>
        </>
    )
}
