<?php

namespace App\Controller;

use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/api/tag", name="tags", methods={"GET"})
     * @return JsonResponse
     */
    public function getTagsAction(): JsonResponse
    {
        return $this->json($this->tagService->findAll());
    }
}
