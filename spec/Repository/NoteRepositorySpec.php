<?php

namespace spec\App\Repository;

use App\Repository\NoteRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;

class NoteRepositorySpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry)
    {
        $this->beConstructedWith($managerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NoteRepository::class);
        $this->shouldHaveType(ServiceEntityRepository::class);
    }
}
