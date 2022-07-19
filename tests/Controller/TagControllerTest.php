<?php

namespace App\Tests\Controller;

use stdClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TagControllerTest extends AbstractApiControllerTest
{

    public function testCreateErrorOnInvalidJson(): void
    {
        $this->client->catchExceptions(false);

        try {
            $this->client->xmlHttpRequest(
                'POST',
                '/api/tag',
                [],
                [],
                [],
                "'foo'"
            );
        } catch (BadRequestHttpException $exception) {
            $this->assertSame(400, $exception->getStatusCode());
        }
    }

    /**
     * @return stdClass[]
     */
    public function testCreateTag(): array
    {
        return [
            $this->subTestCreateTag('foo'),
            $this->subTestCreateTag('bar'),
        ];
    }

    /**
     * @param string $tagName
     * @return stdClass
     */
    private function subTestCreateTag(string $tagName): stdClass
    {
        $this->client->xmlHttpRequest(
            'POST',
            '/api/tag',
            [],
            [],
            [],
            json_encode(['name' => $tagName])
        );

        $this->assertSuccessfulJsonResponse();

        $tagSaved = $this->getDecodedResponseContents();
        $this->assertInstanceOf(stdClass::class, $tagSaved);
        $this->assertIsInt($tagSaved->id);
        $this->assertSame($tagName, $tagSaved->name);

        return $tagSaved;
    }

    /**
     * @depends testCreateTag
     *
     * @param array $tags
     * @return stdClass[]
     */
    public function testGetAllTags(array $tags): array
    {
        $this->client->xmlHttpRequest(
            'GET',
            '/api/tag'
        );

        $this->assertSuccessfulJsonResponse();

        $tagsReturned = $this->getDecodedResponseContents();
        $this->assertIsArray($tagsReturned);

        // test if tags are sorted by alphabetically by name
        $this->assertEquals($tags[1], $tagsReturned[0]);
        $this->assertEquals($tags[0], $tagsReturned[1]);

        return $tagsReturned;
    }

    /**
     * @depends testGetAllTags
     *
     * @param array $tags
     * @return stdClass[]
     */
    public function testGetOneTag(array $tags): array
    {
        $this->client->xmlHttpRequest(
            'GET',
            sprintf('/api/tag/%d', $tags[0]->id)
        );

        $this->assertSuccessfulJsonResponse();

        $tagReturned = $this->getDecodedResponseContents();
        $this->assertEquals($tags[0], $tagReturned);

        return $tags;
    }

    public function testCreateTagExistingName(): void
    {
        $this->client->catchExceptions(false);

        $fooTag = $this->subTestCreateTag('qux');

        try {
            $this->client->xmlHttpRequest(
                'POST',
                '/api/tag',
                [],
                [],
                [],
                json_encode(['name' => 'qux'])
            );
        } catch (UnprocessableEntityHttpException $exception) {
            $this->assertSame($exception->getStatusCode(), 422);
            $this->subTestDeleteTag($fooTag);
        }
    }

    /**
     * @depends testGetOneTag
     *
     * @param array $tags
     * @return void
     */
    public function testDeleteTag(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->subTestDeleteTag($tag);
        }
    }

    /**
     * @param stdClass $tag
     * @return void
     */
    private function subTestDeleteTag(stdClass $tag): void
    {
        $this->client->xmlHttpRequest(
            'DELETE',
            sprintf('/api/tag/%d', $tag->id)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEmpty($responseContent);
    }
}
