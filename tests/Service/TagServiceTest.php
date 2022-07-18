<?php

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagServiceTest extends KernelTestCase
{

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
    }

    private function getTagService(): TagService
    {
        return self::$container->get(TagService::class);
    }

    public function testTagValidation(): void
    {
        // No errors returned for valid tag
        $validTag = new Tag('foobar');
        $validTagErrors = $this->getTagService()->validate($validTag);
        $this->assertIsArray($validTagErrors);
        $this->assertEmpty($validTagErrors);

        // Tag name cannot be empty
        $invalidTag = new Tag();
        $invalidTagErrors = $this->getTagService()->validate($invalidTag);
        $this->assertIsArray($invalidTagErrors);
        $this->assertNotEmpty($invalidTagErrors);
        $nameError = $invalidTagErrors[0];
        $this->assertSame('name', $nameError['field']);
        $this->assertSame('This value should not be blank.', $nameError['message']);
    }

    public function testFindingTag(): void
    {
        $service = $this->getTagService();

        $tag = new Tag('garply');
        $this->assertSame($tag, $service->saveTag($tag));
        $this->assertIsInt($tag->getId());
        $tagFoundById = $service->find($tag);
        $this->assertSame($tag->getId(), $tagFoundById->getId());

        $nameOnlyTag = new Tag('garply');
        $tagFoundByName = $service->find($nameOnlyTag);
        $this->assertSame($tag->getId(), $tagFoundByName->getId());

        // cleaning up
        $service->deleteTag($tag);
    }
}
