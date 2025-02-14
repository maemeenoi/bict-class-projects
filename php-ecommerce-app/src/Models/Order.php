<?php
namespace Agora\Models;

use Agora\Core\AbstractModel;
use Agora\Core\Exceptions\InvalidDataException;

class Order extends AbstractModel
{
    public function create($data)
    {
        // Validate required fields
        if (!isset($data['buyer_id'], $data['business_id'], $data['total_amount'])) {
            throw new InvalidDataException('Missing required order data');
        }

        if (!isset($data['products']) || empty($data['products'])) {
            throw new InvalidDataException('Order must contain at least one product');
        }

        // Start transaction
        $this->getDB()->execute('START TRANSACTION');

        try {
            // Create order
            $sql = "INSERT INTO Orders (buyer_id, business_id, total_amount, shipping_address, notes, status) 
                    VALUES (?, ?, ?, ?, ?, 'pending')";

            $stmt = $this->getDB()->prepare($sql);
            $stmt->execute([
                $data['buyer_id'],
                $data['business_id'],
                $data['total_amount'],
                $data['shipping_address'],
                $data['notes'] ?? null
            ]);

            $orderId = $this->getDB()->lastInsertId();

            // Add order products
            foreach ($data['products'] as $product) {
                $sql = "INSERT INTO Order_Products (order_id, product_id, quantity, unit_price) 
                        VALUES (?, ?, ?, ?)";

                $stmt = $this->getDB()->prepare($sql);
                $stmt->execute([
                    $orderId,
                    $product['product_id'],
                    $product['quantity'],
                    $product['unit_price']
                ]);

                // Update product stock
                $sql = "UPDATE Product 
                        SET stock_quantity = stock_quantity - ?,
                            status = CASE 
                                WHEN stock_quantity - ? <= 0 THEN 'out_of_stock'
                                ELSE status 
                            END
                        WHERE product_id = ?";

                $stmt = $this->getDB()->prepare($sql);
                $stmt->execute([
                    $product['quantity'],
                    $product['quantity'],
                    $product['product_id']
                ]);
            }

            // Commit transaction
            $this->getDB()->execute('COMMIT');
            return $orderId;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->getDB()->execute('ROLLBACK');
            throw new InvalidDataException('Failed to create order: ' . $e->getMessage());
        }
    }

    public function getById($orderId)
    {
        $sql = "SELECT o.*, u.user_name as buyer_name, b.business_name
                FROM Orders o
                JOIN User u ON o.buyer_id = u.user_id
                JOIN Business b ON o.business_id = b.business_id
                WHERE o.order_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if ($order) {
            // Get order products
            $order['products'] = $this->getOrderProducts($orderId);
        }

        return $order;
    }

    public function getOrderProducts($orderId)
    {
        $sql = "SELECT op.*, p.product_name, p.description, p.category
                FROM Order_Products op
                JOIN Product p ON op.product_id = p.product_id
                WHERE op.order_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getBuyerOrders($buyerId, $limit = null)
    {
        $sql = "SELECT o.*, 
                    COUNT(op.order_product_id) as total_items,
                    b.business_name
                FROM Orders o
                JOIN Business b ON o.business_id = b.business_id
                LEFT JOIN Order_Products op ON o.order_id = op.order_id
                WHERE o.buyer_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    public function getBusinessOrders($businessId, $limit = null)
    {
        $sql = "SELECT o.*, 
                    u.user_name as buyer_name,
                    COUNT(op.order_product_id) as total_items
                FROM Orders o
                JOIN User u ON o.buyer_id = u.user_id
                LEFT JOIN Order_Products op ON o.order_id = op.order_id
                WHERE o.business_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($orderId, $status, $businessId)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidDataException('Invalid order status');
        }

        $sql = "UPDATE Orders 
                SET status = ?
                WHERE order_id = ? AND business_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$status, $orderId, $businessId]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidDataException('Order not found or access denied');
        }
    }

    public function cancelOrder($orderId, $buyerId)
    {
        // Start transaction
        $this->getDB()->execute('START TRANSACTION');

        try {
            // Check if order belongs to buyer and is cancellable
            $sql = "SELECT status FROM Orders 
                    WHERE order_id = ? AND buyer_id = ? 
                    AND status IN ('pending', 'processing')";

            $stmt = $this->getDB()->prepare($sql);
            $stmt->execute([$orderId, $buyerId]);

            if ($stmt->rowCount() === 0) {
                throw new InvalidDataException('Order cannot be cancelled');
            }

            // Get order products
            $products = $this->getOrderProducts($orderId);

            // Restore product stock
            foreach ($products as $product) {
                $sql = "UPDATE Product 
                        SET stock_quantity = stock_quantity + ?,
                            status = CASE 
                                WHEN status = 'out_of_stock' THEN 'available'
                                ELSE status 
                            END
                        WHERE product_id = ?";

                $stmt = $this->getDB()->prepare($sql);
                $stmt->execute([$product['quantity'], $product['product_id']]);
            }

            // Update order status
            $sql = "UPDATE Orders SET status = 'cancelled' WHERE order_id = ?";
            $stmt = $this->getDB()->prepare($sql);
            $stmt->execute([$orderId]);

            // Commit transaction
            $this->getDB()->execute('COMMIT');
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->getDB()->execute('ROLLBACK');
            throw new InvalidDataException('Failed to cancel order: ' . $e->getMessage());
        }
    }

    public function getBuyerOrderStats($buyerId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(total_amount) as total_spent
                FROM Orders 
                WHERE buyer_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$buyerId]);
        return $stmt->fetch();
    }

    public function updateSellerOrderStatus($orderId, $status, $sellerId)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidDataException('Invalid order status');
        }

        $sql = "UPDATE Orders o
            JOIN Order_Products op ON o.order_id = op.order_id
            JOIN Product p ON op.product_id = p.product_id
            SET o.status = ?
            WHERE o.order_id = ? AND p.seller_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$status, $orderId, $sellerId]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidDataException('Order not found or access denied');
        }
    }

    public function deleteSellerOrder($orderId, $sellerId)
    {
        // Only allow deletion of cancelled orders
        $sql = "DELETE o FROM Orders o
            JOIN Order_Products op ON o.order_id = op.order_id
            JOIN Product p ON op.product_id = p.product_id
            WHERE o.order_id = ? AND p.seller_id = ? 
            AND o.status = 'cancelled'";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$orderId, $sellerId]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidDataException('Order cannot be deleted (must be cancelled first)');
        }
    }
}