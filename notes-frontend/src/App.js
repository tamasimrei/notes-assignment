import React from 'react'
import {BrowserRouter, Routes, Route, Link } from "react-router-dom"
import {Nav, Container, Row, Col, Button} from 'react-bootstrap'
import "./App.css"

export default function App() {
    return(
        <>
            <BrowserRouter>
                <Nav>
                    <Nav.Item>
                        <Nav.Link as={Link} to={"/"}>Notes</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link as={Link} to={"/tags"}>Tags</Nav.Link>
                    </Nav.Item>
                </Nav>
                <Container fluid>
                    <Row>
                        <Col>
                            <Routes>
                                <Route exact path="/" element={<Notes />}/>
                                <Route exact path="/tags" element={<Tags />}/>
                            </Routes>
                        </Col>
                    </Row>
                </Container>
            </BrowserRouter>
        </>
    )
}

function Notes() {
    return(
        <>
            <h2>Notes</h2>
            <Button>Foo</Button>
        </>
    )
}

function Tags() {
    return(
        <>
            <h2>Tags</h2>
            <Button>Bar</Button>
        </>
    )
}
