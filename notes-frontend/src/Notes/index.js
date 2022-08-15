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
                    <Row className={"align-items-baseline"}>
                        <Col
                            xs={4}
                            className={"fs-4 fw-bold"}
                        >
                            Note Title 1
                        </Col>
                        <Col
                            xs={4}
                            className={"text-end pe-5 fs-6 fst-italic text-muted"}
                        >
                            Created 11:13 am Aug 12, 2022
                        </Col>
                    </Row>
                    <Row>
                        <Col
                            xs={8}
                            className={"fs-6"}
                        >
                            Note Description - Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                            in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
                            sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
                            est laborum.
                        </Col>
                    </Row>
                    <Row className={"mt-2 fs-6"}>
                        <Col>
                            <Badge className={"me-2 px-3 py-2"}>Tag 2</Badge>
                            <Badge className={"me-2 px-3 py-2"}>Tag 5</Badge>
                        </Col>
                    </Row>
                    <Row>
                        <Col xs={8}><hr /></Col>
                    </Row>
                </Col>
            </Row>
        </>
    )
}
