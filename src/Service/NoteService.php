<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\NoteRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteService extends AbstractEntityService
{

    /**
     * @var NoteRepository
     */
    private NoteRepository $noteRepository;

    /**
     * @var TagService
     */
    private TagService $tagService;

    /**
     * @param NoteRepository $noteRepository
     * @param ValidatorInterface $validator
     * @param TagService $tagService
     */
    public function __construct(
        NoteRepository $noteRepository,
        ValidatorInterface $validator,
        TagService $tagService
    ) {
        $this->noteRepository = $noteRepository;
        $this->tagService = $tagService;
        parent::__construct($validator);
    }

    /**
     * @return Note[]
     */
    public function getAllNotes(): array
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
        $this->saveTags($note);
        return $this->noteRepository->persist($note);
    }

    /**
     * @param Note $note
     * @return void
     */
    public function deleteNote(Note $note): void
    {
        $this->noteRepository->remove($note);
    }

    /**
     * @param Note $note
     * @return void
     */
    private function saveTags(Note $note): void
    {
        // Replace submitted tags with real tag entity, if any
        foreach ($note->getTags() as $tag) {
            $tagEntity = $this->tagService->find($tag);
            if ($tagEntity instanceof Tag) {
                $note->removeTag($tag);
                $note->addTag($tagEntity);
            }
        }
    }
}
