<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="user_login")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function loginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

//        if ($error) {
//            $this->get('session')->getFlashBag()->add('error', 'Username or password is incorrect!');
//        }

        $lastUsername = $authenticationUtils->getLastUsername();
        return [
            'last_username' => $lastUsername,
            'error' => $error
        ];
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/login_check", name="user_check")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkAction(Request $request)
    {
        return $this->redirectToRoute('user_login');
    }


    /**
     * @Route("/register", name="user_register")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserType::class);
        $form->offsetUnset('roles');
        $form->offsetUnset('status');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles([$user->getDefaultRole()]);
            $encoder = $this->get('security.password_encoder');

            $user->setPassword(
                $encoder->encodePassword($user, $user->getPasswordRaw())
            );

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_login');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
