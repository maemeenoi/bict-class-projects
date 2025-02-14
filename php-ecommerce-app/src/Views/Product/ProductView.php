<?php
namespace Agora\Views\Product;

use Agora\Core\AbstractView;

class ProductView extends AbstractView
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/product/detail.php');
    }

    public function prepare()
    {
        $product = $this->getModel();

        // Set product details
        $this->setTemplateField('product_id', $product['product_id']);
        $this->setTemplateField('product_name', htmlspecialchars($product['product_name']));
        $this->setTemplateField('description', htmlspecialchars($product['description'] ?? ''));
        $this->setTemplateField('price', number_format($product['price'], 2));
        $this->setTemplateField('stock_quantity', $product['stock_quantity']);
        $this->setTemplateField('category', htmlspecialchars($product['category'] ?? 'Uncategorized'));
        $this->setTemplateField('seller_name', htmlspecialchars($product['seller_name']));
        $this->setTemplateField('business_name', htmlspecialchars($product['business_name']));

        // Set user info for purchase
        $session = $this->getContext()->getSession();
        $this->setTemplateField('buyer_name', $session->get('user_name'));

        // Get user's address from database for pre-fill
        $sql = "SELECT address FROM User WHERE user_id = ?";
        $stmt = $this->getContext()->getDB()->prepare($sql);
        $stmt->execute([$session->get('user_id')]);
        $user = $stmt->fetch();
        $this->setTemplateField('buyer_address', htmlspecialchars($user['address'] ?? ''));
    }
}