import React, {useEffect, useState} from 'react'
import axios from "axios"
import LoadingSpinner from "../App/LoadingSpinner"
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    const [isLoading, setIsLoading] = useState(true)

    const [tagData, setTagData] = useState([])

    // TODO replace this with the API
    const tagsApiUrl = 'http://localhost:3000/test_tags.json'

    function addTag(tagName) {
        // TODO create tag via API
        return {id: 234, name: tagName}
    }

    function deleteTag(tagId) {
        // TODO delete tag via API
        console.log(tagId)
    }

    useEffect(() => {
        const getTagDataAsync = async () => {
            try {



                // FIXME DEBUG
                await new Promise(r => setTimeout(r, 1000))



                const tagData = await axios.get(
                    tagsApiUrl,
                    {
                        method: "GET",
                        timeout: 2000
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
