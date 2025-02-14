<?php
namespace Agora\Controllers;

use Agora\Core\AbstractController;
use Agora\Models\User;
use Agora\Views\Auth\LoginView;
use Agora\Core\Exceptions\InvalidDataException;

class AuthController extends AbstractController
{
    protected function getView($isPostback)
    {
        $action = $this->getURI()->getPath();

        switch ($action) {
            case 'login':
                return $this->handleLogin($isPostback);
            case 'logout':
                return $this->handleLogout();
            default:
                throw new InvalidDataException('Invalid auth action');
        }
    }

    private function handleLogin($isPostback)
    {
        if (!$isPostback) {
            return new LoginView($this->getContext());
        }

        // Handle login form submission
        try {
            $email = $this->getInput('email');
            $password = $this->getInput('password');

            if (!$email || !$password) {
                throw new InvalidDataException('Email and password are required');
            }

            $userModel = new User($this->getContext()->getDB());
            $user = $userModel->authenticate($email, $password);

            if ($user) {
                // Start session and store user data
                $session = $this->getContext()->getSession();
                $session->set('user_id', $user['user_id']);
                $session->set('user_name', $user['user_name']);
                $session->set('user_role', $user['role']);
                $session->set('business_id', $user['business_id']);

                // Redirect based on role
                switch ($user['role']) {
                    case 'Business Admin':
                        $this->redirectTo('/admin/dashboard', 'Welcome back, ' . $user['user_name']);
                        break;
                    case 'Seller':
                        $this->redirectTo('/seller/dashboard', 'Welcome back, ' . $user['user_name']);
                        break;
                    case 'Buyer':
                        $this->redirectTo('/buyer/dashboard', 'Welcome back, ' . $user['user_name']);
                        break;
                }
                return null;
            }

            throw new InvalidDataException('Invalid email or password');
        } catch (InvalidDataException $e) {
            $view = new LoginView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }

    private function handleLogout()
    {
        $this->getContext()->getSession()->destroy();
        $this->redirectTo('/', 'You have been logged out successfully');
        return null;
    }
}