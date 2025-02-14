<?php
namespace Agora\Models;

use Agora\Core\AbstractModel;
use Agora\Core\Exceptions\InvalidDataException;

class Product extends AbstractModel
{
    public function create($data)
    {
        // Validate required fields
        $error = self::errorInRequiredField('Product Name', $data['product_name'], 100);
        self::assertNoError($error);

        $error = self::errorInRequiredNumericField('Price', $data['price'], 2, 0);
        self::assertNoError($error);

        $sql = "INSERT INTO Product (seller_id, product_name, description, category, 
                                   price, status, stock_quantity) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $data['seller_id'],
            $data['product_name'],
            $data['description'] ?? null,
            $data['category'] ?? null,
            $data['price'],
            $data['status'] ?? 'available',
            $data['stock_quantity'] ?? 0
        ]);

        return $this->getDB()->lastInsertId();
    }

    public function update($productId, $data)
    {
        // Check product exists and seller owns it
        $this->verifyOwnership($productId, $data['seller_id']);

        $sql = "UPDATE Product 
                SET product_name = ?, description = ?, category = ?, 
                    price = ?, status = ?, stock_quantity = ? 
                WHERE product_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $data['product_name'],
            $data['description'] ?? null,
            $data['category'] ?? null,
            $data['price'],
            $data['status'],
            $data['stock_quantity'],
            $productId
        ]);
    }

    public function delete($productId, $sellerId)
    {
        $this->verifyOwnership($productId, $sellerId);

        $sql = "DELETE FROM Product WHERE product_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$productId]);
    }

    public function getSellerProducts($sellerId)
    {
        $sql = "SELECT * FROM Product WHERE seller_id = ? ORDER BY created_at DESC";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    private function verifyOwnership($productId, $sellerId)
    {
        $sql = "SELECT COUNT(*) as count FROM Product 
                WHERE product_id = ? AND seller_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$productId, $sellerId]);
        $result = $stmt->fetch();

        if ($result['count'] == 0) {
            throw new InvalidDataException('Product not found or access denied');
        }
    }

    public function getById($productId)
    {
        $sql = "SELECT * FROM Product WHERE product_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
    public function getDetailedProduct($productId)
    {
        $sql = "SELECT p.*, 
            u.user_name as seller_name,
            b.business_name,
            (SELECT COUNT(*) FROM Order_Products op 
             JOIN Orders o ON op.order_id = o.order_id 
             WHERE op.product_id = p.product_id) as times_ordered
            FROM Product p
            JOIN User u ON p.seller_id = u.user_id
            JOIN Business b ON u.business_id = b.business_id
            WHERE p.product_id = ? 
            AND p.status = 'available'
            AND u.is_active = 1";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
}