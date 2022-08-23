import React, {useState} from 'react'
import AddTagForm from "./AddTagForm"
import TagRow from "./TagRow"

export default function Tags() {

    // TODO fetch tags from the API
    const tempTagData = [
        {id: 1, name: "Tag 1"},
        {id: 2, name: "Tag 2"},
        {id: 3, name: "Tag 3"},
        {id: 4, name: "Tag 4"}
    ]

    const [tagData] = useState(tempTagData)

    function addTag(tagName) {
        // TODO create tag via API
        return {id: 234, name:tagName}
    }

    function deleteTag(tagId) {
        // TODO delete tag via API
        console.log(tagId)
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
