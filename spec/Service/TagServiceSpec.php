<?php

namespace spec\App\Service;

use App\Repository\TagRepository;
use App\Service\TagService;
use PhpSpec\ObjectBehavior;

class TagServiceSpec extends ObjectBehavior
{
    function let(TagRepository $tagRepository)
    {
        $this->beConstructedWith($tagRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagService::class);
    }
}
