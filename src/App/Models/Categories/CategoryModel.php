<?php

namespace App\Models\Categories;

use App\DataBase\DataBase as DB;
use PDO;

class CategoryModel implements CategoriesInterface
{
    private $connection;

    public function __construct()
    {
        $db = new DB;
        $this->connection = $db->connect();
    }

    public function fetchAllCategory()
    {
        $req = $this->connection->prepare("SELECT * FROM catigorie");
        $req->execute();
        $catigories = $req->fetchAll(PDO::FETCH_ASSOC);

        return $catigories;
    }
}
