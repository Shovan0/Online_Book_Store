<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CartItem;
use App\Models\Order;

class OrderController
{
    private Order $orderModel;
    private CartItem $cartItemModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->cartItemModel = new CartItem();
    }

    public function checkout(int $userId, array $payload): int
    {
        $cartItems = $this->cartItemModel->getCartItems($userId);

        if (empty($cartItems)) {
            throw new \RuntimeException('Cart is empty.');
        }

        $orderId = $this->orderModel->createOrder($userId, $payload, $cartItems);
        $this->cartItemModel->emptyCart($userId);

        return $orderId;
    }

    public function getOrders(int $userId): array
    {
        return $this->orderModel->getOrdersByUser($userId);
    }

    public function getOrder(int $userId, int $orderId): array|false
    {
        $order = $this->orderModel->getOrderByUser($userId, $orderId);

        if ($order === false) {
            return false;
        }

        $order['items'] = $this->orderModel->getOrderItems($orderId);

        return $order;
    }
}
