<?php
namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CategoryRequest
{
    public static function rules()
    {
        return new Assert\Collection([
            'name' => new Assert\NotBlank(['message' => 'Category name required']),
        ]);
    }
}