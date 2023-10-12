<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class EmtyFileToNullTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        // If the field is empty, convert it to null
        if ($value === null || $value === '') {
            return null;
        }

        return $value;
    }
}
