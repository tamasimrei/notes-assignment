<?php

namespace App\Service;

use App\Repository\TagRepository;

class TagService
{

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->tagRepository->findBy(
            [],
            ['name' => 'ASC']
        );
    }
}
