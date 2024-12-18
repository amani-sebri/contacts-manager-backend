<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\Contact\ContactController;
use App\Models\Contact;
use App\Utils\Request;

class ContactControllerTest extends TestCase
{
    private $contactMock;
    private $requestMock;
    private $controller;

    protected function setUp(): void
    {
        // Mock the Contact model
        $this->contactMock = $this->createMock(Contact::class);

        // Mock the Request utility
        $this->requestMock = $this->createMock(Request::class);

        // Inject mocks into the controller
        $this->controller = new ContactController();
        $this->controller->contactModel = $this->contactMock;
        $this->controller->request = $this->requestMock;
    }

    public function testCreateWithValidData()
    {
        // Mock request data
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => 30,
            'country' => 'USA',
            'email' => 'john.doe@example.com',
            'phone_number' => '1234567890'
        ];
        
        $this->requestMock->method('all')->willReturn($data);

        // Mock successful contact creation
        $this->contactMock->method('create')->willReturn(true);

        // Capture the output
        ob_start();
        $this->controller->create();
        $output = ob_get_clean();

        $this->assertJson($output);
        $response = json_decode($output, true);

        // Assert the response
        $this->assertTrue($response['success']);
        $this->assertEquals('Contact créé avec succès.', $response['message']);
    }

    public function testCreateWithInvalidData()
    {
        // Mock invalid request data
        $data = [
            'first_name' => '', // Missing required field
            'email' => 'invalid_email', // Invalid email format
        ];

        $this->requestMock->method('all')->willReturn($data);

        // Capture the output
        ob_start();
        $this->controller->create();
        $output = ob_get_clean();

        $this->assertJson($output);
        $response = json_decode($output, true);

        // Assert the response
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('errors', $response);
    }

    public function testDeleteWithValidId()
    {
        // Mock valid ID
        $id = 1;

        // Mock successful delete
        $this->contactMock->method('delete')->willReturn(true);

        // Capture the output
        ob_start();
        $this->controller->delete($id);
        $output = ob_get_clean();

        $this->assertJson($output);
        $response = json_decode($output, true);

        // Assert the response
        $this->assertTrue($response['success']);
        $this->assertEquals('Contact supprimé avec succès.', $response['message']);
    }

    public function testDeleteWithInvalidId()
    {
        // Mock invalid ID
        $id = 999;

        // Mock failed delete
        $this->contactMock->method('delete')->willReturn(false);

        // Capture the output
        ob_start();
        $this->controller->delete($id);
        $output = ob_get_clean();

        $this->assertJson($output);
        $response = json_decode($output, true);

        // Assert the response
        $this->assertFalse($response['success']);
        $this->assertEquals('Erreur lors de la suppression du contact.', $response['message']);
    }
}
