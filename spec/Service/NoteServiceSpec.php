<?php

namespace spec\App\Service;

use App\Repository\NoteRepository;
use App\Service\AbstractEntityService;
use App\Service\NoteService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteServiceSpec extends ObjectBehavior
{
    function let(
        NoteRepository $noteRepository,
        ValidatorInterface $validator
    ) {
        $this->beConstructedWith($noteRepository, $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NoteService::class);
        $this->shouldHaveType(AbstractEntityService::class);
    }
}
