<?php

namespace App\Tests\Controller;

use stdClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NoteControllerControllerTest extends AbstractApiControllerTest
{

    public function testCreateErrorOnInvalidJson(): void
    {
        $this->client->catchExceptions(false);

        try {
            $this->client->xmlHttpRequest(
                'POST',
                '/api/note',
                [],
                [],
                [],
                '{"}'
            );
        } catch (BadRequestHttpException $exception) {
            $this->assertSame(400, $exception->getStatusCode());
        }
    }

    /**
     * @return stdClass
     */
    public function testCreateNote(): stdClass
    {
        $noteData = [
            'title' => 'Some Random Note',
            'description' => 'Here go the details',
            'tags' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ]
        ];

        $this->client->xmlHttpRequest(
            'POST',
            '/api/note',
            [],
            [],
            [],
            json_encode($noteData)
        );

        $this->assertSuccessfulJsonResponse();

        $noteCreated = $this->getDecodedResponseContents();
        $this->assertInstanceOf(stdClass::class, $noteCreated);
        $this->assertSame($noteData['title'], $noteCreated->title);
        $this->assertSame($noteData['description'], $noteCreated->description);

        $this->assertReturnedNoteTagNamesEqual($noteCreated, $noteData['tags']);

        return $noteCreated;
    }

    /**
     * @param stdClass $note
     * @param array $tags
     * @return void
     */
    private function assertReturnedNoteTagNamesEqual(stdClass $note, array $tags): void
    {
        $this->assertObjectHasAttribute('tags', $note);
        $this->assertIsArray($note->tags);

        $noteTags = [];
        foreach ($note->tags as $tagSaved) {
            $this->assertInstanceOf(stdClass::class, $tagSaved);
            $this->assertObjectHasAttribute('name', $tagSaved);
            $noteTags[] = ['name' => $tagSaved->name];
        }

        $this->assertEquals($tags, $noteTags);
    }

    /**
     * @depends testCreateNote
     *
     * @param stdClass $note
     * @return void
     */
    public function testGetOneNote(stdClass $note): void
    {
        $this->client->xmlHttpRequest(
            'GET',
            sprintf('/api/note/%d', $note->id)
        );

        $this->assertSuccessfulJsonResponse();

        $noteReturned = $this->getDecodedResponseContents();
        $this->assertEquals($note, $noteReturned);
    }

    /**
     * @depends testCreateNote
     *
     * @param stdClass $note
     * @return void
     */
    public function testGetAllNotes(stdClass $note): void
    {
        $this->client->xmlHttpRequest(
            'GET',
            sprintf('/api/note')
        );

        $this->assertSuccessfulJsonResponse();

        $notesList = $this->getDecodedResponseContents();
        $this->assertIsArray($notesList);
        $this->assertNotEmpty($notesList);
        $this->assertEquals($note, $notesList[0]);
    }

    /**
     * @depends testCreateNote
     *
     * @param stdClass $note
     * @return void
     */
    public function testUpdateNote(stdClass $note): void
    {
        $noteData = [
            'description' => 'Modified details here',
            'tags' => [
                ['name' => 'bar'],
            ]
        ];

        $this->client->xmlHttpRequest(
            'PATCH',
            sprintf('/api/note/%d', $note->id),
            [],
            [],
            [],
            json_encode($noteData)
        );

        $this->assertSuccessfulJsonResponse();

        $noteUpdated = $this->getDecodedResponseContents();
        $this->assertInstanceOf(stdClass::class, $noteUpdated);
        $this->assertSame($note->title, $noteUpdated->title);
        $this->assertSame($noteData['description'], $noteUpdated->description);

        $this->assertReturnedNoteTagNamesEqual($noteUpdated, $noteData['tags']);
    }

    /**
     * @depends testCreateNote
     *
     * @param stdClass $note
     * @return void
     */
    public function testDeleteNote(stdClass $note): void
    {
        $this->deleteTags($note->tags);

        $this->client->xmlHttpRequest(
            'DELETE',
            sprintf('/api/note/%d', $note->id)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEmpty($responseContent);
    }

    /**
     * @param stdClass[] $tags
     * @return void
     */
    private function deleteTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->client->xmlHttpRequest(
                'DELETE',
                sprintf('/api/tag/%d', $tag->id)
            );
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(204);
        }
    }
}
