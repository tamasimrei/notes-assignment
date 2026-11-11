<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Service\TagService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class TagController extends AbstractApiController
{

    /**
     * @var TagService
     */
    private TagService $tagService;

    /**
     * @param SerializerInterface $serializer
     * @param TagService $tagService
     */
    public function __construct(
        SerializerInterface $serializer,
        TagService $tagService
    ) {
        $this->tagService = $tagService;
        parent::__construct($serializer);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/tag', name: 'get_tags', methods: ['GET'], format: 'json')]
    public function getTagsAction(): JsonResponse
    {
        return $this->json($this->tagService->getAllTags());
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     */
    #[Route('/api/tag/{tagId}', name: 'get_one_tag', methods: ['GET'], format: 'json')]
    public function getOneTagAction(#[MapEntity(id: 'tagId')] Tag $tag): JsonResponse
    {
        return $this->json($tag);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/tag', name: 'create_tag', methods: ['POST'], format: 'json')]
    public function createTagAction(Request $request): JsonResponse
    {
        $tag = $this->getValidTagFromRequest($request);
        $tag = $this->tagService->saveTag($tag);
        return $this->json($tag);
    }

    /**
     * @param Tag $tag
     * @return Response
     */
    #[Route('/api/tag/{tagId}', name: 'delete_tag', methods: ['DELETE'], format: 'json')]
    public function deleteOneTagAction(#[MapEntity(id: 'tagId')] Tag $tag): Response
    {
        $this->tagService->deleteTag($tag);
        return (new Response())->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Tag
     * @throws BadRequestHttpException
     */
    private function getValidTagFromRequest(Request $request): Tag
    {
        $tag = $this->getTagFromRequest($request);
        $this->validateTag($tag);
        return $tag;
    }

    /**
     * @param Request $request
     * @return Tag
     */
    private function getTagFromRequest(Request $request): Tag
    {
        try {
            $tag = $this->serializer->deserialize(
                (string)$request->getContent(),
                Tag::class,
                JsonEncoder::FORMAT
            );
        } catch (NotEncodableValueException $e) {
            throw $this->createJsonBadRequestHttpException('Cannot Decode Request');
        }

        return $tag;
    }

    /**
     * @param Tag $tag
     */
    private function validateTag(Tag $tag): void
    {
        $validationErrors = $this->tagService->validate($tag);
        if (empty($validationErrors)) {
            return;
        }

        throw new UnprocessableEntityHttpException(
            $this->serializer->serialize(
                [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Tag Validation Failed',
                    'errors' => $validationErrors,
                ],
                JsonEncoder::FORMAT
            )
        );
    }
}
