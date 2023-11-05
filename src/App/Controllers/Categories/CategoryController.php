<?php

namespace App\Controllers\Categories;

use App\Models\Categories\CategoriesInterface;

class CategoryController
{
    private $category;

    public function __construct(CategoriesInterface $categoryModel)
    {
        $this->category = $categoryModel;
    }

    public function index()
    {
        $category = $this->category->fetchAllCategory();

        echo json_encode([
            'category' => $category
        ]);
    }
}
