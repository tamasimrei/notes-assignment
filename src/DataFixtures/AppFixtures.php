<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    const TAG_NAMES = ['foo', 'bar', 'baz', 'qux', 'corge', 'grault', 'garply', 'waldo', 'fred'];

    const NOTES = [
        //  [title          description                     created at      [tags] ]
            ['some note',   'here comes the description',   '3 hours ago',  ['bar','qux']   ],
            ['lorem ipsum', 'details provided for lipsum',  'yesterday',    ['foo', 'fred'] ],
            ['yet another', 'whatever it means',            '40 hours ago', []              ],
    ];

    public function load(ObjectManager $manager): void
    {
        array_map([$manager, 'persist'], $this->createTags());

        $manager->flush();
    }

    /**
     * @return array
     */
    private function createTags(): array
    {
        return array_map([$this, 'createTag'], self::TAG_NAMES);
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
