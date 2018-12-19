<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use adminBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;
use adminBundle\Form\ProductType;

class ProductController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function addproductsAction(Request $request) {

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product); //llamar a la vista

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,
               $em = $this->getDoctrine()->getEntityManager();

                $product = new Product();
                $product->setCode($form->get("code")->getData()); //seteo de los datos de FRM
                $product->setName($form->get("name")->getData());
                $product->setDescription($form->get("description")->getData());
                $product->setBrand($form->get("brand")->getData());
                $product->setPrice($form->get("price")->getData());



                //para el combo box
                $categori_repo = $em->getRepository("adminBundle:Category");

                $category = $categori_repo->find($form->get("category")->getData());

                $product->setCategory($category); //pasamos un objeto




                $em->persist($product);
                $flush = $em->flush();



                if ($flush == null) {
                    $estado = "el Producto se ha creado correctamente";
                } else {
                    $estado = "error a la añdir la Entrada";
                }

            } else {
                $estado = "Error de Formulario";
            }
           $this->session->getFlashBag()->add("estado", $estado); //para los mensajes de confirmacion
            return $this->redirectToRoute("listproducts"); //redirigirnos a las lita
        }




        return $this->render("adminBundle:viewProduct:addProduct.html.twig", array(
            'form' => $form->createView()
        ));
    }    //listar entradas
    public function listproductAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $category_repo = $em->getRepository("adminBundle:Product");
        $products = $category_repo->findAll(); //listar todos
        //en el array se envia la categoria para que se imprima en la vista index.html.twig
        return $this->render("adminBundle:viewProduct:productlist.html.twig", array(
            'producto' => $products
        ));

    }
}
