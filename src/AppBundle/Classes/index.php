<?php
include 'Product.php';
include 'Cart.php';
include 'CartItem.php';

$cart = new Cart();

$product1 = new Product('Product 10', 'Description of product', 2.50);
$product2 = new Product('Product 22', 'Produkt222222 descrip', 9.45);

$cartItem1 = new CartItem($product1, 2);
$cartItem2 = new CartItem($product2, 5);

$cart->addItem($cartItem1);
$cart->addItem($cartItem2);

listCartItems($cart);
echo $cart->getTotal();
echo PHP_EOL;

$cart->removeItem($cartItem2, 4);
$cart->addItem($cartItem1, 1);

listCartItems($cart);
echo $cart->getTotal();
echo PHP_EOL;

function listCartItems(Cart &$cart)
{
    foreach ($cart->getItems() as $item)
    {
        $product = $item->getProduct();

        echo $product->getName().' | ';
        echo $product->getDescription().' | ';
        echo $product->getPrice().' | ';
        echo $item->getQuantity().' | ';
        echo $item->getItemPrice() . ' | ';
        echo PHP_EOL;
    }
}

