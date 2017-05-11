<?php

namespace AppBundle\Classes;

use AppBundle\Entity\Product;


class CartItem implements CartItemInterface
{

    private $quantity;

    /**
     * @var Product
     */
    private $product;

    private $percetnage = 0;

    function __construct(/*ProductInterface*/
        $product,
        $quantity = null,
        $percentage = 0
    ) {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->percetnage = $percentage;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getItemPrice()
    {
        return $this->quantity * $this->product->getPrice() * (1 - $this->percetnage/100);
    }

    public function getItemPromotionalPrice()
    {
        return $this->quantity * $this->product->getPromotionalPrice();
    }

    public function getItemPromotionalPercentage()
    {
        $this->product->getPromotionalPrice();
    }

    public function addQuantity($quantity)
    {
        if ($quantity > 0) {
            $this->quantity += $quantity;
        }
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getPercetnage(): int
    {
        return $this->percetnage;
    }

    /**
     * @param int $percetnage
     */
    public function setPercetnage(int $percetnage)
    {
        $this->percetnage = $percetnage;
    }



}