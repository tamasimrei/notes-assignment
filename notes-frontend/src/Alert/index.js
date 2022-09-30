import React, {useContext} from "react"
import {Badge, Toast, ToastContainer} from "react-bootstrap"
import ReactTimeAgo from "react-time-ago"
import AlertContext from "./AlertContext"

export default function Alert() {

    const {alerts, removeAlert} = useContext(AlertContext)

    return (
        <>
            <ToastContainer position="top-end" className="p-3">
                {alerts.map(alert =>
                    <Toast
                        key={alert.id}
                        autohide={alert.autohide}
                        delay={alert.delay}
                        show={alert.show}
                        onClose={() => removeAlert(alert.id)}
                        bg={alert.visual}
                        >
                        <Toast.Header>
                            <Badge
                                bg={alert.visual}
                                className="me-1"
                            >
                                {alert.badge}
                            </Badge>
                            <strong className="me-auto">
                                {alert.caption}
                            </strong>
                            <small className="text-muted">
                                <ReactTimeAgo date={alert.timestamp} />
                            </small>
                        </Toast.Header>
                        <Toast.Body>
                            {alert.message}
                        </Toast.Body>
                    </Toast>
                )}
            </ToastContainer>
        </>
    )
}
