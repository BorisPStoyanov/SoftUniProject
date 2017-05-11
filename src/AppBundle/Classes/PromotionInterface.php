<?php
namespace AppBundle\Classes;
use \DateTime;

interface PromotionInterface {

    public function getName();

    public function getType();

    public function getPercentage();

    public function isActive(DateTime $date);

    public function calculateDiscount($price);
}