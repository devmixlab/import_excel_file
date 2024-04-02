<?php
namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class UniquePostindexException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Value with `{{name}}` = {{input}} already exists.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Validation message if the negative of Something is called and fails validation.',
        ],
    ];
}