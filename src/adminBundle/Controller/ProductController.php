<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use adminBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;
use adminBundle\Form\ProductType;


class ProductController extends Controller {
    //message flash
    private $session;

    public function __construct() {
        $this->session = new Session();
    }
     //addproduct
    public function addProductAction(Request $request) {

        $porduct = new Product();
        $form = $this->createForm(ProductType::class, $porduct); //llamar a la vista

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,
                $em = $this->getDoctrine()->getEntityManager();

                $porduct = new Product();
                $porduct->setCode($form->get("code")->getData()); //seteo de los datos de FRM
                $porduct->setName($form->get("name")->getData());
                $porduct->setDescription($form->get("description")->getData());
                $porduct->setBrand($form->get("brand")->getData());
                $porduct->setPrice($form->get("price")->getData());





                //for combo
                $categori_repo = $em->getRepository("adminBundle:Category");

                $category = $categori_repo->find($form->get("category")->getData());

                $porduct->setCategory($category); //pasamos un objeto

                    if($porduct->getName() ==  $porduct->getCode()) {


                        $estate = "el Nombre no puede ser igual al codigo";


                    }else if (strpos($porduct->getCode(), " ") ){


                    $estate = "el codigo no puede contener espacios en blanco";



                    //´|i|:|!|#|%|&|=|¡|¿|;|{|}|-|,|.|<|>|~|°
                    }else if( preg_match("/(á|é|í|ó|ú|ñ+)/", $porduct->getCode())) {

                        $estate = "has ingresado caracteres no validos";
                    }


                    else{
                        $em->persist($porduct);
                        $em->flush();//submit

                        $estate = "el producto se ha creado correctamente";

                    }

            } else {
                $estate = "Error de Formulario";
            }



            $this->session->getFlashBag()->add("estado", $estate); //para los mensajes de confirmacion
            //return $this->redirectToRoute("listaproducts"); //redirigirnos a las lita
        }




        return $this->render("@admin/viewProduct/addProduct.html.twig", array(
            'form' => $form->createView()
        ));
    }

    //listar Productos
    public function listaProductAction($page) {
        $em = $this->getDoctrine()->getEntityManager();
        $Product_repo = $em->getRepository("adminBundle:Product");

        $category_repo = $em->getRepository("adminBundle:Category"); //listar categorias en el menu

       // $products = $Product_repo->findAll(); //listar entradas
        $pagesize = 5;
        $products = $Product_repo->getPaginaterProducts($pagesize,$page);


        $totalitems = count($products);

        $pagesCount  =  ceil($totalitems/$pagesize);



        $categories = $category_repo->findAll(); //listar categorias en el menu
        return $this->render("adminBundle:viewProduct:productList.html.twig", array(
            "products" => $products,
            "categories" => $categories,
            "totalitems"=> $totalitems,
            "pagecount"=>$pagesCount,
            "page" => $page,
            "pagesumar" => $page

        ));
    }

    public function deleteProductAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $product_repo = $em->getRepository("adminBundle:Product");


        $product =  $product_repo->find($id);




        $em->remove($product);
        $em->flush();


        return $this->redirectToRoute("listaproducts");


    }


    public function editProductAction(Request $request, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $product_repo = $em->getRepository("adminBundle:Product");

        //para el combo box
        $categori_repo = $em->getRepository("adminBundle:Category");





        //generar el formulario en base a una entrada
        $product = $product_repo->find($id);



        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        //comprovar si los datos son validos

        if ($form->isSubmitted()) {

            if ($form->isValid()) {// validation.yml,



                $product->setCode($form->get("code")->getData()); //seteo de los datos de FRM
                $product->setName($form->get("name")->getData());
                $product->setDescription($form->get("description")->getData());
                $product->setBrand($form->get("brand")->getData());
                $product->setPrice($form->get("price")->getData());



                //para el combo box
                $category = $categori_repo->find($form->get("category")->getData());

                $product->setCategory($category); //pasamos un objeto



                $em->persist($product);
                $flush = $em->flush();








                if ($flush == null) {
                    $estate = "el producto se ha editado correctamente";
                } else {
                    $estate = "error al editar  el Producto";
                }
            } else {
                $estate = "Error de Formulario";
            }
            $this->session->getFlashBag()->add("estado", $estate); //para los mensajes de confirmacion
            return $this->redirectToRoute("listaproducts"); //redirigirnos a las lita
        }




        return $this->render("adminBundle:viewProduct:productEdit.html.twig", array(
            'form' => $form->createView(),
            'product' => $product
        ));




    }

}
