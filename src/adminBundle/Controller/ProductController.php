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

    public function addProductAction(Request $request) {

        $entry = new Product();
        $form = $this->createForm(ProductType::class, $entry); //llamar a la vista

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,
                $em = $this->getDoctrine()->getEntityManager();

                $entry = new Product();
                $entry->setCode($form->get("code")->getData()); //seteo de los datos de FRM
                $entry->setName($form->get("name")->getData());
                $entry->setDescription($form->get("description")->getData());
                $entry->setBrand($form->get("brand")->getData());
                $entry->setPrice($form->get("price")->getData());



                //para el combo box
                $categori_repo = $em->getRepository("adminBundle:Category");

                $category = $categori_repo->find($form->get("category")->getData());

                $entry->setCategory($category); //pasamos un objeto


                $em->persist($entry);
                $flush = $em->flush();



                if ($flush == null) {
                    $estado = "el Producto se ha creado correctamente";
                } else {
                    $estado = "error a la añadir el Producto";
                }
            } else {
                $estado = "Error de Formulario";
            }
            $this->session->getFlashBag()->add("estado", $estado); //para los mensajes de confirmacion
            return $this->redirectToRoute("listaproducts"); //redirigirnos a las lita
        }




        return $this->render("@admin/viewProduct/addProduct.html.twig", array(
            'form' => $form->createView()
        ));
    }

    //listar Productos
    public function listaEntryAction($page) {
        $em = $this->getDoctrine()->getEntityManager();
        $Product_repo = $em->getRepository("adminBundle:Product");

        $category_repo = $em->getRepository("adminBundle:Category"); //listar categorias en el menu

        $products = $Product_repo->findAll(); //listar entradas
        //$pagesize = 5;
        //$entries = $entry_repo->getPaginaterEntries($pagesize,$page);


        //mostrar los links
       // $totalitems = count($entries);
        //$pagesCount  =  ceil($totalitems/$pagesize);



        $categories = $category_repo->findAll(); //listar categorias en el menu
        return $this->render("adminBundle:viewProduct:productList.html.twig", array(
            "products" => $products,
            "categories" => $categories

        ));
    }

    public function deleteEntryAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("adminBundle:Product");


        $entry =  $entry_repo->find($id);




        $em->remove($entry);
        $em->flush();


        return $this->redirectToRoute("listaproducts");


    }


    public function editEntryAction(Request $request, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("adminBundle:Product");

        //para el combo box
        $categori_repo = $em->getRepository("adminBundle:Category");





        //generar el formulario en base a una entrada
        $product = $entry_repo->find($id);



        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        //comprovar si los datos son validos

        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,



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
                    $estado = "el Producto se ha editado correctamente";
                } else {
                    $estado = "error al editar  el Producto";
                }
            } else {
                $estado = "Error de Formulario";
            }
            $this->session->getFlashBag()->add("estado", $estado); //para los mensajes de confirmacion
            return $this->redirectToRoute("listaproducts"); //redirigirnos a las lita
        }




        return $this->render("adminBundle:viewProduct:productEdit.html.twig", array(
            'form' => $form->createView(),
            'product' => $product
        ));




    }

}
