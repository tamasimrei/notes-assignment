<?php

namespace App\Tests\Controller;

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
     * @return mixed
     */
    protected function getDecodedResponseContents(): mixed
    {
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($responseContent);
        return json_decode($responseContent, false, JSON_THROW_ON_ERROR);
    }
}
