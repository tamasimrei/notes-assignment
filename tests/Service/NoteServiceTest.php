<?php

namespace App\Tests\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Service\NoteService;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NoteServiceTest extends KernelTestCase
{

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
    }

    private function getNoteService(): NoteService
    {
        return self::$container->get(NoteService::class);
    }

    private function getTagService(): TagService
    {
        return self::$container->get(TagService::class);
    }

    public function testNoteValidation(): void
    {
        $note = new Note();

        $validationErrors = $this->getNoteService()->validate($note);
        $this->assertIsArray($validationErrors);
        $this->assertNotEmpty($validationErrors);
        $titleError = $validationErrors[0];
        $this->assertSame('title', $titleError['field']);
        $this->assertSame('This value should not be blank.', $titleError['message']);

        $note->setTitle('A nice title');
        $noErrors = $this->getNoteService()->validate($note);
        $this->assertIsArray($noErrors);
        $this->assertEmpty($noErrors);
    }

    public function testSavingNoteWithTags(): void
    {
        // Tag to be found in database when saving Note
        $tag1 = new Tag('weight');
        $this->getTagService()->saveTag($tag1);

        // Tag to be created when saving note
        $tag2 = new Tag('height');

        $note = new Note();
        $note
            ->setTitle('Personal Data')
            ->setDescription('Details about a person')
            ->addTag($tag1)
            ->addTag($tag2)
        ;
        $this->getNoteService()->saveNote($note);

        // Check if all tags were saved
        $this->assertIsInt($note->getId());
        foreach ($note->getTags() as $tag) {
            $this->assertIsInt($tag->getId());
        }
        $this->assertIsInt($tag2->getId());

        // Cleaning up
        $this->getNoteService()->deleteNote($note);
        $this->getTagService()->deleteTag($tag1);
        $this->getTagService()->deleteTag($tag2);
    }
}
