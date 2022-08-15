import React from 'react'
import {Button, Col, Form, Row} from 'react-bootstrap'

export default function Tags() {
    return (
        <>
                <Form>
                    <Row className={"pt-4 pb-5"}>
                        <Col xs={3}>
                            <Form.Control type="text" />
                        </Col>
                        <Col>
                            <Button>Add Tag</Button>
                        </Col>
                    </Row>
                    <Row className={"fs-5"}>
                        <Col xs={2} className={"fw-bold"}>Tag 1</Col>
                        <Col><Button variant={"link"}>delete</Button></Col>
                    </Row>
                    <Row className={"fs-5"}>
                        <Col xs={2} className={"fw-bold"}>Tag 2</Col>
                        <Col><Button variant={"link"}>delete</Button></Col>
                    </Row>
                    <Row className={"fs-5"}>
                        <Col xs={2} className={"fw-bold"}>Tag 3</Col>
                        <Col><Button variant={"link"}>delete</Button></Col>
                    </Row>
                    <Row className={"fs-5"}>
                        <Col xs={2} className={"fw-bold"}>Tag 4</Col>
                        <Col><Button variant={"link"}>delete</Button></Col>
                    </Row>
                </Form>
        </>
    )
}
