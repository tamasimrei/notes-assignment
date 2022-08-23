import React from "react"
import {Badge} from "react-bootstrap";

export default function NoteTag(props) {
    return (
        <Badge className="me-2 px-3 py-2">{props.tag.name}</Badge>
    )
}
