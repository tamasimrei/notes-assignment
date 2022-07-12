<?php

namespace App\Service;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteService extends AbstractEntityService
{

    /**
     * @var NoteRepository
     */
    private NoteRepository $noteRepository;

    /**
     * @param NoteRepository $noteRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        NoteRepository $noteRepository,
        ValidatorInterface $validator
    ) {
        $this->noteRepository = $noteRepository;
        parent::__construct($validator);
    }

    /**
     * @param int $noteId
     * @return Note|null
     */
    public function findById(int $noteId): ?Note
    {
        return $this->noteRepository->find($noteId);
    }

    /**
     * @return Note[]
     */
    public function findAll(): array
    {
        return $this->noteRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );
    }

    /**
     * @param Note $note
     * @return Note
     */
    public function saveNote(Note $note): Note
    {
        return $this->noteRepository->persist($note);
    }

    /**
     * @param int $noteId
     * @return bool
     */
    public function deleteById(int $noteId): bool
    {
        $note = $this->findById($noteId);
        if (empty($note)) {
            return false;
        }

        $this->noteRepository->remove($note);
        return true;
    }
}
