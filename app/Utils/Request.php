<?php

namespace App\Utils;

class Request
{
    private $inputData;

    public function __construct()
    {
        $this->inputData = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    // Get all input data
    public function all()
    {
        return $this->inputData;
    }

    // Get a specific input value with a default
    public function get($key, $default = null)
    {
        return $this->inputData[$key] ?? $default;
    }

    // Get input as JSON (optional helper)
    public function asJson()
    {
        return json_encode($this->inputData);
    }
}
