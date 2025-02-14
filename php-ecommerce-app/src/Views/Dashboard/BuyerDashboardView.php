<?php
namespace Agora\Views\Dashboard;

use Agora\Core\AbstractView;
use Agora\Models\Product;
use Agora\Models\Order;

class BuyerDashboardView extends AbstractView
{
    private $error = null;
    private $success = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/dashboard/buyer.php');
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function setSuccess($message)
    {
        $this->success = $message;
    }

    public function prepare()
    {
        if ($this->error) {
            $this->setTemplateField('error', $this->error);
        }

        if ($this->success) {
            $this->setTemplateField('success', $this->success);
        }

        $buyerId = $this->getContext()->getSession()->get('user_id');
        $businessId = $this->getContext()->getSession()->get('business_id');

        // Get user and business information
        $sql = "SELECT u.*, b.business_name, b.location_name 
                FROM User u 
                JOIN Business b ON u.business_id = b.business_id 
                WHERE u.user_id = ?";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$buyerId]);
        $profile = $stmt->fetch();



        // Set profile information
        $this->setTemplateField('buyer_name', htmlspecialchars($profile['user_name']));
        $this->setTemplateField('business_name', htmlspecialchars($profile['business_name']));
        $this->setTemplateField('location_name', htmlspecialchars($profile['location_name']));
        $this->setTemplateField('email', htmlspecialchars($profile['email']));
        $this->setTemplateField('phone', htmlspecialchars($profile['phone'] ?? ''));
        $this->setTemplateField('address', htmlspecialchars($profile['address'] ?? ''));

        // Get available products
        $sql = "SELECT p.*, u.business_id, b.business_name, u.user_name as seller_name
                FROM Product p 
                JOIN User u ON p.seller_id = u.user_id 
                JOIN Business b ON u.business_id = b.business_id 
                WHERE p.status = 'available' 
                AND u.business_id = ? 
                AND u.is_active = 1
                ORDER BY p.created_at DESC";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        $products = $stmt->fetchAll();

        // Format and set products
        $this->setTemplateField('products', $this->formatProducts($products));

        // Get recent orders
        $sql = "SELECT o.*, 
                COUNT(op.order_product_id) as total_items,
                GROUP_CONCAT(p.product_name SEPARATOR ', ') as products_list
                FROM Orders o
                JOIN Order_Products op ON o.order_id = op.order_id
                JOIN Product p ON op.product_id = p.product_id
                WHERE o.buyer_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT 5";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$buyerId]);
        $orders = $stmt->fetchAll();

        // Format and set orders
        $this->setTemplateField('recent_orders', $this->formatOrders($orders));
    }

    private function formatProducts($products)
    {
        if (empty($products)) {
            return '<div class="alert alert-info">No products available at the moment.</div>';
        }

        $html = '';
        foreach ($products as $product) {
            $html .= '
        <div class="col-md-4 product-card" data-price="' . $product['price'] . '" data-date="' . $product['created_at'] . '">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($product['product_name']) . '</h5>
                    <p class="card-text">' . htmlspecialchars(substr($product['description'] ?? '', 0, 100)) . '...</p>
                    <div class="mb-3">
                        <span class="badge bg-primary">' . htmlspecialchars($product['category'] ?? 'Uncategorized') . '</span>
                        <span class="badge bg-success">$' . number_format($product['price'], 2) . '</span>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-shop"></i> ' . htmlspecialchars($product['seller_name']) . '<br>
                        <i class="bi bi-box"></i> ' . $product['stock_quantity'] . ' in stock
                    </div>
                    <div class="d-grid gap-2">
                        <a href="' . $this->getContext()->getURI()->getSite() . '/product/' . $product['product_id'] . '" 
                           class="btn btn-outline-primary">View Details</a>
                        <button type="button" class="btn btn-primary" 
                                onclick="showPurchaseModal(' . htmlspecialchars(json_encode($product)) . ')">
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>';
        }
        return $html;
    }

    private function formatOrders($orders)
    {
        if (empty($orders)) {
            return '<div class="alert alert-info">No orders found.</div>';
        }

        $html = '<div class="table-responsive"><table class="table">';
        $html .= '<thead><tr>';
        $html .= '<th>Order ID</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Items</th>';
        $html .= '<th>Total</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($orders as $order) {
            $html .= '<tr>';
            $html .= '<td>#' . $order['order_id'] . '</td>';
            $html .= '<td>' . date('M j, Y', strtotime($order['order_date'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($order['products_list']) . '</td>';
            $html .= '<td>$' . number_format($order['total_amount'], 2) . '</td>';
            $html .= '<td>' . $this->formatStatus($order['status']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function formatStatus($status)
    {
        $statusClasses = [
            'pending' => 'badge bg-warning',
            'processing' => 'badge bg-info',
            'shipped' => 'badge bg-primary',
            'delivered' => 'badge bg-success',
            'cancelled' => 'badge bg-danger'
        ];

        $class = $statusClasses[$status] ?? 'badge bg-secondary';
        return '<span class="' . $class . '">' . ucfirst($status) . '</span>';
    }
}