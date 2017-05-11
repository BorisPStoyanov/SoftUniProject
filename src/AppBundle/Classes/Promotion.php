<?php

namespace AppBundle\Classes;
use AppBundle\Classes\PromotionInterface;
use \DateTime;

class Promotion implements PromotionInterface
{
    const TYPE_PRODUCT = 10;
    const TYPE_CATEGORY = 20;
    const TYPE_ALL_PRODUCTS = 30;
    const TYPE_USER = 40;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var integer
     */
    private $percentage;

    /**
     * @var \DateTime
     */
    private $fromDate;

    /**
     * @var \DateTime
     */
    private $toDate;

    function __construct($name,
                         $percentage,
                         DateTime $fromDate,
                         DateTime $toDate,
                         $type=self::TYPE_PRODUCT
    ){
        $this->name = $name;
        $this->percentage = $percentage;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->type = $type;
    }


    public function getFromDate()
    {
        return $this->fromDate;
    }

    private function setFromDate(DateTime $fromDate)
    {
        $this->fromDate = $fromDate;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    public function getToDate()
    {
        return $this->toDate;
    }


    private function setToDate(DateTime $toDate)
    {
        $this->toDate = $toDate;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function isActive(DateTime $date)
    {
        return ($date >= $this->fromDate && $date <= $this->toDate);
    }

    public function calculateDiscount($price)
    {
        return ($this->percentage * $price)/100.0;
    }
}