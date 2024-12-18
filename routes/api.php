<?php

// Manually include necessary files
require_once '../app/Controllers/Contact/ContactController.php';
require_once '../app/Utils/CorsHandler.php';

// Apply CORS globally
App\Utils\CorsHandler::handle();

// Fetch request method and URI
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Initialize the ContactController
$contactController = new App\Controllers\Contact\ContactController();

// Simple routing mechanism
switch (true) {
    case $requestMethod === 'GET' && $requestUri === '/api/contacts':
        $contactController->index();
        break;

    case $requestMethod === 'POST' && $requestUri === '/api/contacts':
        $contactController->create();
        break;

// Handle PUT request for updating a contact by ID
case $requestMethod === 'PUT' && preg_match('/\/api\/contacts\/(\d+)$/', $requestUri, $matches):
    $contactController->update($matches[1]);
    break;

// Handle DELETE request for deleting a contact by ID
case $requestMethod === 'DELETE' && preg_match('/\/api\/contacts\/(\d+)$/', $requestUri, $matches):
    $contactController->delete($matches[1]);
    break;

    case $requestMethod === 'OPTIONS':
        // Preflight requests for CORS
        http_response_code(200);
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
