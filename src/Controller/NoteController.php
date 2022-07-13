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
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/api/note", name="get_notes", methods={"GET"}, format="json")
     * @return JsonResponse
     */
    public function getNotesAction(): JsonResponse
    {
        return $this->json($this->noteService->getAllNotes());
    }

    /**
     * @Route("/api/note/{noteId}", name="get_one_note", methods={"GET"}, format="json")
     * @ParamConverter("note", options={"mapping":{"noteId":"id"}})
     * @param Note $note
     * @return JsonResponse
     */
    public function getOneNoteAction(Note $note): JsonResponse
    {
        return $this->json($note);
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
     * @Route("/api/note/{noteId}", name="delete_note", methods={"DELETE"}, format="json")
     * @ParamConverter("note", options={"mapping":{"noteId":"id"}})
     * @param Note $note
     * @return Response
     */
    public function deleteOneNoteAction(Note $note): Response
    {
        $this->noteService->deleteNote($note);
        return (new Response())->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/note/{noteId}", name="update_note", methods={"PUT","PATCH"}, format="json")
     * @ParamConverter("note", options={"mapping":{"noteId":"id"}})
     * @param Request $request
     * @param Note $note
     * @return JsonResponse
     */
    public function updateNoteAction(Request $request, Note $note): JsonResponse
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
    private function getValidNoteFromRequest(Request $request, object $objectToPopulate = null): Note
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
