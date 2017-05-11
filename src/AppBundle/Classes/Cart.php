<?php

namespace AppBundle\Classes;
use AppBundle\Classes\CartItemInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Cart implements CartInterface
{
    /**
     * @var ArrayCollection | CartItemInterface []
     */
    private $items = [];

    /**
     * @var PromotionInterface
     */
    private $promotion = null;


    function __construct(ArrayCollection $items/*, PromotionInterface $promotion = null*/)
    {
        $this->items = $items;
        //$this->setPromotion($promotion);
    }

    public function setPromotion(PromotionInterface $promotion)
    {
        if ($promotion->getType() === Promotion::TYPE_USER)
            $this->promotion = $promotion;
    }


    public function addItem(CartItemInterface $item, $quantity = 1)
    {
        $key = $this->getItemKey($item);
        if (null === $key) {
            $this->items[] = $item;
        } else {
            $this->items[$key]->addQuantity($quantity);
        }
    }


    public function removeItem(CartItemInterface $item, $quantity)
    {
        $key = $this->getItemKey($item);
        if ($key !== false) {
            $newQuantity = $this->items[$key]->getQuantity() - $quantity;
            if ($newQuantity > 0) {
                $this->items[$key]->setQuantity($newQuantity);
            } else {
                //$this->items[$key]->setQuantity(0);
                unset($this->items[$key]);
            }
        }
    }

    public function getTotal()
    {
        return $this->recalculate();
    }

    public function getDiscountPercentage()
    {
        if ($this->promotion instanceof PromotionInterface) {
            return $this->promotion->getPercentage();
        }
        return 0;
    }

    public function getDiscountedValue($price)
    {
        if ($this->promotion instanceof PromotionInterface) {
            return $this->promotion->calculateDiscount($price);
        }
        return 0;
    }

    private function recalculate()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getItemPrice();
        }

        $total -= $this->getDiscountedValue($total);

        return $total;
    }

    public function getItems()
    {
        return $this->items;
    }

    private function getItemKey($item)
    {
        $key = array_search($item, $this->items, true);
        if ($key === false) {
            return null;
        }
        return $key;
    }

    public function getItemCount()
    {
        return count($this->items);
    }
}