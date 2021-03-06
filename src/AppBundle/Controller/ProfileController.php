<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Developer;
use AppBundle\Form\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Invitation;
use VMelnik\DoctrineEncryptBundle\Configuration\Encrypted;


/**
 * @Route("/profile")
 * @Security("has_role('ROLE_USER')")
 */
class ProfileController extends BaseController
{
    /**
     * @Route("/", name="profile_index")
     * @Template()
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('AppBundle:Question')->findAllUnanswered();

        $user = $this->getUser();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $questions,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('profile/index.html.twig', array(
            'user' => $user,
            'questions' => $pagination,
        ));
    }

    /**
     * @Route("/answered", name="profile_answered")
     * @Template()
     *
     */
    public function answeredAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $questions = $em->getRepository('AppBundle:Question')->findBy(array('developer' => $user));

        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $questions = $em->getRepository('AppBundle:Question')->findAllAnswered();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $questions,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('profile/answered.html.twig', array(
            'questions' => $pagination,
        ));
    }

    /**
     * Overview of all the developers
     *
     * @Route("/developer", name="developer_overview")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function developerOverviewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $developers = $em->getRepository('AppBundle:Developer')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $developers,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('developer/overview.html.twig', array(
            'developers' => $pagination,
        ));
    }

    /**
     * Delete a developer
     *
     * @Route("/developer/delete/{id}", name="developer_delete")
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function developerDeleteAction(Request $request, Developer $developer)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Developer')->find($developer);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('developer_overview');
    }

    /**
     * Updating a developers role
     *
     * @Route("/developer/update/{id}", name="developer_update")
     *
     */
    public function developerUpdateAction(Request $request, Developer $developer)
    {
        $em = $this->getDoctrine()->getManager();

        $hasAdminRole = false;

        foreach ($developer->getRoles() as $role) {
            if($role == 'ROLE_ADMIN') {
                $hasAdminRole = true;
            }
        }

        if($hasAdminRole){
            $developer->removeRole('ROLE_ADMIN');
            $hasAdminRole = "ROLE_USER";
        }else{
            $developer->addRole('ROLE_ADMIN');
            $hasAdminRole = "ROLE_ADMIN";
        }

        $em->persist($developer);
        $em->flush();

        return new Response($hasAdminRole, 200);
    }
}