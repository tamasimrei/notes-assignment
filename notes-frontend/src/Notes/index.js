import React from 'react'
import {Badge, Button, Col, Row} from "react-bootstrap";

export default function Notes() {
    return (
        <>
            <Row className={"pt-4 pb-5"}>
                <Col xs={3}>
                    <Button className={"px-4"}>Add Note</Button>
                </Col>
            </Row>
            <Row>
                <Col>
                    <Row className={"align-items-baseline pb-1"}>
                        <Col
                            xs={6}
                            className={"fs-4 fw-bold"}
                        >
                            Note Title 1
                        </Col>
                        <Col
                            xs={5}
                            className={"text-end pe-4 fs-6 fst-italic text-muted"}
                        >
                            Created 11:13 am Aug 12, 2022
                        </Col>
                    </Row>
                    <Row>
                        <Col
                            xs={11}
                            className={"fs-6"}
                        >
                            Note Description - Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                            exercitation commodo consequat.
                        </Col>
                    </Row>
                    <Row className={"mt-2 fs-6"}>
                        <Col>
                            <Badge className={"me-2 px-3 py-2"}>Tag 2</Badge>
                            <Badge className={"me-2 px-3 py-2"}>Tag 5</Badge>
                        </Col>
                    </Row>
                    <Row>
                        <Col xs={11}><hr /></Col>
                    </Row>
                </Col>
            </Row>
        </>
    )
}
