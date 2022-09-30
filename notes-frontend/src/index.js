import React from 'react'
import ReactDOM from 'react-dom/client'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min'
import App from './App'
import {AlertProvider} from "./Alert/AlertContext"
import TimeAgo from 'javascript-time-ago'
import timeLocaleEn from 'javascript-time-ago/locale/en.json'

TimeAgo.addDefaultLocale(timeLocaleEn)

const root = ReactDOM.createRoot(document.getElementById('root'))

root.render(
    <React.StrictMode>
        <AlertProvider>
            <App />
        </AlertProvider>
    </React.StrictMode>
)
