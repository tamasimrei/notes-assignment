import axios from "axios"

const baseURL = import.meta.env.VITE_API_BASE_URL
if (!baseURL) {
    throw new Error('VITE_API_BASE_URL is not set (see notes-frontend/.env)')
}

export default axios.create({
    baseURL,
    timeout: 20000
})
