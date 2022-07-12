<?php

namespace spec\App\Repository;

use App\Repository\TagRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;

class TagRepositorySpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry)
    {
        $this->beConstructedWith($managerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagRepository::class);
        $this->shouldHaveType(ServiceEntityRepository::class);
    }
}
