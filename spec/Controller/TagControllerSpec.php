<?php

namespace spec\App\Controller;

use App\Controller\TagController;
use App\Service\TagService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagControllerSpec extends ObjectBehavior
{
    function let(
        TagService $tagService,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->beConstructedWith(
            $tagService,
            $serializer,
            $validator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagController::class);
    }
}
