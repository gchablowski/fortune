<?php

namespace fortuneBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;

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
     * @return Product|array
     *
     * @Post("/add/email")
     */
    public function emailsAction(Request $request) {
        $form = $this->createFormBuilder(array(), array('csrf_protection' => false))
                ->add('email', 'text', array(
                    'constraints' => new Email()
                ))
                ->setMethod('POST')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Les données sont un tableau avec les clés "name", "email", et "message"
            $data = $form->getData();
            var_dump($form->getData());
            die();
        }

        return array(
            'form' => $form,
        );
    }

}
