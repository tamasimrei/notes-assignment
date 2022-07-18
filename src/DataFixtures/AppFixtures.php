<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\Tag;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Exception;

class AppFixtures extends Fixture
{

    private const TAG_NAMES = ['foo', 'bar', 'baz', 'qux', 'waldo', 'fred'];

    private const NOTES = [
        // [title       description                    created at    [tags] ]
        ['some note',   'here comes the description',  '3 days ago', ['bar', 'qux']],
        ['lorem ipsum', 'details provided for lipsum', 'yesterday',  ['foo', 'fred']],
        ['yet another', 'whatever it means',           '1 hour ago', []],
    ];

    /**
     * @var ObjectRepository
     */
    private ObjectRepository $tagRepository;

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->tagRepository = $manager->getRepository(Tag::class);

        $tags = $this->createTags();
        array_walk($tags, [$manager, 'persist']);
        $manager->flush();

        $notes = $this->createNotes();
        array_walk($notes, [$manager, 'persist']);
        $manager->flush();
    }

    /**
     * Create Tag entities from tag names
     *
     * @return array
     */
    private function createTags(): array
    {
        return array_map([$this, 'createTag'], self::TAG_NAMES);
    }

    /**
     * @return array
     */
    private function createNotes(): array
    {
        return array_map([$this, 'createNote'], self::NOTES);
    }

    /**
     * @param array $data
     * @return Note
     * @throws Exception
     */
    private function createNote(array $data): Note
    {
        $note = new Note();
        $note
            ->setTitle($data[0])
            ->setDescription(($data[1]))
            ->setCreatedAt(new DateTime($data[2]))
        ;

        $this->addTagsToNote($note, $data[3]);

        return $note;
    }

    /**
     * @param Note $note
     * @param array $tagNames
     * @return void
     */
    private function addTagsToNote(Note $note, array $tagNames): void
    {
        foreach ($tagNames as $tagName) {
            $tag = $this->findTagByName($tagName) ?? $this->createTag($tagName);
            $note->addTag($tag);
        }
    }

    /**
     * @param string $tagName
     * @return Tag|null
     */
    private function findTagByName(string $tagName): ?Tag
    {
        return $this->tagRepository->findOneBy(['name' => $tagName]);
    }

    /**
     * @param string $name
     * @return Tag
     */
    private function createTag(string $name): Tag
    {
        return new Tag($name);
    }
}
