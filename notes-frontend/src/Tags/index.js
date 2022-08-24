import React, {useEffect, useState} from 'react'
import axios from "axios"
import LoadingSpinner from "../App/LoadingSpinner"
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    const [isLoading, setIsLoading] = useState(true)

    const [tagData, setTagData] = useState([])

    // TODO replace this with the API
    //const tagsApiUrl = 'http://localhost:3000/test_tags.json'
    const tagsApiUrl = 'http://localhost:8080/api/tag'

    function addTag(tagName) {
        // TODO create tag via API
        const newTag = {
            id: Math.floor(Math.random() * 1000000 ) + 1,
            name: tagName
        }

        const newTagData = [...tagData, newTag]
        newTagData.sort((a, b) => ('' + a.name).localeCompare(b.name))
        setTagData(tagData => newTagData)
    }

    function deleteTag(tagId) {
        // TODO delete tag via API
        setTagData(tagData => tagData.filter(tag => tag.id !== tagId))
    }

    useEffect(() => {
        const getTagDataAsync = async () => {
            try {
                const tagData = await axios.get(
                    tagsApiUrl,
                    {
                        method: "GET",
                        timeout: 20000
                    }
                )

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
