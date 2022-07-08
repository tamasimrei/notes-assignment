<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagService
{

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(
        TagRepository $tagRepository,
        ValidatorInterface $validator
    ) {
        $this->tagRepository = $tagRepository;
        $this->validator = $validator;
    }

    /**
     * @param Tag $tag
     * @return array
     */
    public function validateTag(Tag $tag): array
    {
        $validationErrors = [];
        $constraintViolations = $this->validator->validate($tag);

        foreach ($constraintViolations as $violation) {
            /** @var ConstraintViolation $violation */
            $validationErrors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $validationErrors;
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
