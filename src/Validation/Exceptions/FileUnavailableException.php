<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 8/4/16
 * Time: 11:50 PM
 */
namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class FileUnavailableException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'File not found'
        ]
    ];

}