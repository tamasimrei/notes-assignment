<?php

namespace spec\App\Service;

use App\Repository\NoteRepository;
use App\Service\NoteService;
use PhpSpec\ObjectBehavior;

class NoteServiceSpec extends ObjectBehavior
{
    function let(NoteRepository $noteRepository)
    {
        $this->beConstructedWith($noteRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NoteService::class);
    }
}
