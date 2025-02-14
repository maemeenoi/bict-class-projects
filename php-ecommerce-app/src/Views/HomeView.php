<?php
namespace Agora\Views;

use Agora\Core\AbstractView;

class HomeView extends AbstractView
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/templates/home.php');
    }

    public function prepare()
    {
        // Get session info
        $session = $this->getContext()->getSession();
        $isLoggedIn = $session->isKeySet('user_id');

        // Set login status for template
        $this->setTemplateField('isLoggedIn', $isLoggedIn);

        if ($isLoggedIn) {
            $this->setTemplateField('userName', $session->get('user_name'));
            // Convert role to lowercase for URL purposes
            $role = strtolower($session->get('user_role'));
            $role = str_replace(' ', '-', $role);
            $this->setTemplateField('userRole', $role);
        }

        // Get recent products
        $db = $this->getContext()->getDB();
        $sql = "SELECT p.*, u.business_id, b.business_name 
                FROM Product p 
                JOIN User u ON p.seller_id = u.user_id 
                JOIN Business b ON u.business_id = b.business_id 
                WHERE p.status = 'available' 
                AND u.is_active = 1
                ORDER BY p.created_at DESC 
                LIMIT 8";

        $products = $db->query($sql);
        $this->setTemplateField('products', $this->formatProducts($products, $isLoggedIn));
    }

    private function formatProducts($products, $isLoggedIn)
    {
        $html = '<div class="row g-4">';

        // Get user role from session if logged in
        $userRole = '';
        if ($isLoggedIn) {
            $session = $this->getContext()->getSession();
            $userRole = $session->get('user_role');
        }

        foreach ($products as $product) {
            $html .= '
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($product['product_name']) . '</h5>
                    <p class="card-text">' . htmlspecialchars($product['description'] ?? 'No description available.') . '</p>
                    <p class="card-text">
                        <strong>$' . number_format($product['price'], 2) . '</strong><br>
                        <small class="text-muted">Sold by: ' . htmlspecialchars($product['business_name']) . '</small>
                    </p>';

            // Show different buttons based on login status and role
            if (!$isLoggedIn) {
                $html .= '<a href="' . $this->getContext()->getURI()->getSite() . '/login" 
                        class="btn btn-outline-primary w-100">Login to Purchase</a>';
            } else if ($userRole === 'Buyer') {
                $html .= '<a href="' . $this->getContext()->getURI()->getSite() . '/product/' . $product['product_id'] . '" 
                        class="btn btn-primary w-100">View Details</a>';
            } else {
                // For sellers and admins, show a disabled button or message
                $html .= '<button class="btn btn-secondary w-100" disabled>Buyers Only</button>';
            }

            $html .= '
                </div>
            </div>
        </div>';
        }
        $html .= '</div>';
        return $html;
    }
}