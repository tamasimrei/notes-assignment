<?php

namespace App\Serializer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Flatten the Symfony JSON exception to our custom error message
 * including the message from controllers
 */
class JsonApiProblemNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof FlattenException) {
            throw new InvalidArgumentException();
        }

        try {
            $error = json_decode(
                $object->getMessage(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            $error = [
                'code' => $object->getStatusCode(),
                'message' => $object->getStatusText(),
                'errors' => []
            ];
        }

        return $error;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof FlattenException;
    }
}
