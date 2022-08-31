import React, {useState} from "react"
import {Button, Col, Form, Row} from "react-bootstrap"

export default function AddTagForm(props) {

    const [tagName, setTagName] = useState('')

    const [validated, setValidated] = useState(false)

    function handleSubmit(event) {
        event.preventDefault()

        const form = event.target
        if (form.checkValidity() !== true) {
            setValidated(true)
            event.stopPropagation()
            return
        }

        props.onAddTag(tagName)
        setValidated(false)
        setTagName('')
    }

    return (
        <Form
            onSubmit={handleSubmit}
            noValidate
            validated={validated}
        >
            <Row className="pt-4 pb-5">
                <Col xs={3}>
                    <Form.Control
                        type="text"
                        name="tagName"
                        onChange={e => setTagName(e.target.value)}
                        value={tagName}
                        required
                        placeholder="Enter tag name"
                        onBlur={() => setValidated(false)}
                    />
                    <Form.Control.Feedback type="invalid">
                        Please provide a tag name
                    </Form.Control.Feedback>
                </Col>
                <Col>
                    <Button type="submit" variant="outline-dark">
                        Add Tag
                    </Button>
                </Col>
            </Row>
        </Form>
    )
}
