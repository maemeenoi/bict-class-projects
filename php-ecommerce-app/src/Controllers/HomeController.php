<?php
namespace Agora\Controllers;

use Agora\Core\AbstractController;
use Agora\Views\HomeView;

class HomeController extends AbstractController
{
    protected function getView($isPostback)
    {
        // Create and return the home view
        $view = new HomeView($this->getContext());

        // Get featured products from the database
        $db = $this->getContext()->getDB();
        $sql = "SELECT p.*, u.business_id, b.business_name 
                FROM Product p 
                JOIN User u ON p.seller_id = u.user_id 
                JOIN Business b ON u.business_id = b.business_id 
                WHERE p.status = 'available' 
                LIMIT 4";

        $products = $db->query($sql);
        $view->setModel($products);

        return $view;
    }

    public function index()
    {
        return $this->process();
    }
}