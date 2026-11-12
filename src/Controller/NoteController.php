<?php

namespace App\Controller;

use App\Entity\Note;
use App\Service\NoteService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
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
        $this->noteService = $noteService;
        parent::__construct($serializer);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/note', name: 'get_notes', methods: ['GET'], format: 'json')]
    public function getNotesAction(): JsonResponse
    {
        return $this->json($this->noteService->getAllNotes());
    }

    /**
     * @param Note $note
     * @return JsonResponse
     */
    #[Route('/api/note/{noteId}', name: 'get_one_note', methods: ['GET'], format: 'json')]
    public function getOneNoteAction(#[MapEntity(id: 'noteId')] Note $note): JsonResponse
    {
        return $this->json($note);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/note', name: 'create_note', methods: ['POST'], format: 'json')]
    public function createNoteAction(Request $request): JsonResponse
    {
        $note = $this->getValidNoteFromRequest($request);
        $note = $this->noteService->saveNote($note);
        return $this->json($note);
    }

    /**
     * @param Note $note
     * @return Response
     */
    #[Route('/api/note/{noteId}', name: 'delete_note', methods: ['DELETE'], format: 'json')]
    public function deleteOneNoteAction(#[MapEntity(id: 'noteId')] Note $note): Response
    {
        $this->noteService->deleteNote($note);
        return (new Response())->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param Note $note
     * @return JsonResponse
     */
    #[Route('/api/note/{noteId}', name: 'update_note', methods: ['PUT', 'PATCH'], format: 'json')]
    public function updateNoteAction(Request $request, #[MapEntity(id: 'noteId')] Note $note): JsonResponse
    {
        $note = $this->getValidNoteFromRequest($request, $note);
        $note = $this->noteService->saveNote($note);
        return $this->json($note);
    }

    /**
     * @param Request $request
     * @param object|null $objectToPopulate
     * @return Note
     */
    private function getValidNoteFromRequest(Request $request, ?object $objectToPopulate = null): Note
    {
        $note = $this->getNoteFromRequest($request, $objectToPopulate);
        $this->validateNote($note);
        return $note;
    }

    /**
     * @param Request $request
     * @param object|null $objectToPopulate
     * @return Note
     */
    private function getNoteFromRequest(Request $request, ?object $objectToPopulate): Note
    {
        $context = [];
        if (isset($objectToPopulate)) {
            $context['object_to_populate'] = $objectToPopulate;
        }

        try {
            $note = $this->serializer->deserialize(
                (string)$request->getContent(),
                Note::class,
                JsonEncoder::FORMAT,
                $context
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
