<?php

namespace Agora\Controllers\Dashboard;

use Agora\Views\Dashboard\AdminDashboardView;
use Agora\Models\Business;
use Agora\Models\User;
use Agora\Core\Exceptions\InvalidDataException;

class AdminDashboardController extends DashboardController
{
    protected function getView($isPostback)
    {
        if (!$this->checkAccess(['Business Admin'])) {
            return null;
        }

        if ($isPostback) {
            return $this->handleAction();
        }

        $view = new AdminDashboardView($this->getContext());
        return $view;
    }

    private function handleAction()
    {
        $action = $this->getInput('action');
        $businessId = $this->getContext()->getSession()->get('business_id');
        $adminId = $this->getContext()->getSession()->get('user_id');    
        $businessModel = new Business($this->getContext()->getDB());

        try {
            switch ($action) {
                case 'toggle_seller':
                    return $this->handleToggleSeller($businessModel, $businessId);

                case 'upload_logo':
                    return $this->handleLogoUpload($businessModel, $businessId);

                case 'add_user':
                    return $this->handleAddUser($businessModel, $businessId);
                    
                case 'update_profile':
                    return $this->handleProfileUpdate($adminId);

                default:
                    throw new InvalidDataException('Invalid action');
            }
        } catch (InvalidDataException $e) {
            $view = new AdminDashboardView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }   

    private function handleLogoUpload($businessModel, $businessId)
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidDataException('Please select a valid image file');
        }

        $file = $_FILES['logo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            throw new InvalidDataException('Invalid file type. Please upload a JPG, PNG or GIF');
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'business_' . $businessId . '_' . time() . '.' . $ext;
        $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/uploads/logos/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $logoPath = '/uploads/logos/' . $filename;
            $businessModel->updateLogo($businessId, $logoPath);
            $this->redirectTo('/admin/dashboard', 'Logo updated successfully');
            return null;
        }

        throw new InvalidDataException('Failed to upload logo');
    }

    private function handleProfileUpdate($adminId)
    {
        // Validate required fields
        $userName = $this->getInput('user_name');
        $email = $this->getInput('email');

        if (!$userName || !$email) {
            throw new InvalidDataException('Name and email are required');
        }

        // Get other fields
        $data = [
            'user_id' => $adminId,
            'user_name' => $userName,
            'email' => $email,
            'phone' => $this->getInput('phone'),
            'address' => $this->getInput('address'),
            'bio' => $this->getInput('bio')
        ];

        try {
            // Create User model instance
            $userModel = new User($this->getContext()->getDB());
            
            // Update profile
            $userModel->updateProfile($data);

            // Return view with success message
            $view = new AdminDashboardView($this->getContext());
            $view->setSuccess('Profile updated successfully');
            return $view;
        } catch (InvalidDataException $e) {
            $view = new AdminDashboardView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }

    private function handleAddUser($businessModel, $businessId)
    {
        $data = [
            'user_name' => $this->getInput('user_name'),
            'email' => $this->getInput('email'),
            'password' => $this->getInput('password'),
            'role' => $this->getInput('role'),
            'phone' => $this->getInput('phone'),
            'address' => $this->getInput('address'),
        ];

        $businessModel->addUserToBusiness($businessId, $data);
        $this->redirectTo('/admin/dashboard', 'User added successfully');
        return null;
    }

    private function handleToggleSeller($businessModel, $businessId)
    {
        $sellerId = $this->getInput('seller_id');
        if (!$sellerId) {
            throw new InvalidDataException('Seller ID is required');
        }
        $businessModel->toggleSellerStatus($sellerId, $businessId);
        $this->redirectTo('/admin/dashboard', 'Seller status updated successfully');
        return null;
    }
}