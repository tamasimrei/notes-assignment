<?php

namespace spec\App\Controller;

use App\Controller\NoteController;
use PhpSpec\ObjectBehavior;

class NoteControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NoteController::class);
    }
}
