<?php
namespace Agora\Controllers\Dashboard;

use Agora\Core\AbstractController;
use Agora\Core\Exceptions\InvalidRequestException;

abstract class DashboardController extends AbstractController
{
    protected function checkAccess($allowedRoles)
    {
        $session = $this->getContext()->getSession();
        if (!$session->isKeySet('user_id')) {
            $this->redirectTo('/login', 'Please login to access dashboard');
            return false;
        }

        $userRole = $session->get('user_role');
        if (!in_array($userRole, $allowedRoles)) {
            $this->redirectTo('/', 'Access denied');
            return false;
        }
        return true;
    }
}