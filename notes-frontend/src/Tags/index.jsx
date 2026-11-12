import React, {useContext, useEffect, useState} from 'react'
import httpClient from "../api/httpClient"
import AlertContext from "../Alert/AlertContext"
import LoadingSpinner from "../App/LoadingSpinner"
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    const { addAlert, addHttpErrorAlert } = useContext(AlertContext)
    const [tagData, setTagData] = useState([])
    const [isLoading, setIsLoading] = useState(true)

    const addTag = async (tagName) => {
        try {
            let response = await httpClient.post('/tag', {
                name: tagName
            })
            if (!response || !response.data || !response.data.id || !response.data.name) {
                throw new Error("Created tag not received")
            }

            let newTagData = [...tagData, response.data]
            newTagData.sort((a, b) => ('' + a.name).localeCompare(b.name))
            setTagData(tagData => newTagData)
            addAlert('success', 'Tag added')
        } catch (error) {
            addHttpErrorAlert(error, 'Error creating tag')
        }
    }

    const deleteTag = async (tagId) => {
        try {
            let response = await httpClient.delete('/tag/' + tagId)
            if (response.status !== 204) {
                throw new Error('Unexpected HTTP status received: ' + response.status)
            }

            setTagData(tagData => tagData.filter(tag => tag.id !== tagId))
            addAlert('info', 'Tag deleted')
        } catch (error) {
            addHttpErrorAlert(error, 'Error deleting tag')
        }
    }

    useEffect(() => {
        const getTagDataAsync = async () => {
            try {
                let tagsResponse = await httpClient.get('/tag')
                if (!tagsResponse || !tagsResponse.data || !Array.isArray(tagsResponse.data)) {
                    throw new Error('No tags received')
                }
                setTagData(tagsResponse.data)
                addAlert('info', 'Tags loaded')
            } catch(error) {
                setTagData([])
                addHttpErrorAlert(error, 'Error loading tags')
            }
        }

        getTagDataAsync().then(() => setIsLoading(false))
    }, [])

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
