<?php
namespace Agora\Controllers\Dashboard;

use Agora\Views\Dashboard\SellerDashboardView;
use Agora\Models\Product;
use Agora\Models\User;
use Agora\Models\Order;
use Agora\Core\Exceptions\InvalidDataException;
use Agora\Core\Exceptions\InvalidRequestException;

class SellerDashboardController extends DashboardController
{
    protected function getView($isPostback)
    {
        if (!$this->checkAccess(['Seller'])) {
            return null;
        }

        if ($isPostback) {
            return $this->handleAction();
        }

        $view = new SellerDashboardView($this->getContext());
        return $view;
    }

    private function handleAction()
    {
        $action = $this->getInput('action');
        $userId = $this->getContext()->getSession()->get('user_id');

        try {
            switch ($action) {
                case 'add':
                case 'update':
                case 'delete':
                    return $this->handleProductAction();

                case 'update_profile':
                    return $this->handleProfileUpdate($userId);

                case 'change_password':
                    return $this->handlePasswordChange($userId);

                case 'update_order_status':
                    return $this->handleOrderStatusUpdate();
                case 'delete_order':
                    return $this->handleOrderDelete();

                default:
                    throw new InvalidRequestException('Invalid action');
            }
        } catch (InvalidDataException $e) {
            $view = new SellerDashboardView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }

    private function handleProfileUpdate($userId)
    {
        // Validate required fields
        $userName = $this->getInput('user_name');
        $email = $this->getInput('email');

        if (!$userName || !$email) {
            throw new InvalidDataException('Name and email are required');
        }

        // Get other fields
        $data = [
            'user_id' => $userId,
            'user_name' => $userName,
            'email' => $email,
            'phone' => $this->getInput('phone'),
            'address' => $this->getInput('address'),
            'bio' => $this->getInput('bio')
        ];

        // Update profile
        $userModel = new User($this->getContext()->getDB());
        $userModel->updateProfile($data);

        // Return view with success message
        $view = new SellerDashboardView($this->getContext());
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

        $view = new SellerDashboardView($this->getContext());
        $view->setSuccess('Password updated successfully');
        return $view;
    }

    private function handleProductAction()
    {
        $action = $this->getInput('action');
        $productModel = new Product($this->getContext()->getDB());

        try {
            switch ($action) {
                case 'add':
                    return $this->addProduct($productModel);
                case 'update':
                    return $this->updateProduct($productModel);
                case 'delete':
                    return $this->deleteProduct($productModel);
                default:
                    throw new InvalidRequestException('Invalid action');
            }
        } catch (InvalidDataException $e) {
            $view = new SellerDashboardView($this->getContext());
            $view->setError($e->getMessage());
            return $view;
        }
    }

    private function addProduct($productModel)
    {
        $data = [
            'seller_id' => $this->getContext()->getSession()->get('user_id'),
            'product_name' => $this->getInput('product_name'),
            'description' => $this->getInput('description'),
            'category' => $this->getInput('category'),
            'price' => $this->getInput('price'),
            'stock_quantity' => $this->getInput('stock_quantity'),
            'status' => 'available'
        ];

        try {
            $productId = $productModel->create($data);
            $view = new SellerDashboardView($this->getContext());
            $view->setSuccess('Product added successfully');
            return $view;
        } catch (InvalidDataException $e) {
            throw new InvalidDataException('Failed to add product: ' . $e->getMessage());
        }
    }

    private function updateProduct($productModel)
    {
        $productId = $this->getInput('product_id');
        if (!$productId) {
            throw new InvalidDataException('Product ID is required');
        }

        $data = [
            'seller_id' => $this->getContext()->getSession()->get('user_id'),
            'product_name' => $this->getInput('product_name'),
            'description' => $this->getInput('description'),
            'category' => $this->getInput('category'),
            'price' => $this->getInput('price'),
            'stock_quantity' => $this->getInput('stock_quantity'),
            'status' => $this->getInput('status')
        ];

        try {
            $productModel->update($productId, $data);
            $view = new SellerDashboardView($this->getContext());
            $view->setSuccess('Product updated successfully');
            return $view;
        } catch (InvalidDataException $e) {
            throw new InvalidDataException('Failed to update product: ' . $e->getMessage());
        }
    }

    private function deleteProduct($productModel)
    {
        $productId = $this->getInput('product_id');
        if (!$productId) {
            throw new InvalidDataException('Product ID is required');
        }

        $sellerId = $this->getContext()->getSession()->get('user_id');

        try {
            $productModel->delete($productId, $sellerId);
            $view = new SellerDashboardView($this->getContext());
            $view->setSuccess('Product deleted successfully');
            return $view;
        } catch (InvalidDataException $e) {
            throw new InvalidDataException('Failed to delete product: ' . $e->getMessage());
        }
    }

    private function handleOrderStatusUpdate()
    {
        $orderId = $this->getInput('order_id');
        $status = $this->getInput('status');
        $sellerId = $this->getContext()->getSession()->get('user_id');

        if (!$orderId || !$status) {
            throw new InvalidDataException('Order ID and status are required');
        }

        $orderModel = new Order($this->getContext()->getDB());
        $orderModel->updateSellerOrderStatus($orderId, $status, $sellerId);

        $view = new SellerDashboardView($this->getContext());
        $view->setSuccess('Order status updated successfully');
        return $view;
    }

    private function handleOrderDelete()
    {
        $orderId = $this->getInput('order_id');
        $sellerId = $this->getContext()->getSession()->get('user_id');

        if (!$orderId) {
            throw new InvalidDataException('Order ID is required');
        }

        $orderModel = new Order($this->getContext()->getDB());
        $orderModel->deleteSellerOrder($orderId, $sellerId);

        $view = new SellerDashboardView($this->getContext());
        $view->setSuccess('Order deleted successfully');
        return $view;
    }


}