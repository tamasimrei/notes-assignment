<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class AbstractApiController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param string $message
     * @return NotFoundHttpException
     */
    protected function createJsonNotFoundException(string $message = 'Not Found'): NotFoundHttpException
    {
        $data = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => $message,
            'errors' => [],
        ];

        return $this->createNotFoundException(
            $this->serializer->serialize($data, 'json')
        );
    }

    /**
     * @param string $message
     * @return BadRequestHttpException
     */
    protected function createJsonBadRequestHttpException(string $message = 'Bad Request'): BadRequestHttpException
    {
        $data = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => $message,
            'errors' => [],
        ];

        return new BadRequestHttpException(
            $this->serializer->serialize($data, 'json')
        );
    }
}
