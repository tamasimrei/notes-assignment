import React from 'react'
import {BrowserRouter, Route, Routes, NavLink} from "react-router-dom"
import {Col, Container, Nav, Row} from 'react-bootstrap'
import Notes from "../Notes"
import Tags from "../Tags"
import "./style.css"

export default function App() {
    return (
        <>
            <BrowserRouter>
                <Nav>
                    <Nav.Item>
                        <Nav.Link
                            as={NavLink}
                            to={"/"}
                        >Notes</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link
                            as={NavLink}
                            to={"/tags"}
                        >Tags</Nav.Link>
                    </Nav.Item>
                </Nav>
                <Container fluid>
                    <Row>
                        <Col style={{padding: '0 1em'}}>
                            <Routes>
                                <Route exact path="/" element={<Notes/>}/>
                                <Route exact path="/tags" element={<Tags/>}/>
                            </Routes>
                        </Col>
                    </Row>
                </Container>
            </BrowserRouter>
        </>
    )
}
