<?php

namespace Agora\Models;

use Agora\Core\AbstractModel;

class Region extends AbstractModel
{
    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM Region ORDER BY region_name";
        return $this->getDB()->query($sql);
    }
}