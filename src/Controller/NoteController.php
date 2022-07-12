<?php

namespace App\Controller;

use App\Entity\Note;
use App\Service\NoteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class NoteController extends AbstractApiController
{

    /**
     * @var NoteService
     */
    private NoteService $noteService;

    /**
     * @param SerializerInterface $serializer
     * @param NoteService $noteService
     */
    public function __construct(
        SerializerInterface $serializer,
        NoteService $noteService
    ) {
        parent::__construct($serializer);
        $this->noteService = $noteService;
    }

    /**
     * @Route("/api/note", name="create_note", methods={"POST"}, format="json")
     * @param Request $request
     * @return JsonResponse
     */
    public function createNoteAction(Request $request): JsonResponse
    {
        $note = $this->getValidNoteFromRequest($request);
        $note = $this->noteService->saveNote($note);
        return $this->json($note);
    }

    /**
     * @param Request $request
     * @return Note
     * @throws BadRequestHttpException
     */
    private function getValidNoteFromRequest(Request $request): Note
    {
        $note = $this->getNoteFromRequest($request);
        $this->validateNote($note);
        return $note;
    }

    /**
     * @param Request $request
     * @return Note
     */
    private function getNoteFromRequest(Request $request): Note
    {
        try {
            $note = $this->serializer->deserialize(
                (string)$request->getContent(),
                Note::class,
                JsonEncoder::FORMAT
            );
        } catch (NotEncodableValueException $e) {
            throw $this->createJsonBadRequestHttpException('Cannot Decode Request');
        }

        return $note;
    }

    /**
     * @param Note $note
     */
    private function validateNote(Note $note): void
    {
        $validationErrors = $this->noteService->validate($note);
        if (empty($validationErrors)) {
            return;
        }

        throw new UnprocessableEntityHttpException(
            $this->serializer->serialize(
                [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Note Validation Failed',
                    'errors' => $validationErrors,
                ],
                JsonEncoder::FORMAT
            )
        );
    }
}
