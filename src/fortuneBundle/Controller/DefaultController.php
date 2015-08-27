<?php

namespace fortuneBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use fortuneBundle\Entity\Email;
use fortuneBundle\Form\EmailType;

class DefaultController extends FOSRestController {

    /**
     * Get qotes action
     * @return array
     *
     * @Get("/quotes")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('fortuneBundle:Quote');

        $data = $repository->myFindAllQuotes();

        return $data;
    }

    /**
     * post email action
     * @param Request $request
     * @return array()
     *
     * @Post("/add/email")
     */
    public function emailsAction(Request $request) {
        //create new object
        $email = new Email();

        //create form
        $form = $this->createForm(new EmailType(), $email);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //persist the new email
            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();

            return array("message" => "Your email have been saved. To proceed your registration a validation mail have been send to you.");
        }

        return array(
            'form' => $form,
        );
    }
    
    

}
