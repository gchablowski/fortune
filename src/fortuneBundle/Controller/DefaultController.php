<?php

namespace fortuneBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use fortuneBundle\Entity\Email;
use fortuneBundle\Form\EmailType;

class DefaultController extends FOSRestController {

    /**
     * Get qotes action
     * @return array
     *
     * @Get("/quotes", name="quotes")
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
     * @return array() with a form
     *
     * @Post("/add/email", name="email_add")
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

            //send a validation mail
            $dispatcher = $this->get('hip_mandrill.dispatcher');

            $message = new Message();

            $message->addTo($email->getEmail())
                    ->setSubject('Validate your registration')
                    ->setHtml($this->renderView('fortuneBundle:Default:registration.html.twig', array('token' => $email->getToken())))
                    ->setSubaccount('Project');

            $result = $dispatcher->send($message);

            return array("message" => "Your email have been saved. To proceed your registration a validation mail have been send to you.");
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * put activate/desactivate email action
     * @param Request $request
     * @return array() with a message
     *
     * @Put("/activate/email/{token}", name="email_activate",defaults={"activate" = true})
     * @Put("/desactivate/email/{token}", name="email_desactivate", defaults={"activate" = false})
     */
    public function emailsActivationAction(Request $request, $token, $activate) {
        // get email object
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fortuneBundle:Email')->findOneBy(array("token" => $token, "active" => !$activate));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find your email.');
        }

        //persist
        $entity->setActive($activate);
        $em->persist($entity);
        $em->flush();

        // return a message
        $message = $entity->getEmail()." have been desactivated";
        if ($activate) {
            $message = $entity->getEmail()." have been activated";
        }

        return array(
            "code" => 200,
            "message" => $message,
            "errors" => null
        );
    }

}
