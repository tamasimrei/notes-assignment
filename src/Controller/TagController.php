<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Service\TagService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class TagController extends AbstractApiController
{

    /**
     * @var TagService
     */
    private TagService $tagService;

    /**
     * @param TagService $tagService
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer,
        TagService $tagService
    ) {
        $this->tagService = $tagService;
        parent::__construct($serializer);
    }

    /**
     * @Route("/api/tag", name="get_tags", methods={"GET"}, format="json")
     * @return JsonResponse
     */
    public function getTagsAction(): JsonResponse
    {
        return $this->json($this->tagService->findAll());
    }

    /**
     * @Route("/api/tag/{tagId}", name="get_one_tag", methods={"GET"}, format="json")
     * @param int $tagId
     * @return JsonResponse
     */
    public function getOneTagAction(int $tagId): JsonResponse
    {
        $tag = $this->tagService->findById($tagId);
        if (!$tag) {
            throw $this->createJsonNotFoundException('Tag Not Found');
        }

        return $this->json($tag);
    }

    /**
     * @Route("/api/tag", name="create_tag", methods={"POST"}, format="json")
     * @param Request $request
     * @return JsonResponse
     */
    public function createTagAction(Request $request): JsonResponse
    {
        $tag = $this->getValidTagFromRequest($request);

        //dd($tag);
        // TODO create via repo method

        return $this->json($tag);
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
                'json'
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
        $validationErrors = $this->tagService->validateTag($tag);
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
                'json'
            )
        );
    }

    /**
     * @Route("/api/tag/{tagId}", name="delete_tag", methods={"DELETE"}, format="json")
     * @param int $tagId
     * @return Response
     */
    public function deleteOneTagAction(int $tagId): Response
    {
        $removed = $this->tagService->deleteById($tagId);
        if (!$removed) {
            throw $this->createJsonNotFoundException('Tag Not Found');
        }

        return (new Response())->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
