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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/api/tag", name="get_tags", methods={"GET"}, format="json")
     * @return JsonResponse
     */
    public function getTagsAction(): JsonResponse
    {
        return $this->json($this->tagService->getAllTags());
    }

    /**
     * @Route("/api/tag/{tagId}", name="get_one_tag", methods={"GET"}, format="json")
     * @ParamConverter("tag", options={"mapping":{"tagId":"id"}})
     * @param Tag $tag
     * @return JsonResponse
     */
    public function getOneTagAction(Tag $tag): JsonResponse
    {
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
        $tag = $this->tagService->saveTag($tag);
        return $this->json($tag);
    }

    /**
     * @Route("/api/tag/{tagId}", name="delete_tag", methods={"DELETE"}, format="json")
     * @ParamConverter("tag", options={"mapping":{"tagId":"id"}})
     * @param Tag $tag
     * @return Response
     */
    public function deleteOneTagAction(Tag $tag): Response
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
