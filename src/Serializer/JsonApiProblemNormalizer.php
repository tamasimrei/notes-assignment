<?php

namespace App\Serializer;

use ArrayObject;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonApiProblemNormalizer implements NormalizerInterface
{

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     * @return array|string|int|float|bool|ArrayObject|null \ArrayObject is used to make sure an empty object is encoded as an object not an array
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof FlattenException) {
            throw new InvalidArgumentException();
        }

        return json_decode($object->getMessage());
    }

    /**
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     * @return bool
     */
    public function supportsNormalization($data, $format = null): bool
    {





        // FIXME
        // service disabled
        return false;






        return $data instanceof FlattenException;
    }
}
