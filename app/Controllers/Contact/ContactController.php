<?php

namespace App\Controllers\Contact;

use App\Models\Contact;
use App\Validators\ContactValidator;
use App\Utils\CorsHandler;
use App\Utils\Request;

class ContactController
{
    private $contactModel;

    public function __construct()
    {  
        $this->contactModel = new Contact();
        CorsHandler::handle(); // Gestion globale des en-têtes CORS
    }

    // Retourne la liste des contacts
    public function index()
    {
        try {
            $contacts = $this->contactModel->read();
            echo json_encode([
                'success' => true,
                'data' => $contacts
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Crée un nouveau contact
    public function create()
    {  
        
        $request = new Request(); // Initialize the request object
        $data = $request->all(); // Get all input data

        try {
            // Validation des données
            $errors = ContactValidator::validate($data);

            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'errors' => $errors
                ]);
                return;
            }

            // Préparer les données
            $contact = $this->contactModel;
            $contact->first_name = $data['first_name'];
            $contact->last_name = $data['last_name'];
            $contact->age = $data['age'] ?? null;
            $contact->country = $data['country'] ?? '';
            $contact->email = $data['email'];
            $contact->phone_number = $data['phone_number'];

            if ($contact->create()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contact créé avec succès.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création du contact.'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Met à jour un contact
    public function update($id)
    {
        $request = new \App\Utils\Request(); // Initialize the request object
        $data = $request->all(); // Get all input data
    
        try {
            // Validate data for update (allowing partial updates)
            $errors = ContactValidator::validate($data, true);
    
            if (!empty($errors)) {
                // Return validation errors
                echo json_encode([
                    'success' => false,
                    'errors' => $errors
                ]);
                return;
            }
    
            // Fetch the existing contact data (optional if needed)
            $existingContact = $this->contactModel->findById($id);
            if (!$existingContact) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact introuvable.'
                ]);
                return;
            }
    
            // Prepare and update contact data
            $contact = $this->contactModel;
            $contact->id = $id;
            $contact->first_name = $data['first_name'] ?? $existingContact['first_name'];
            $contact->last_name = $data['last_name'] ?? $existingContact['last_name'];
            $contact->age = $data['age'] ?? $existingContact['age'];
            $contact->country = $data['country'] ?? $existingContact['country'];
            $contact->email = $data['email'] ?? $existingContact['email'];
            $contact->phone_number = $data['phone_number'] ?? $existingContact['phone_number'];
    
            if ($contact->update()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contact mis à jour avec succès.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du contact.'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ]);
        }
    }
    

    // Supprime un contact
    public function delete($id)
    {
        try {
            $contact = $this->contactModel;
            $contact->id = $id;

            if ($contact->delete()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contact supprimé avec succès.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression du contact.'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
