<?php

namespace spec\App\Controller;

use App\Controller\TagController;
use PhpSpec\ObjectBehavior;

class TagControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TagController::class);
    }
}
