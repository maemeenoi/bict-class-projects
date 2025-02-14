<?php
namespace Agora\Models;

use Agora\Core\AbstractModel;
use Agora\Core\Exceptions\InvalidDataException;

class Business extends AbstractModel
{
    private $businessId;
    private $regionId;
    private $businessName;
    private $locationName;
    private $address;
    private $phone;
    private $email;
    private $businessLogo;
    private $operationHours;
    private $isActive;

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function create($data)
    {
        // Validate required fields
        $error = self::errorInRequiredField('Business Name', $data['business_name'], 100);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Location Name', $data['location_name'], 100);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Address', $data['address'], 255);
        self::assertNoError($error);

        $sql = "INSERT INTO Business (region_id, business_name, location_name, address, phone, email, business_logo, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $data['region_id'],
            $data['business_name'],
            $data['location_name'],
            $data['address'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['business_logo'] ?? null
        ]);

        return $this->getDB()->lastInsertId();
    }
    public function getByRegion($regionId)
    {
        $sql = "SELECT * FROM Business WHERE region_id = ? AND is_active = 1";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$regionId]);
        return $stmt->fetchAll();
    }

    public function getBusinessStats($businessId)
    {
        $sql = "SELECT 
                (SELECT COUNT(*)
                 FROM User 
                 WHERE business_id = ? AND role = 'Seller' AND is_active = 1) as total_sellers,
                
                (SELECT COUNT(DISTINCT p.product_id)
                 FROM User u
                 JOIN Product p ON u.user_id = p.seller_id
                 WHERE u.business_id = ?) as total_products,
                
                (SELECT COUNT(DISTINCT p.product_id)
                 FROM User u
                 JOIN Product p ON u.user_id = p.seller_id
                 WHERE u.business_id = ? AND p.status = 'available') as active_products,
                
                (SELECT COUNT(DISTINCT o.order_id)
                 FROM Orders o
                 WHERE o.business_id = ?) as total_orders,
                
                (SELECT COALESCE(SUM(o.total_amount), 0)
                 FROM Orders o
                 WHERE o.business_id = ?) as total_revenue";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId, $businessId, $businessId, $businessId, $businessId]);
        return $stmt->fetch();
    }

    public function getSellers($businessId)
    {
        $sql = "SELECT user_id, user_name, email, phone, is_active, created_at,
                    (SELECT COUNT(*) FROM Product WHERE seller_id = User.user_id) as product_count
                FROM User 
                WHERE business_id = ? AND role = 'Seller'
                ORDER BY user_name";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        return $stmt->fetchAll();
    }

    public function getRecentOrders($businessId, $limit = 5)
    {
        $sql = "SELECT o.*, u.user_name as buyer_name
                FROM Orders o
                JOIN User u ON o.buyer_id = u.user_id
                WHERE o.business_id = ?
                ORDER BY o.order_date DESC
                LIMIT ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId, $limit]);
        return $stmt->fetchAll();
    }

    public function toggleSellerStatus($userId, $businessId)
    {
        $sql = "UPDATE User 
                SET is_active = NOT is_active 
                WHERE user_id = ? AND business_id = ? AND role = 'Seller'";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$userId, $businessId]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidDataException('Seller not found or access denied');
        }
    }

    public function getById($businessId)
    {
        $sql = "SELECT * FROM Business WHERE business_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        return $stmt->fetch();
    }

    public function addUserToBusiness($businessId, $data)
    {
        // Validate required fields
        $error = self::errorInRequiredField('User Name', $data['user_name'], 50);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Email', $data['email'], 100);
        self::assertNoError($error);

        // Check if email already exists
        $sql = "SELECT COUNT(*) as count FROM User WHERE email = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$data['email']]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new InvalidDataException('Email already exists');
        }

        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO User (business_id, user_name, email, address, phone, 
                             password_hash, role, bio, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $businessId,
            $data['user_name'],
            $data['email'],
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $passwordHash,
            $data['role'],
            $data['bio'] ?? null
        ]);

        return $this->getDB()->lastInsertId();
    }

    public function updateLogo($businessId, $logoPath)
    {
        $sql = "UPDATE Business SET business_logo = ? WHERE business_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        return $stmt->execute([$logoPath, $businessId]);
    }

    public function getBuyers($businessId)
    {
        $sql = "SELECT user_id, user_name, email, phone, address, is_active, created_at
            FROM User 
            WHERE business_id = ? AND role = 'Buyer'
            ORDER BY user_name";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$businessId]);
        return $stmt->fetchAll();
    }
}
