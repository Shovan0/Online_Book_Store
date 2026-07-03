<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CartItem;

class CartController
{
    private CartItem $cartItemModel;

    public function __construct()
    {
        $this->cartItemModel = new CartItem();
    }

    public function add(int $userId, int $bookId, int $quantity): void
    {
        $this->cartItemModel->addOrUpdate($userId, $bookId, $quantity);
    }

    public function list(int $userId): array
    {
        return $this->cartItemModel->getCartItems($userId);
    }

    public function update(int $cartId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->cartItemModel->remove($cartId);
            return;
        }

        $this->cartItemModel->updateQuantity($cartId, $quantity);
    }

    public function remove(int $cartId): void
    {
        $this->cartItemModel->remove($cartId);
    }

    public function empty(int $userId): void
    {
        $this->cartItemModel->emptyCart($userId);
    }
}
