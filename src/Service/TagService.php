<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagService extends AbstractEntityService
{

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param TagRepository $tagRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        TagRepository $tagRepository,
        ValidatorInterface $validator
    ) {
        $this->tagRepository = $tagRepository;
        parent::__construct($validator);
    }

    /**
     * @param int $tagId
     * @return Tag|null
     */
    public function findById(int $tagId): ?Tag
    {
        return $this->tagRepository->find($tagId);
    }

    /**
     * @return Tag[]
     */
    public function findAll(): array
    {
        return $this->tagRepository->findBy(
            [],
            ['name' => 'ASC']
        );
    }

    /**
     * @param Tag $tag
     * @return Tag
     */
    public function saveTag(Tag $tag): Tag
    {
        return $this->tagRepository->persist($tag);
    }

    /**
     * @param int $tagId
     * @return bool
     */
    public function deleteById(int $tagId): bool
    {
        $tag = $this->findById($tagId);
        if (empty($tag)) {
            return false;
        }

        $this->tagRepository->remove($tag);
        return true;
    }
}
