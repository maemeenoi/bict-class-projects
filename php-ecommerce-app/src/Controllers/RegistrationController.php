<?php
namespace Agora\Controllers;

use Agora\Core\AbstractController;
use Agora\Models\User;
use Agora\Models\Business;
use Agora\Models\Region;
use Agora\Views\Registration\RegistrationChoiceView;
use Agora\Views\Registration\BusinessRegistrationView;
use Agora\Views\Registration\CustomerRegistrationView;
use Agora\Core\Exceptions\InvalidDataException;

class RegistrationController extends AbstractController
{
    public function index()
    {
        return $this->process();
    }

    protected function getView($isPostback)
    {
        // For GET requests (initial page loads)
        if (!$isPostback) {
            $registrationType = $this->getFilter('registration_type');
            $step = $this->getFilter('step') ?? 1;
        }
        // For POST requests (form submissions)
        else {
            $registrationType = $this->getInput('registration_type');
            $step = $this->getInput('step') ?? 1;
        }

        // Show initial registration choice view
        if (!$registrationType) {
            error_log("Showing choice view");
            return new RegistrationChoiceView($this->getContext());
        }

        // Handle form submissions
        if ($isPostback) {
            try {
                if ($registrationType === 'business') {
                    return $this->handleBusinessRegistration($step);
                } else {
                    return $this->handleCustomerRegistration($step);
                }
            } catch (InvalidDataException $e) {
                $view = $registrationType === 'business'
                    ? new BusinessRegistrationView($this->getContext())
                    : new CustomerRegistrationView($this->getContext());
                $view->setStep($step);
                $view->setError($e->getMessage());
                return $view;
            }
        }

        // Show registration forms
        if ($registrationType === 'business') {
            $view = new BusinessRegistrationView($this->getContext());
            $view->setStep($step);
            return $view;
        } else {
            $view = new CustomerRegistrationView($this->getContext());
            $view->setStep($step);
            return $view;
        }
    }

    private function redirectToStep($type, $step, $params = [])
    {
        $params['registration_type'] = $type;
        $params['step'] = $step;
        $queryString = http_build_query($params);
        $this->redirectTo("/register?$queryString");
        return null;
    }

    private function handleBusinessRegistration($step)
    {
        switch ($step) {
            case 1: // Region selection
                $regionId = $this->getInput('region_id');
                if (!$regionId) {
                    throw new InvalidDataException('Please select a region');
                }
                return $this->redirectToStep('business', 2, ['region_id' => $regionId]);

            case 2: // Business details
                $businessData = [
                    'region_id' => $this->getInput('region_id'),
                    'business_name' => $this->getInput('business_name'),
                    'location_name' => $this->getInput('location_name'),
                    'address' => $this->getInput('address'),
                    'phone' => $this->getInput('phone'),
                    'email' => $this->getInput('email'),
                    'business_logo' => $this->getInput('business_logo')  // Added this line
                ];

                $business = new Business($this->getContext()->getDB());
                $businessId = $business->create($businessData);

                return $this->redirectToStep('business', 3, [
                    'business_id' => $businessId
                ]);

            case 3: // Admin account
                $userData = [
                    'business_id' => $this->getInput('business_id'),
                    'user_name' => $this->getInput('admin_name'),
                    'email' => $this->getInput('admin_email'),
                    'address' => $this->getInput('admin_address'),  // Added this line
                    'phone' => $this->getInput('admin_phone'),      // Added this line
                    'password' => $this->getInput('admin_password'),
                    'role' => 'Business Admin'
                ];

                $user = new User($this->getContext()->getDB());
                $user->create($userData);

                $this->redirectTo('/login', 'Business registration successful! Please login.');
                return null;
        }
    }

    private function handleCustomerRegistration($step)
    {
        switch ($step) {
            case 1: // Role and region selection
                $role = $this->getInput('role');
                $regionId = $this->getInput('region_id');

                if (!$role || !$regionId) {
                    throw new InvalidDataException('Please select both role and region');
                }

                return $this->redirectToStep('customer', 2, [
                    'role' => $role,
                    'region_id' => $regionId
                ]);

            case 2: // Business selection and user details
                $userData = [
                    'business_id' => $this->getInput('business_id'),
                    'user_name' => $this->getInput('user_name'),
                    'email' => $this->getInput('email'),
                    'address' => $this->getInput('address'),
                    'phone' => $this->getInput('phone'),
                    'password' => $this->getInput('password'),
                    'role' => $this->getInput('role')
                ];

                $user = new User($this->getContext()->getDB());
                $user->create($userData);

                $this->redirectTo('/login', 'Registration successful! Please login.');
                return null;
        }
    }

}