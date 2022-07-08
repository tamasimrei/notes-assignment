<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\Collection;

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
     * @return Tag[]
     */
    public function findAll()
    {
        return $this->tagRepository->findBy(
            [],
            ['name' => 'ASC']
        );
    }

    /**
     * @param int $tagId
     * @return Tag|null
     */
    public function findById(int $tagId)
    {
        return $this->tagRepository->find($tagId);
    }

    /**
     * @param int $tagId
     * @return bool
     */
    public function deleteById(int $tagId): bool
    {
        $tag = $this->findById($tagId);
        if (! $tag) {
            return false;
        }

        $this->tagRepository->remove($tag);
        return true;
    }
}
