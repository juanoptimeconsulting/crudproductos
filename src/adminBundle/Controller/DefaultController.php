<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $product_repo = $em->getRepository("adminBundle:Product");
        $products = $product_repo->findAll();
        foreach ($products as $run){
            echo  $run->getName()."</br>";
            echo  $run->getCategory()->getName()."</br>";
        }

        die();

        return $this->render('adminBundle:Default:index.html.twig');
    }
}
