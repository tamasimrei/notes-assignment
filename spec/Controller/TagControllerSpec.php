<?php

namespace spec\App\Controller;

use App\Controller\AbstractApiController;
use App\Controller\TagController;
use App\Service\TagService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\SerializerInterface;

class TagControllerSpec extends ObjectBehavior
{
    function let(
        SerializerInterface $serializer,
        TagService $tagService
    ) {
        $this->beConstructedWith(
            $serializer,
            $tagService
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagController::class);
        $this->shouldHaveType(AbstractApiController::class);
    }
}
