<?php

namespace App\Validators;

use App\Config\Database;

class ContactValidator {
    
    public static function validate($data, $isUpdate = false) {
        $errors = [];

        // Get the database connection
        $database = new Database();
        $db = $database->getConnection();

        // Validation for CREATE or UPDATE
        if (!$isUpdate) {
            // Required fields for CREATE
            if (empty($data['first_name'])) {
                $errors[] = 'Le prénom est obligatoire.';
            }

            if (empty($data['last_name'])) {
                $errors[] = 'Le nom est obligatoire.';
            }

            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Un email valide est obligatoire.';
            } else {
                // Check for duplicate email in the database
                $query = "SELECT id FROM contacts WHERE email = :email";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':email', $data['email']);
                $stmt->execute();
                $result = $stmt->fetch();

                if ($result) {
                    $errors[] = 'Cet email est déjà utilisé.';
                }
            }

            if (empty($data['country'])) {
                $errors[] = 'Le champ pays est obligatoire.';
            }

            if (empty($data['phone_number']) || !preg_match('/^[0-9\-\+]{9,15}$/', $data['phone_number'])) {
                $errors[] = 'Un numéro de téléphone valide est requis.';
            }
        }

        // Shared validation for both CREATE and UPDATE
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Un email valide est obligatoire.';
        }

        if (!empty($data['age']) && (!is_numeric($data['age']) || $data['age'] <= 0)) {
            $errors[] = "L'âge doit être un nombre positif.";
        }

        if (!empty($data['phone_number']) && !preg_match('/^[0-9\-\+]{9,15}$/', $data['phone_number'])) {
            $errors[] = 'Un numéro de téléphone valide est requis.';
        }

        return $errors;
    }
}
