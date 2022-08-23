import React, {useState} from "react"
import {Button, Col, Form, Row} from "react-bootstrap"

export default function AddTagForm(props) {

    const [tagName, setTagName] = useState('')

    function handleAddTag(onAddTag) {
        const tag = onAddTag(tagName)
        // TODO handle data returned by the API
        console.log(tag)
        setTagName('')
    }

    function handleKeyUp(event, onAddTag) {
        if (event.key === 'Enter') {
            handleAddTag(onAddTag)
        }
    }

    return (
        <Form onSubmit={e => e.preventDefault()}>
            <Row className={"pt-4 pb-5"}>
                <Col xs={3}>
                    <Form.Control
                        type="text"
                        onChange={e => setTagName(e.target.value)}
                        onKeyUp={(e) => handleKeyUp(e, props.onAddTag)}
                        value={tagName}
                    />
                </Col>
                <Col>
                    <Button
                        onClick={() => handleAddTag(props.onAddTag)}
                    >
                        Add Tag
                    </Button>
                </Col>
            </Row>
        </Form>
    )
}
