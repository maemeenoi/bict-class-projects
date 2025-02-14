<?php
namespace Agora\Views\Dashboard;

use Agora\Core\AbstractView;
use Agora\Models\Product;

class SellerDashboardView extends AbstractView
{
    private $error = null;
    private $success = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/dashboard/seller.php');
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

        $sellerId = $this->getContext()->getSession()->get('user_id');

        // Get user and business information
        $sql = "SELECT u.*, b.business_name, b.location_name, b.address as business_address, 
                       b.phone as business_phone, b.email as business_email, b.operation_hours
                FROM User u 
                JOIN Business b ON u.business_id = b.business_id 
                WHERE u.user_id = ?";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$sellerId]);
        $profile = $stmt->fetch();

        if ($profile) {
            $this->setTemplateField('seller', $profile['user_name']);
        }

        // Set user profile fields
        $this->setTemplateField('user_name', htmlspecialchars($profile['user_name'] ?? ''));
        $this->setTemplateField('email', htmlspecialchars($profile['email'] ?? ''));
        $this->setTemplateField('phone', htmlspecialchars($profile['phone'] ?? ''));
        $this->setTemplateField('address', htmlspecialchars($profile['address'] ?? ''));
        $this->setTemplateField('bio', htmlspecialchars($profile['bio'] ?? ''));

        // Set business information fields
        $this->setTemplateField('business_name', htmlspecialchars($profile['business_name'] ?? ''));
        $this->setTemplateField('business_location', htmlspecialchars($profile['location_name'] ?? ''));
        $this->setTemplateField('business_address', htmlspecialchars($profile['business_address'] ?? ''));
        $this->setTemplateField('business_phone', htmlspecialchars($profile['business_phone'] ?? ''));
        $this->setTemplateField('business_email', htmlspecialchars($profile['business_email'] ?? ''));
        $this->setTemplateField('business_hours', htmlspecialchars($profile['operation_hours'] ?? ''));

        // Get seller's statistics
        $sql = "SELECT 
                    COUNT(DISTINCT p.product_id) as total_products,
                    SUM(CASE WHEN p.status = 'available' THEN 1 ELSE 0 END) as active_products,
                    COUNT(DISTINCT o.order_id) as total_orders,
                    COALESCE(SUM(op.total_price), 0) as total_sales
                FROM Product p
                LEFT JOIN Order_Products op ON p.product_id = op.product_id
                LEFT JOIN Orders o ON op.order_id = o.order_id
                WHERE p.seller_id = ?";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$sellerId]);
        $stats = $stmt->fetch();

        // Set statistics fields
        $this->setTemplateField('total_products', $stats['total_products'] ?? 0);
        $this->setTemplateField('active_products', $stats['active_products'] ?? 0);
        $this->setTemplateField('total_orders', $stats['total_orders'] ?? 0);
        $this->setTemplateField('total_sales', number_format($stats['total_sales'] ?? 0, 2));

        // Get seller's products
        $sql = "SELECT 
                    p.*,
                    COALESCE(COUNT(DISTINCT o.order_id), 0) as times_ordered,
                    COALESCE(SUM(op.quantity), 0) as total_quantity_sold
                FROM Product p
                LEFT JOIN Order_Products op ON p.product_id = op.product_id
                LEFT JOIN Orders o ON op.order_id = o.order_id
                WHERE p.seller_id = ?
                GROUP BY p.product_id
                ORDER BY p.created_at DESC";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$sellerId]);
        $products = $stmt->fetchAll();

        // Format and set products
        $this->setTemplateField('products', $this->formatProducts($products));

        // Format categories as HTML options for Add/Edit product modals
        $categoriesHtml = $this->formatCategories([
            'Electronics',
            'Fashion',
            'Home & Garden',
            'Sports',
            'Books',
            'Toys',
            'Health & Beauty',
            'Automotive',
            'Other'
        ]);
        $this->setTemplateField('categories', $categoriesHtml);

        // Add product modals HTML
        $this->setTemplateField('modals', $this->getModalsHtml());

        // Get seller's orders
        $sellerId = $this->getContext()->getSession()->get('user_id');
        $sql = "SELECT o.*, op.quantity, op.total_price, p.product_name, u.user_name as buyer_name
            FROM Orders o
            JOIN Order_Products op ON o.order_id = op.order_id
            JOIN Product p ON op.product_id = p.product_id
            JOIN User u ON o.buyer_id = u.user_id
            WHERE p.seller_id = ?
            ORDER BY o.order_date DESC";

        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$sellerId]);
        $orders = $stmt->fetchAll();

        $this->setTemplateField('orders', $this->formatOrders($orders));
    }

    private function formatProducts($products)
    {
        if (empty($products)) {
            return '<div class="alert alert-info">No products found. Add your first product using the button above.</div>';
        }

        $html = '<div class="table-responsive"><table class="table">
                <thead><tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th>Sales</th>
                    <th>Actions</th>
                </tr></thead><tbody>';

        foreach ($products as $product) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($product['product_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($product['category'] ?? 'N/A') . '</td>';
            $html .= '<td>$' . number_format($product['price'], 2) . '</td>';
            $html .= '<td>' . $this->formatStatus($product['status']) . '</td>';
            $html .= '<td>' . $product['stock_quantity'] . '</td>';
            $html .= '<td>' . $product['total_quantity_sold'] . ' units<br><small class="text-muted">' .
                $product['times_ordered'] . ' orders</small></td>';
            $html .= '<td>
                        <button class="btn btn-sm btn-primary me-1" 
                                onclick="editProduct(' . htmlspecialchars(json_encode($product)) . ')">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteProduct(' . $product['product_id'] . ')">
                            <i class="bi bi-trash"></i>
                        </button>
                      </td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function formatCategories($categories)
    {
        $html = '';
        foreach ($categories as $category) {
            $html .= sprintf(
                '<option value="%s">%s</option>',
                htmlspecialchars($category),
                htmlspecialchars($category)
            );
        }
        return $html;
    }

    private function formatStatus($status)
    {
        $statusClasses = [
            'available' => 'badge bg-success',
            'out_of_stock' => 'badge bg-warning',
            'discontinued' => 'badge bg-danger'
        ];

        $class = $statusClasses[$status] ?? 'badge bg-secondary';
        return '<span class="' . $class . '">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
    }

    private function getModalsHtml()
    {
        $html = $this->getAddProductModalHtml();
        $html .= $this->getEditProductModalHtml();
        $html .= $this->getDeleteProductModalHtml();
        return $html;
    }

    private function getAddProductModalHtml()
    {
        // Add Product Modal HTML
        return '<!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="##site##/seller/dashboard">
                        <input type="hidden" name="action" value="add">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Product form fields -->
                            <div class="mb-3">
                                <label class="form-label">Product Name*</label>
                                <input type="text" name="product_name" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">Select Category</option>
                                    ##categories##
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price*</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control" required min="0" step="0.01">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stock Quantity*</label>
                                <input type="number" name="stock_quantity" class="form-control" required min="0" value="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
    }

    private function getEditProductModalHtml()
    {
        // Edit Product Modal HTML similar to Add Product Modal
        return '<!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1">
            <!-- Similar structure to Add Product Modal with edit-specific fields -->
        </div>';
    }

    private function getDeleteProductModalHtml()
    {
        // Delete Confirmation Modal HTML
        return '<!-- Delete Product Modal -->
        <div class="modal fade" id="deleteProductModal" tabindex="-1">
            <!-- Delete confirmation dialog -->
        </div>';
    }

    private function formatOrders($orders)
    {
        if (empty($orders)) {
            return '<tr><td colspan="8" class="text-center">No orders found</td></tr>';
        }

        $html = '';
        foreach ($orders as $order) {
            $statusClass = $this->getStatusClass($order['status']);

            $html .= '
        <tr data-status="' . $order['status'] . '">
            <td>#' . $order['order_id'] . '</td>
            <td>' . htmlspecialchars($order['product_name']) . '</td>
            <td>' . htmlspecialchars($order['buyer_name']) . '</td>
            <td>' . $order['quantity'] . '</td>
            <td>$' . number_format($order['total_price'], 2) . '</td>
            <td><span class="badge ' . $statusClass . '">' . ucfirst($order['status']) . '</span></td>
            <td>' . date('M j, Y', strtotime($order['order_date'])) . '</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="updateOrderStatus(' . $order['order_id'] . ')">
                        Update Status
                    </button>
                    ' . ($order['status'] === 'cancelled' ? '
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="deleteOrder(' . $order['order_id'] . ')">
                        Delete
                    </button>
                    ' : '') . '
                </div>
            </td>
        </tr>';
        }
        return $html;
    }

    private function getStatusClass($status)
    {
        $classes = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger'
        ];
        return $classes[$status] ?? 'bg-secondary';
    }
}