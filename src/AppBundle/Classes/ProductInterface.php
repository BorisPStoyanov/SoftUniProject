<?php
namespace AppBundle\Classes;

interface ProductInterface
{

    public function getName();

    public function getDescription();

    public function getPrice();

    public function getPromotionalPrice();

    public function getPromotionalPercentage();

} 