<?php

namespace AppBundle\Classes;
use AppBundle\Classes\ProductInterface;
use AppBundle\Classes\PromotionInterface;

class Product implements ProductInterface
{

    private $name;

    private $description;

    private $price;

    /**
     * @var PromotionInterface
     */
    private $promotion;

    function __construct($name, $description, $price, ProductInterface $promotion = null)
    {
        $this->description = $description;
        $this->name = $name;
        $this->price = $price;
        $this->promotion = $promotion;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPromotionalPrice()
    {
        if ($this->promotion instanceof PromotionInterface) {
            return $this->price - $this->promotion->calculateDiscount($this->price);
        }
        return $this->price;
    }

    public function getPromotionalPercentage()
    {
        if ($this->promotion instanceof PromotionInterface) {
            return $this->promotion->getPercentage();
        }
        return 0;
    }
}