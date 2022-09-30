import React, {useContext, useEffect, useMemo, useState} from 'react'
import axios from "axios"
import AlertContext from "../Alert/AlertContext"
import LoadingSpinner from "../App/LoadingSpinner"
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    const { addAlert } = useContext(AlertContext)
    const [tagData, setTagData] = useState([])
    const [isLoading, setIsLoading] = useState(true)

    const httpClient = useMemo(() => {
        return axios.create({
            // TODO store API base url in env
            baseURL: 'http://localhost:8080/api',
            timeout: 20000
        })
    }, [])

    const addTag = async (tagName) => {
        try {
            let response = await httpClient.post('/tag', {
                name: tagName
            })
            if (!response || !response.data) {
                throw new Error("Created Tag not received")
            }

            let newTagData = [...tagData, response.data]
            newTagData.sort((a, b) => ('' + a.name).localeCompare(b.name))
            setTagData(tagData => newTagData)
            addAlert('success', 'Tag added')
        } catch (error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)

            if (error.response) {
                // The client was given an error response (5xx, 4xx)
            } else if (error.request) {
                // The client never received a response, and the request was never left
            } else {
                // Anything else
            }
        }
    }

    const deleteTag = async (tagId) => {
        try {
            await httpClient.delete('/tag/' + tagId)
            setTagData(tagData => tagData.filter(tag => tag.id !== tagId))
            addAlert('info', 'Tag deleted')
        } catch (error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)

            if (error.response) {
                // The client was given an error response (5xx, 4xx)
            } else if (error.request) {
                // The client never received a response, and the request was never left
            } else {
                // Anything else
            }
        }
    }

    useEffect(() => {
        const getTagDataAsync = async () => {
            try {
                let tagsResponse = await httpClient.get('/tag')
                if (!tagsResponse || !tagsResponse.data) {
                    throw new Error("No Tags received")
                }
                setTagData(tagsResponse.data)
                addAlert('info', 'Tags loaded')
            } catch(error) {
                // TODO implement error handling
                // see error.response.status
                console.log(error)
                setTagData([])

                if (error.response) {
                    // The client was given an error response (5xx, 4xx)
                } else if (error.request) {
                    // The client never received a response, and the request was never left
                } else {
                    // Anything else
                }
            }
        }

        getTagDataAsync().then(() => setIsLoading(false))
    }, [httpClient])

    if (isLoading) {
        return (
            <LoadingSpinner />
        )
    }

    return (
        <>
            <AddTagForm onAddTag={addTag} />

            {tagData.map(tag =>
                <TagRow
                    key={tag.id}
                    tag={tag}
                    onDelete={deleteTag}
                />
            )}
        </>
    )
}
