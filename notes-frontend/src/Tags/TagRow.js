import React from "react"
import {Button, Col, Row} from "react-bootstrap"

export default function TagRow(props) {
    return (
        <Row className={"fs-5"}>
            <Col
                xs={2}
                className={"fw-bold"}
            >
                {props.tag.name}
            </Col>
            <Col>
                <Button
                    variant={"link"}
                    onClick={() => props.onDelete(props.tag.id)}
                >
                    delete
                </Button>
            </Col>
        </Row>
    )
}
