<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;

class PriceCalculator
{
    /** @var  PromotionManager */
    protected $manager;

    public function __construct(PromotionManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @param Product $product
     *
     * @return float
     */
    public function calculate($product)
    {
        $category = $product->getCategory();
        $category_id = $category->getId();

        $promotion = 0;

        $promotion = $this->manager->getMaxPromotion($category);

        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}
