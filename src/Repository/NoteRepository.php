<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * @param Note $note
     * @return Note
     */
    public function persist(Note $note): Note
    {
        $this->getEntityManager()->persist($note);
        $this->getEntityManager()->flush();
        return $note;
    }

    /**
     * @param Note $note
     * @return void
     */
    public function remove(Note $note): void
    {
        $this->getEntityManager()->remove($note);
        $this->getEntityManager()->flush();
    }
}
