import React from 'react'
import {BrowserRouter, Route, Routes, NavLink} from "react-router-dom"
import {Col, Container, Nav, Row} from 'react-bootstrap'
import Notes from "../Notes"
import Tags from "../Tags"
import "./style.css"
import Alert from "../Alert"

export default function App() {

    const navLinkClassName = "text-uppercase fw-bold fs-3"

    return (
        <>
            <Container className="py-5">
                <Row>
                    <Col>
                        <Alert />
                    </Col>
                </Row>
                <BrowserRouter>
                    <Nav>
                        <Nav.Item>
                            <Nav.Link
                                as={NavLink}
                                to="/"
                                className={navLinkClassName}
                            >Notes</Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                            <Nav.Link
                                as={NavLink}
                                to="/tags"
                                className={navLinkClassName}
                            >Tags</Nav.Link>
                        </Nav.Item>
                    </Nav>
                    <Row>
                        <Col className="px-4">
                            <Routes>
                                <Route exact path="/" element={<Notes/>}/>
                                <Route exact path="/tags" element={<Tags/>}/>
                            </Routes>
                        </Col>
                    </Row>
                </BrowserRouter>
            </Container>
        </>
    )
}
