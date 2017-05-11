<?php

namespace AppBundle\Controller;

use AppBundle\Classes\Cart;
use AppBundle\Classes\CartItem;
use AppBundle\Classes\CartItemInterface;
use AppBundle\Entity\OrderItems;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Product;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("/cart/view", name="cart_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function vewCartAction()
    {
        $cart = $this->getCartFromSession();
        // dump($cart);

        return $this->render('cart/view_cart.html.twig', ['cart' => $cart, 'user' => $this->getUser()]);
    }

    private function getCartFromSession()
    {
        $session = $this->get('session');
        // test data
//        $session->set('cart_items', [
//            1 => ['product_id' => 1, 'quantity' => 5, 'percentage'=>50],
//            2 => ['product_id' => 2, 'quantity' => 2, 'percentage'=>50]
//        ]);

        $items = $session->get('cart_items');
        if (!$items) {
            $items = [];
        }
        // dump($items);
        // test data end

        /** @var ArrayCollection | CartItemInterface [] */
        $cartItems = new ArrayCollection();
        foreach ($items as $item) {
            $cartItem = new CartItem($this->getProduct($item['product_id']), $item['quantity'], $item['percentage']);
            $cartItems->add($cartItem);
        }

        $cart = new Cart($cartItems, null);
        return $cart;
    }

    private function getProduct($id)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Product')->find($id);
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     * @Method("POST")
     */
    public function emptyCartAction(Request $request)
    {
        $this->get('session')->remove('cart_items');
        return $this->redirectToRoute('cart_view');
    }

    /**
     * @Route("/cart/add", name="cart_add_product")
     * @Method("POST")
     */
    public function addToCartAction(Request $request)
    {
        $promotionManager = $this->get('promotion_manager');
        $id = $request->request->get('id');
        $quantity = $request->request->get('quantity');
        $product = $this->getProduct($id);
        $promotion = $promotionManager->getMaxPromotion($product->getCategory());
        $cartItem = new CartItem($product, $quantity, $promotion);
        $this->setCartItemInSession($cartItem);
        return $this->redirectToRoute('cart_view');
    }

    private function setCartItemInSession(CartItem $item)
    {
        $session = $this->get('session');
        $items = $session->get('cart_items');
        if (!$items) {
            $items = [];
        }
        $product_id = $item->getProduct()->getId();
        if (isset($items[$product_id])) {
            $items[$product_id]['quantity'] += $item->getQuantity();
            $items[$product_id]['percentage'] = $item->getPercetnage();

        } else {
            $items[$product_id]['product_id'] = $product_id;
            $items[$product_id]['quantity'] = $item->getQuantity();
            $items[$product_id]['percentage'] = $item->getPercetnage();
        }
        if ($items[$product_id]['quantity'] < 1) {
            unset($items[$product_id]);
        }
        $session->set('cart_items', $items);
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function CheckoutCartAction(Request $request)
    {
        $user = $this->getUser();

        $cart = $this->getCartFromSession();

        $order = new Orders();
        $order->setDate(new DateTime());
        $order->setUser($user);
        $order->setStatus(Orders::STATUS_NEW);

//        dump($order);
//        exit;

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        $userCash = $user->getCash();
        $spendCash = 0;
        foreach ($cart->getItems() as $item) {
            /** @var  Product $product */
            $product = $item->getProduct();
            $orderItem = new OrderItems();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity((int)$item->getQuantity());
            $orderItem->setTotal($item->getItemPrice());
            $stock = $product->getStock();
            $stock->setQuantity($stock->getQuantity() - $item->getQuantity());
            $em->persist($stock);
            $em->persist($product);
            $em->persist($orderItem);
            $spendCash += $orderItem->getTotal();
        }

        if ($product->getStock()->getQuantity() < $item->getQuantity()) {
            $this->get('session')->getFlashBag()->add('error', 'Insufficient availability');
        }

        if ($spendCash <= $userCash) {
            $user->setCash($userCash-$spendCash);
            $em->persist($user);
            $em->flush();
        }else{
            $this->get('session')->getFlashBag()->add('error', 'Your money is not enough');
        }

        $this->get('session')->remove('cart_items');

        return $this->redirectToRoute('orders_show', ['id' => $order->getId()]);
    }


}
