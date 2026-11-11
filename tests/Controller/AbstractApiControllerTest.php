<?php

namespace App\Tests\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiControllerTest extends WebTestCase
{

    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function assertSuccessfulJsonResponse(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    /**
     * Asserts the response is an error rendered by Symfony's error handling
     * through JsonApiProblemNormalizer: {code, message, errors[]}.
     */
    protected function assertJsonErrorResponse(int $statusCode, string $message): stdClass
    {
        $this->assertResponseStatusCodeSame($statusCode);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $error = $this->getDecodedResponseContents();
        $this->assertInstanceOf(stdClass::class, $error);
        $this->assertSame($statusCode, $error->code);
        $this->assertSame($message, $error->message);
        $this->assertIsArray($error->errors);

        return $error;
    }

    /**
     * @return mixed
     */
    protected function getDecodedResponseContents(): mixed
    {
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($responseContent);
        return json_decode($responseContent, false, JSON_THROW_ON_ERROR);
    }
}
