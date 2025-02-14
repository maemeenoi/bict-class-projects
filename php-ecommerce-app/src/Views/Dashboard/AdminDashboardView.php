<?php
namespace Agora\Views\Dashboard;

use Agora\Core\AbstractView;
use Agora\Models\Business;

class AdminDashboardView extends AbstractView
{
    private $error = null;
    private $success = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(dirname(__DIR__) . '/templates/dashboard/admin.php');
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

        $businessId = $this->getContext()->getSession()->get('business_id');
        $businessModel = new Business($this->getContext()->getDB());

        // Get business details including logo
        $sql = "SELECT business_logo FROM Business WHERE business_id = ?";
        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        $business = $stmt->fetch();

        // Get admin user details
        $userId = $this->getContext()->getSession()->get('user_id');
        $sql = "SELECT user_name FROM User WHERE user_id = ? AND role = 'Business Admin'";
        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$userId]);
        $admin = $stmt->fetch();

        if ($admin) {
            $this->setTemplateField('admin_name', $admin['user_name'] ?? '');
            $this->setTemplateField('admin_email', $admin['email'] ?? '');
            $this->setTemplateField('admin_phone', $admin['phone'] ?? '');
            $this->setTemplateField('admin_address', $admin['address'] ?? '');
            $this->setTemplateField('admin_bio', $admin['bio'] ?? '');
        } else {
            // Set default empty values if no admin data is found
            $this->setTemplateField('admin_name', '');
            $this->setTemplateField('admin_email', '');
            $this->setTemplateField('admin_phone', '');
            $this->setTemplateField('admin_address', '');
            $this->setTemplateField('admin_bio', '');
        }

        // Can't fix this it 's still happened :()
        if ($business && !empty($business['business_logo'])) {
            $this->setTemplateField('has_logo', true); 
            $this->setTemplateField('business_logo', $business['business_logo']);
        } else {
            $this->setTemplateField('has_logo', false);
            $this->setTemplateField('business_logo', '');
        }

        // Get business statistics
        $stats = $businessModel->getBusinessStats($businessId);

        // Format stats before setting them in template
        $this->setTemplateField('total_sellers', $stats['total_sellers'] ?? 0);
        $this->setTemplateField('total_products', $stats['total_products'] ?? 0);
        $this->setTemplateField('active_products', $stats['active_products'] ?? 0);
        $this->setTemplateField('total_orders', $stats['total_orders'] ?? 0);
        $this->setTemplateField('total_revenue', number_format($stats['total_revenue'] ?? 0, 2));

        // Get sellers list
        $sellers = $businessModel->getSellers($businessId);
        $this->setTemplateField('sellers', $this->formatSellers($sellers));

        // Get recent orders
        $recentOrders = $businessModel->getRecentOrders($businessId);
        $this->setTemplateField('recentOrders', $this->formatRecentOrders($recentOrders));
    }


    private function formatSellers($sellers)
    {
        if (empty($sellers)) {
            return '<p class="text-muted">No sellers found</p>';
        }

        $html = '<div class="table-responsive"><table class="table">';
        $html .= '<thead><tr>';
        $html .= '<th>Seller</th>';
        $html .= '<th>Contact</th>';
        $html .= '<th>Products</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Joined</th>';
        $html .= '<th>Actions</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($sellers as $seller) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($seller['user_name']) . '</td>';
            $html .= '<td>';
            $html .= '<div>' . htmlspecialchars($seller['email']) . '</div>';
            $html .= '<small class="text-muted">' . htmlspecialchars($seller['phone'] ?? '') . '</small>';
            $html .= '</td>';
            $html .= '<td>' . ($seller['product_count'] ?? 0) . '</td>';
            $html .= '<td>' . $this->formatStatus($seller['is_active']) . '</td>';
            $html .= '<td>' . date('M j, Y', strtotime($seller['created_at'])) . '</td>';
            $html .= '<td>';
            $html .= '<form method="post" class="d-inline">';
            $html .= '<input type="hidden" name="action" value="toggle_seller">';
            $html .= '<input type="hidden" name="seller_id" value="' . $seller['user_id'] . '">';
            $html .= '<button type="submit" class="btn btn-sm ' .
                ($seller['is_active'] ? 'btn-warning' : 'btn-success') . '">' .
                ($seller['is_active'] ? 'Deactivate' : 'Activate') . '</button>';
            $html .= '</form>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function formatRecentOrders($orders)
    {
        if (empty($orders)) {
            return '<p class="text-muted">No recent orders</p>';
        }

        $html = '<div class="table-responsive"><table class="table">';
        $html .= '<thead><tr>';
        $html .= '<th>Order ID</th>';
        $html .= '<th>Buyer</th>';
        $html .= '<th>Amount</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Date</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($orders as $order) {
            $html .= '<tr>';
            $html .= '<td>#' . $order['order_id'] . '</td>';
            $html .= '<td>' . htmlspecialchars($order['buyer_name']) . '</td>';
            $html .= '<td>$' . number_format($order['total_amount'], 2) . '</td>';
            $html .= '<td>' . $this->formatOrderStatus($order['status']) . '</td>';
            $html .= '<td>' . date('M j, Y', strtotime($order['order_date'])) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function formatStatus($isActive)
    {
        return '<span class="badge ' . ($isActive ? 'bg-success' : 'bg-danger') . '">' .
            ($isActive ? 'Active' : 'Inactive') . '</span>';
    }

    private function formatOrderStatus($status)
    {
        $statusClasses = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger'
        ];

        $class = $statusClasses[$status] ?? 'bg-secondary';
        return '<span class="badge ' . $class . '">' . ucfirst($status) . '</span>';
    }
}