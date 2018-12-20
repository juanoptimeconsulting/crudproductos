<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    // driver tests, ignore
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("adminBundle:Category");
        $products = $category_repo->findAll();
        foreach ($products as $run){

            echo  $run->getName()."</br>";


            $productos  = $run->getProducts();

            foreach ($productos as $getpr){

                echo  $getpr->getName()."</br>";
            }
        }

        die();

        return $this->render('adminBundle:Default:index.html.twig');
    }


    public  function inicioAction(){

        return $this->render('adminBundle:Default:index.html.twig');

    }
}
