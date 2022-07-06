<?php

namespace spec\App\Entity;

use App\Entity\Tag;
use PhpSpec\ObjectBehavior;

class TagSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Tag::class);
    }

    function it_has_id()
    {
        $this->getId()->shouldReturn(null);
        $this->setId(12345)->shouldReturn($this);
        $this->getId()->shouldReturn(12345);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(null);
        $this->setName('foobar')->shouldReturn($this);
        $this->getName()->shouldReturn('foobar');
    }
}
