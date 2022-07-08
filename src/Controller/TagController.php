<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{

    /**
     * @var TagService
     */
    private $tagService;

    /**
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @Route("/api/tag", name="get_tags", methods={"GET"})
     * @return JsonResponse
     */
    public function getTagsAction(): JsonResponse
    {
        return $this->json($this->tagService->findAll());
    }

    /**
     * @Route("/api/tag/{tagId}", name="get_one_tag", methods={"GET"})
     * @param int $tagId
     * @return JsonResponse
     */
    public function getOneTagAction(int $tagId): JsonResponse
    {
        $tag = $this->tagService->findById($tagId);
        if (!$tag) {
            throw $this->createNotFoundException();
        }

        return $this->json($tag);
    }

    /**
     * @Route("/api/tag", name="create_tag", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function createTagAction(
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        // handle request data
        $tag = $serializer->deserialize((string)$request->getContent(), Tag::class, 'json');
        dd($tag);
        // validate object
        // create via repo method
        // return new object in json
    }

    /**
     * @Route("/api/tag/{tagId}", name="delete_tag", methods={"DELETE"})
     * @param int $tagId
     * @return Response
     */
    public function deleteOneTagAction(int $tagId): Response
    {
        $removed = $this->tagService->deleteById($tagId);
        if (!$removed) {
            throw $this->createNotFoundException();
        }

        return (new Response())->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
