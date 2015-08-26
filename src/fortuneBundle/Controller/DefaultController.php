<?php

namespace fortuneBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;

class DefaultController extends FOSRestController {

    /**
     * Get action
     * @var integer $id Id of the entity
     * @return array
     * 
     * @Get("/quotes")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('fortuneBundle:Quote');

        $data = $repository->findAll();

        return $data;
    }

}
