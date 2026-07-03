<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Book;
use App\Models\Category;

class BookController
{
    private Book $bookModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->bookModel = new Book();
        $this->categoryModel = new Category();
    }

    public function getCategories(): array
    {
        return $this->categoryModel->getAll();
    }

    public function getFeaturedBooks(int $limit = 4): array
    {
        return $this->bookModel->getFeatured($limit);
    }

    public function getLatestBooks(int $limit = 6): array
    {
        return $this->bookModel->getLatest($limit);
    }

    public function getBooks(array $request): array
    {
        $page = isset($request['page']) && is_numeric($request['page']) ? max(1, (int) $request['page']) : 1;
        $limit = 12;

        return $this->bookModel->search($request, $page, $limit) + ['page' => $page, 'limit' => $limit];
    }

    public function getBook(int $id): array|false
    {
        return $this->bookModel->findById($id);
    }
}
