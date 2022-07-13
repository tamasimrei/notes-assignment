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
    private TagRepository $tagRepository;

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
     * @param Tag $tag
     * @return Tag|null
     */
    public function find(Tag $tag): ?Tag
    {
        if (! is_null($tag->getId())) {
            return $this->findById($tag->getId());
        }

        return $this->tagRepository->findOneBy(['name' => $tag->getName()]);
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
    public function getAllTags(): array
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
     * @param Tag $tag
     * @return void
     */
    public function deleteTag(Tag $tag): void
    {
        $this->tagRepository->remove($tag);
    }
}
