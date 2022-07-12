<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param Tag $tag
     * @return Tag
     */
    public function persist(Tag $tag): Tag
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
        return $tag;
    }

    /**
     * @param Tag $tag
     * @return void
     */
    public function remove(Tag $tag): void
    {
        $this->getEntityManager()->remove($tag);
        $this->getEntityManager()->flush();
    }
}
