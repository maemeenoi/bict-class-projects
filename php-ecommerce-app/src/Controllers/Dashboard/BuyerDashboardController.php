<?php
namespace Agora\Controllers\Dashboard;

use Agora\Views\Dashboard\BuyerDashboardView;
use Agora\Models\User;
use Agora\Models\Product;
use Agora\Models\Order;
use Agora\Core\Exceptions\InvalidDataException;
use Agora\Core\Exceptions\InvalidRequestException;

class BuyerDashboardController extends DashboardController
{
    protected function getView($isPostback)
    {
        if (!$this->checkAccess(['Buyer'])) {
            return null;
        }

        if ($isPostback) {
            return $this->handleAction();
        }

        $view = new BuyerDashboardView($this->getContext());
        return $view;
    }

    private function handleAction()
    {
        $action = $this->getInput('action');
        $userId = $this->getContext()->getSession()->get('user_id');

        try {
            switch ($action) {
                case 'update_profile':
                    return $this->handleProfileUpdate($userId);
                case 'change_password':
                    return $this->handlePasswordChange($userId);
                case 'buy_now':
                    return $this->handlePurchase($userId);
                default:
                    throw new InvalidRequestException('Invalid action');
            }
        } catch (InvalidDataException $e) {
            $view = new BuyerDashboardView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }

    private function handlePurchase($userId)
    {
        $productId = $this->getInput('product_id');
        $quantity = (int) ($this->getInput('quantity') ?? 1);

        if (!$productId) {
            throw new InvalidDataException('Product ID is required');
        }

        // Get product details
        $productModel = new Product($this->getContext()->getDB());
        $product = $productModel->getById($productId);

        if (!$product) {
            throw new InvalidDataException('Product not found');
        }

        // Get buyer's business ID from session
        $businessId = $this->getContext()->getSession()->get('business_id');

        // Calculate total amount
        $totalAmount = $product['price'] * $quantity;

        // Create order data
        $orderData = [
            'buyer_id' => $userId,
            'business_id' => $businessId,
            'total_amount' => $totalAmount,
            'shipping_address' => $this->getInput('shipping_address'),
            'notes' => $this->getInput('notes'),
            'products' => [
                [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product['price']
                ]
            ]
        ];

        // Create order
        $orderModel = new Order($this->getContext()->getDB());
        $orderId = $orderModel->create($orderData);

        // Return view with success message
        $view = new BuyerDashboardView($this->getContext());
        $view->setSuccess('Order placed successfully! Order #' . $orderId . ' has been created.');
        return $view;
    }

    private function handleProfileUpdate($userId)
    {
        $userName = $this->getInput('user_name');
        $email = $this->getInput('email');

        if (!$userName || !$email) {
            throw new InvalidDataException('Name and email are required');
        }

        $data = [
            'user_id' => $userId,
            'user_name' => $userName,
            'email' => $email,
            'phone' => $this->getInput('phone'),
            'address' => $this->getInput('address')
        ];

        $userModel = new User($this->getContext()->getDB());
        $userModel->updateProfile($data);

        $view = new BuyerDashboardView($this->getContext());
        $view->setSuccess('Profile updated successfully');
        return $view;
    }

    private function handlePasswordChange($userId)
    {
        $currentPassword = $this->getInput('current_password');
        $newPassword = $this->getInput('new_password');
        $confirmPassword = $this->getInput('confirm_password');

        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            throw new InvalidDataException('All password fields are required');
        }

        if ($newPassword !== $confirmPassword) {
            throw new InvalidDataException('New passwords do not match');
        }

        if (strlen($newPassword) < 8) {
            throw new InvalidDataException('Password must be at least 8 characters long');
        }

        $userModel = new User($this->getContext()->getDB());
        $userModel->updatePassword($userId, $currentPassword, $newPassword);

        $view = new BuyerDashboardView($this->getContext());
        $view->setSuccess('Password updated successfully');
        return $view;
    }
}