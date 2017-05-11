<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Stock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;


/**
 * Product controller.
 *
 * @@ 1Route("/admin/product")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     * @Route("/admin/", name="admin")
     * @Route("/admin/product/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $calc = $this->get('price_calculator');

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findAll();

        return $this->render('product/index.html.twig', array(
            'products' => $products,
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'user' => $this->getUser(),
            'calc' => $calc
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/admin/product/new", name="product_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setDateCreated(new \DateTime());
            $product->setDateUpdated(new \DateTime());

            $product->setUser($this->getUser());

            $stock = new Stock();
            $stock->setQuantity(1);
            $stock->setProduct($product);
            $product->setStock($stock);

            /** @var UploadedFile $file */
            $file = $product->getImage();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getTitle() . '' . $product->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->persist($stock);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Product is added successfully!');

            }
        }

        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/admin/product/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'user' => $this->getUser(),
        ));
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/admin/product/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {

        $product->setDateUpdated(new \DateTime());
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $imagePath = $product->getImage();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $product->getImage();
            if (!$file) {
                $product->setImage($imagePath);
            } else {
                $filename = md5($product->getTitle() . '' . $product->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);
            }


            $stock = $product->getStock();
            $stock->setQuantity($request->request->get('quantity'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->persist($stock);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Product is edited successfully!');

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'quantity' => $product->getStock()->getQuantity(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/admin/product/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('product_index');
        }
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Lists all product entities bay category.
     *
     * @Route("/product/list/{id}", defaults={"id": 0},  name="product_list")
     * @Method("GET")
     */
    public function listProductsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Product');
        if ($id > 0) {
            $category = $em->getRepository('AppBundle:Category')->find($id);
            $products = $repo->findBy(['category_id' => $category->getId()]);
        } else {
            $products = $repo->findAll();
        }
        return $this->render('product/list.html.twig', array(
            'products' => $products,
            'user' => $this->getUser(),
            'category_id' => $id,
            'calc' => $this->get('price_calculator')
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/product/{id}", name="product_view")
     * @Method("GET")
     */
    public function viewAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('product/view.html.twig', [
            'product' => $product,
            'user' => $this->getUser(),
            'calc' => $this->get('price_calculator')
        ]);
    }
}
