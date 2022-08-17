import React, {useState} from "react"
import {Button, Col, Form, Row} from "react-bootstrap"

export default function AddTagForm(props) {

    const [tagName, setTagName] = useState('')

    function handleChange(e) {
        setTagName(e.target.value)
    }

    function handleAddTag(onAddTag) {
        const tag = onAddTag(tagName)
        // FIXME remove console log
        console.log(tag)
        setTagName('')
    }

    return (
        <>
            <Form onSubmit={e => e.preventDefault()}>
                <Row className={"pt-4 pb-5"}>
                    <Col xs={3}>
                        <Form.Control
                            type="text"
                            onChange={handleChange}
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
        </>
    )
}
