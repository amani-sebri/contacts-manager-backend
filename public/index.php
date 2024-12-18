<?php
// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Apply CORS globally
App\Utils\CorsHandler::handle();

// Include the routes
require_once __DIR__ . '/../routes/api.php';
