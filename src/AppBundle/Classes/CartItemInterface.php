<?php
namespace AppBundle\Classes;

interface CartItemInterface
{
    public function getProduct();

    public function getItemPrice();

    public function getItemPromotionalPrice();

    public function getItemPromotionalPercentage();

    public function addQuantity($quantity);

    public function getQuantity();

    public function setQuantity($quantity);
} 