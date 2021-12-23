<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 24/04/2021
 * Time: 21:18
 */

namespace App\EventListener;


use App\Entity\Annonce;
use App\Entity\AnnonceSonorise;
use App\Entity\Organisation;
use App\Entity\Partenaire;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class CustomKernelListener
{


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $uniqueId = md5(uniqid());
        $uniqueId = substr($uniqueId, 0, 7);
        $rand = substr($uniqueId, 0, 2);


//        if ($entity instanceof Annonce)
//            $entity->setReference("ANN-"  . strtoupper(substr($entity->getTitre(), 0, 3)). strtoupper($uniqueId));

        if ($entity instanceof AnnonceSonorise){
           if (!$entity->getReference() ) {
               $entity->setReference("SONN"  . $rand."-". strtoupper($uniqueId));
           }
        }



        if ($entity instanceof Organisation){

            $entity->setNumEntreprise("ORG-" . strtoupper($uniqueId));
            $entity->setReference("ORG-" . strtoupper(substr($entity->getRaisonSocial(), 0, 3)).'-'.strtoupper($uniqueId));
        }
        if ($entity instanceof Partenaire){

            $entity->setReference("PART-" . strtoupper(substr($entity->getDesignation(), 0, 3)).'-'.strtoupper($uniqueId));
        }


    }


    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}