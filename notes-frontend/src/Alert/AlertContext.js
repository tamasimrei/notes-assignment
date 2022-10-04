import { createContext, useState } from 'react'

const AlertContext = createContext({
    alerts: [],
    addAlert: () => {},
    removeAlert: () => {}
})

export const AlertProvider = ({children}) => {
    const [alerts, setAlerts] = useState([])
    const [nextAlertId, setNextAlertId] = useState(1)

    const addAlert = (severity, message) => {
        let autoHide, delay, badge, visual

        switch (severity) {
            case 'error':
                autoHide = false
                delay = null
                badge = '!'
                visual = 'danger'
                break
            case 'warning':
                autoHide = true
                delay = 10000
                badge = '!'
                visual = 'warning'
                break
            case 'success':
                autoHide = true
                delay = 3000
                badge = '='
                visual = 'success'
                break
            case 'info':
            default:
                autoHide = true
                delay = 2000
                badge = 'o'
                visual = 'light'
                break
        }

        setAlerts([
            {
                'id': nextAlertId,
                'caption': severity[0].toUpperCase() + severity.slice(1),
                'message': message,
                'visual': visual,
                'badge': badge,
                'autohide': autoHide,
                'delay': delay,
                'show': true,
                'timestamp': Date.now()
            },
            ...alerts
        ])
        setNextAlertId(nextAlertId + 1)
    }

    const removeAlert = (alertId) => {
        setAlerts(alerts => alerts.map(alert => {
            if (alert.id === alertId) {
                alert.show = false
            }
            return alert
        }))
        // Delay clean up to allow animated hiding
        setTimeout(() => {
            setAlerts(alerts => alerts.filter(alert => alert.id !== alertId))
        }, 5000)
    }

    return (
        <>
            <AlertContext.Provider
                value={{
                    alerts,
                    addAlert,
                    removeAlert
                }}
            >
                {children}
            </AlertContext.Provider>
        </>
    )
}

export default AlertContext
