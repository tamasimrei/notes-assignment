<?php

namespace spec\App\Entity;

use App\Entity\Note;
use App\Entity\Tag;
use DateTime;
use PhpSpec\ObjectBehavior;

class NoteSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Note::class);
    }

    function it_has_id()
    {
        $this->getId()->shouldReturn(null);
        $this->setId(12345)->shouldReturn($this);
        $this->getId()->shouldReturn(12345);
    }

    function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);
        $this->setTitle('some title')->shouldReturn($this);
        $this->getTitle()->shouldReturn('some title');
    }

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);
        $this->setDescription('detailed description')->shouldReturn($this);
        $this->getDescription()->shouldReturn('detailed description');
    }

    function it_has_a_creation_timestamp()
    {
        $this->getCreatedAt()->shouldBeAnInstanceOf(DateTime::class);
    }

    function it_can_have_tags(Tag $tag)
    {
        $this->getTags()->shouldReturn([]);
        $this->hasTag($tag)->shouldReturn(false);
        $this->addTag($tag)->shouldReturn($this);
        $this->hasTag($tag)->shouldReturn(true);
        $this->getTags()->shouldReturn([$tag]);
    }
}
