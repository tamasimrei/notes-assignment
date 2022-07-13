<?php

namespace spec\App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\AbstractEntityService;
use App\Service\TagService;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagServiceSpec extends ObjectBehavior
{
    function let(
        TagRepository $tagRepository,
        ValidatorInterface $validator
    ) {
        $this->beConstructedWith($tagRepository, $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagService::class);
        $this->shouldHaveType(AbstractEntityService::class);
    }

    function it_can_validate_a_tag(
        $validator,
        ConstraintViolationListInterface $violationList,
        Tag $tag
    ) {
        $violationList->valid()->willReturn(false);
        $violationList->rewind()->shouldBeCalled();

        $validator->validate($tag)->willReturn($violationList);

        $this->validate($tag)->shouldReturn([]);
    }

    function it_returns_validation_errors_for_invalid_object(
        $validator,
        ConstraintViolationListInterface $violationList,
        ConstraintViolation $constraintViolation1,
        ConstraintViolation $constraintViolation2,
        Tag $tag
    ) {
        // Setting up the violations
        $constraintViolation1->getPropertyPath()->willReturn('id');
        $constraintViolation1->getMessage()->willReturn('violation 1');
        $constraintViolation2->getPropertyPath()->willReturn('name');
        $constraintViolation2->getMessage()->willReturn('violation 2');

        // Allow the violations list to be iterated
        $violationList->rewind()->shouldBeCalled();
        $violationList->next()->shouldBeCalled(2);
        $violationList->valid()->willReturn(true, true, false);
        $violationList->current()->willReturn(
            $constraintViolation1,
            $constraintViolation2
        );

        $validator->validate($tag)->willReturn($violationList);

        $this->validate($tag)->shouldReturn([
            [
                'field' => 'id',
                'message' => 'violation 1'
            ],
            [
                'field' => 'name',
                'message' => 'violation 2'
            ]
        ]);
    }

    function it_can_find_a_tag_by_id(
        $tagRepository,
        Tag $tagWithId
    ) {
        $tagWithId->getId()->willReturn(123);
        $tagRepository->find(123)->willReturn($tagWithId);

        $this->find($tagWithId)->shouldReturn($tagWithId);
    }

    function it_can_find_a_tag_by_name(
        $tagRepository,
        Tag $tagWithOnlyName
    ) {
        $tagWithOnlyName->getId()->willReturn(null);
        $tagWithOnlyName->getName()->willReturn('foobar');
        $tagRepository->findOneBy(['name' => 'foobar'])->willReturn($tagWithOnlyName);

        $this->find($tagWithOnlyName)->shouldReturn($tagWithOnlyName);
    }

    function it_returns_null_trying_to_find_an_empty_tag(
        $tagRepository,
        Tag $emptyTag
    ) {
        $emptyTag->getId()->willReturn(null);
        $emptyTag->getName()->willReturn(null);
        $tagRepository->findOneBy(['name' => null])->willReturn(null);

        $this->find($emptyTag)->shouldReturn(null);
    }
}
