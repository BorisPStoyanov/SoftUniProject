<?php

namespace AppBundle\Entity;

use AppBundle\Classes\PromotionInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PromotionRepository")
 */
class Promotion
{
    const TYPE_PRODUCT = 10;
    const TYPE_CATEGORY = 20;
    const TYPE_ALL_PRODUCTS = 30;
    const TYPE_USER = 40;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var int
     *
     * @ORM\Column(name="percentage", type="integer",)
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(100)
     */
    private $percentage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fromDate", type="datetime")
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="toDate", type="datetime")
     */
    private $toDate;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="promotions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Promotion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get percentage
     *
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set percentage
     *
     * @param integer $percentage
     *
     * @return Promotion
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return Promotion
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     *
     * @return Promotion
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function isActive(DateTime $date)
    {
        return ($date >= $this->fromDate && $date <= $this->toDate);
    }

    public function calculateDiscount($price)
    {
        return ($this->percentage * $price) / 100.0;
    }

    /**
     * @return ArrayCollection | Promotion []
     */
    public static function getPromotionsArray()
    {
        return array_flip([
            //self::TYPE_PRODUCT => "For a Product",
            self::TYPE_CATEGORY => "For product Category",
            self::TYPE_ALL_PRODUCTS => "For All Products",
            //self::TYPE_USER => "For Users"
        ]);
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category | null $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }



}

