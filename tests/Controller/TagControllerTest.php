<?php

namespace App\Tests\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TagControllerTest extends WebTestCase
{

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @return void
     */
    public function testCreateErrorOnInvalidJson(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);
        $this->expectException(BadRequestHttpException::class);

        $this->client->xmlHttpRequest(
            'POST',
            '/api/tag',
            [],
            [],
            [],
            "''"
        );

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @return stdClass[]
     */
    public function testCreateTag(): array
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

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

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($responseContent);
        $tagSaved = json_decode($responseContent, false, JSON_THROW_ON_ERROR);
        $this->assertInstanceOf(stdClass::class, $tagSaved);
        $this->assertIsInt($tagSaved->id);
        $this->assertSame($tagName, $tagSaved->name);

        return $tagSaved;
    }

    /**
     * @depends testCreateTag
     *
     * @param array $tags
     * @return array
     */
    public function testGetAllTags(array $tags): array
    {
        $this->client = static::createClient();

        $this->client->xmlHttpRequest(
            'GET',
            '/api/tag'
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($responseContent);
        $tagsReturned = json_decode($responseContent, false, JSON_THROW_ON_ERROR);

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
     * @return array
     */
    public function testGetOneTag(array $tags): array
    {
        $this->client = static::createClient();

        $this->client->xmlHttpRequest(
            'GET',
            sprintf('/api/tag/%d', $tags[0]->id)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($responseContent);
        $tagReturned = json_decode($responseContent, false, JSON_THROW_ON_ERROR);
        $this->assertEquals($tags[0], $tagReturned);

        return $tags;
    }

    public function testCreateTagExistingName(): void
    {
        $this->client = static::createClient();
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
        $this->client = static::createClient();
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
