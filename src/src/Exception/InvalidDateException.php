<?php

namespace App\Exception;

class InvalidDateException extends \Exception
{
    protected $message = 'Invalid date format: expected YYYY-MM-DD';
}