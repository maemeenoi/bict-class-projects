<?php
namespace Agora\Controllers;

use Agora\Core\AbstractController;
use Agora\Models\Product;
use Agora\Views\Product\ProductView;
use Agora\Core\Exceptions\InvalidDataException;

class ProductController extends AbstractController
{
    protected function getView($isPostback)
    {
        // Get product ID from route parameters
        $productId = $this->getURI()->getParam(0);

        if (!$productId || !is_numeric($productId)) {
            throw new InvalidDataException('Invalid product ID');
        }

        return $this->showProduct($productId);
    }

    private function showProduct($productId)
    {
        // Check if user is logged in
        $session = $this->getContext()->getSession();
        if (!$session->isKeySet('user_id')) {
            $this->redirectTo('/login', 'Please login to view product details');
            return null;
        }

        // Get product details
        $productModel = new Product($this->getContext()->getDB());
        $product = $productModel->getDetailedProduct($productId);

        if (!$product) {
            throw new InvalidDataException('Product not found');
        }

        // Create and return the view
        $view = new ProductView($this->getContext());
        $view->setModel($product);
        return $view;
    }
}