<?php

namespace spec\App\Service;

use App\Repository\NoteRepository;
use App\Service\AbstractEntityService;
use App\Service\NoteService;
use App\Service\TagService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteServiceSpec extends ObjectBehavior
{
    function let(
        NoteRepository $noteRepository,
        ValidatorInterface $validator,
        TagService $tagService
    ) {
        $this->beConstructedWith($noteRepository, $validator, $tagService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NoteService::class);
        $this->shouldHaveType(AbstractEntityService::class);
    }
}
