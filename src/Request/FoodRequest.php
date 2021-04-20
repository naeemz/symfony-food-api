<?php
namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class FoodRequest
{
    public static function rules()
    {
        return new Assert\Collection([
            'name'          => new Assert\NotBlank(['message' => 'Food name required']),
            'price'         => [new Assert\NotBlank(), new Assert\PositiveOrZero(['message' => 'Price should be numeric'])],
            'image'         => new Assert\Optional(new Assert\File(['maxSize'=>'1024k', 'mimeTypes' => ['image/jpeg', 'image/png']])),
            'description'   => new Assert\Optional(),
            'serving_per_person' => new Assert\Optional(),
            'categories'    => new Assert\Optional(),
        ]);
    }
}