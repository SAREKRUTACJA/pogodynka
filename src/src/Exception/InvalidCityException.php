<?php

namespace App\Exception;

class InvalidCityException extends \Exception
{
    protected $message = 'Invalid input: all cities must be strings';
}