import React from "react"
import {Col, Row, Spinner} from "react-bootstrap"

export default function LoadingSpinner() {
    return (
        <Row className="pt-4 pb-5">
            <Col xs={3}>
                <Spinner animation="border" variant="primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </Spinner>
            </Col>
        </Row>
    );
}
