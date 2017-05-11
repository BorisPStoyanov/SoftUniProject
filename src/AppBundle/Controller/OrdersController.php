<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Orders;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Order controller.
 *
 *
 */
class OrdersController extends Controller
{
    /**
     * Lists all order entities.
     *
     * @Route("/admin/orders", name="orders_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('AppBundle:Orders')->findAll();

        return $this->render('orders/index.html.twig', array(
            'orders' => $orders,
            'statuses' => array_flip(Orders::getPossibleStatuses())
        ));
    }

    /**
     * Finds and displays a order entity.
     *
     * @Route("/orders/{id}", name="orders_show")
     * @Method("GET")
     */
    public function showAction(Orders $order)
    {

        return $this->render('orders/show.html.twig', array(
            'order' => $order,
            'user' => $this->getUser(),
            'statuses' => array_flip(Orders::getPossibleStatuses())
        ));
    }

    /**
     * Finds and displays a order entity.
     *
     * @Route("/orders/user/{id}", name="orders_show_for_user")
     * @Method("GET")
     */
    public function showMyOrdersAction(User $user)
    {

        $orders = $user->getOrders();

        return $this->render('orders/list_user_orders.html.twig', array(
            'orders' => $orders,
            'user' => $this->getUser(),
            'statuses' => array_flip(Orders::getPossibleStatuses())
        ));
    }

    /**
     * Finds and displays a order entity.
     *
     * @Route("/admin/orders/{id}", name="orders_view")
     * @Method("GET")
     */
    public function viewAction(Orders $order)
    {

        return $this->render('orders/view.html.twig', array(
            'order' => $order,
            'statuses' => array_flip(Orders::getPossibleStatuses())
        ));
    }


    /**
     *
     * @Route("/admin/orders/{id}/edit", name="admin_orders_edit")
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request, Orders $order)
    {

        $editForm = $this->createForm('AppBundle\Form\OrderType', $order);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_view', array('id' => $order->getId()));
        }

        return $this->render('orders/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'statuses' => Orders::getPossibleStatuses()
        ));
    }
}
