<?php

namespace spec\App\Controller;

use App\Controller\AbstractApiController;
use App\Controller\NoteController;
use App\Service\NoteService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\SerializerInterface;

class NoteControllerSpec extends ObjectBehavior
{
    function let(
        SerializerInterface $serializer,
        NoteService $noteService
    ) {
        $this->beConstructedWith(
            $serializer,
            $noteService
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NoteController::class);
        $this->shouldHaveType(AbstractApiController::class);
    }
}
