<?php

namespace AppBundle\Classes;
use AppBundle\Classes\CartItemInterface;

interface CartInterface
{
    public function addItem(CartItemInterface $item, $quantity=1);

    public function removeItem(CartItemInterface $item, $quantity);

    public function getItems();

    public function getTotal();

    public function getItemCount();
}