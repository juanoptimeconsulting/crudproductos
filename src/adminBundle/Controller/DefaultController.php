<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('adminBundle:Default:index.html.twig');
    }
}
