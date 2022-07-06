<?php

namespace App\Service;

use App\Repository\NoteRepository;

class NoteService
{

    /**
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @param NoteRepository $noteRepository
     */
    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }
}
