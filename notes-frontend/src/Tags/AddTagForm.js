import React, {useState} from "react"
import {Button, Col, Form, Row} from "react-bootstrap"

export default function AddTagForm(props) {

    const [tagName, setTagName] = useState('')

    const [validated, setValidated] = useState(false);

    function handleSubmit(event, onAddTag) {
        event.preventDefault()

        const form = event.currentTarget
        if (form.checkValidity() !== true) {
            setValidated(true)
            event.stopPropagation()
            return
        }

        const tag = onAddTag(tagName)
        // TODO handle data returned by the API
        console.log(tag)
        setValidated(false)
        setTagName('')
    }

    return (
        <Form onSubmit={e => handleSubmit(e, props.onAddTag)} noValidate validated={validated}>
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
                    <Button type="submit">
                        Add Tag
                    </Button>
                </Col>
            </Row>
        </Form>
    )
}
