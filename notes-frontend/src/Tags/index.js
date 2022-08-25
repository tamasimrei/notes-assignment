import React, {useEffect, useState} from 'react'
import axios from "axios"
import LoadingSpinner from "../App/LoadingSpinner"
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    const [isLoading, setIsLoading] = useState(true)

    const [tagData, setTagData] = useState([])

    const httpClient = axios.create({
        // TODO store API base url in env
        //baseURL: "http://localhost:3000/test_tags.json",
        baseURL: 'http://localhost:8080/api/tag',
        timeout: 20000
    });

    const addTag = async (tagName) => {
        try {
            let response = await httpClient.post('', {
                name: tagName
            })
            let newTagData = [...tagData, response.data]
            newTagData.sort((a, b) => ('' + a.name).localeCompare(b.name))
            setTagData(tagData => newTagData)
        } catch (error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)
        }
    }

    const deleteTag = async (tagId) => {
        try {
            let response = await httpClient.delete('/' + tagId)
            setTagData(tagData => tagData.filter(tag => tag.id !== tagId))
        } catch (error) {
            // TODO implement error handling
            // see error.response.status
            console.log(error)
        }
    }

    useEffect(() => {
        const getTagDataAsync = async () => {
            try {
                let tagData = await httpClient.get('')
                if (!tagData || !tagData.data) {
                    throw new Error("No Tag data found")
                }
                setTagData(tagData.data)
            } catch(error) {
                // TODO implement error handling
                // see error.response.status
                console.log(error)
                setTagData([])
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
