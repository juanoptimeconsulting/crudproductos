<?php

namespace adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use adminBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Session\Session;
use adminBundle\Form\CategoryType;

class CategoryController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    //agregamos una categoria
    public function addCategoryAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category); //llamar a la vista

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,
                $em = $this->getDoctrine()->getEntityManager();

                $category = new Category();
                $category->setCode($form->get("code")->getData());
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $category->setActive($form->get("active")->getData());

                $em->persist($category);
                $flush = $em->flush();

                if ($flush == null) {
                    $estado = "la categoria se ha creado correctamente";
                } else {
                    $estado = "error a la añdir la Categoria";
                }
            } else {
                $estado = "Eror de Formlario";
            }
            $this->session->getFlashBag()->add("estado", $estado);//para los mensajes de confirmacion
            return $this->redirectToRoute("listcategory"); //redirigirnos a las lita
        }




        return $this->render("adminBundle:viewCategory:addCategory.html.twig", array(
            'form' => $form->createView()
        ));
    }
    public function listacategoriaAction() {//redirigirnos
        $em = $this->getDoctrine()->getEntityManager();
        $category_repo = $em->getRepository("adminBundle:Category");
        $categories = $category_repo->findAll(); //listar todos
        //en el array se envia la categoria para que se imprima en la vista index.html.twig
        return $this->render("adminBundle:viewCategory:listCategory.html.twig", array(
            'category' => $categories
        ));
    }

    //BORRAR UNA CATEGORIA, no se elimina ya que hay una forekey entre productos
    public function deletecategoryAction($id) {

        try {


            $em = $this->getDoctrine()->getEntityManager();
            $category_repo = $em->getRepository("adminBundle:Category");
            $categories = $category_repo->find($id);


            //verificar si esta siendo utlizada la etiqueta para poder elimianarla
            if (count($categories->getProducts()) == 0) {
                $em->remove($categories);
                $em->flush();
            }
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
            echo "error de BD";
        }
        //var_dump(count($tags->getEntryTag()));
        return $this->redirectToRoute("listcategory");
    }



    public function editcategoryAction(Request $request, $id)
    {
        //se setean los datos para que sean enviados al formulario


        $em = $this->getDoctrine()->getEntityManager();
        $category_repo = $em->getRepository("adminBundle:Category");
        $categories_edit = $category_repo->find($id);


        $form = $this->createForm(CategoryType::class, $categories_edit); //llenar los datos al forumlario
        //var_dump(count($tags->getEntryTag()));

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,


                $categories_edit->setCode($form->get("code")->getData());
                $categories_edit->setName($form->get("name")->getData());
                $categories_edit->setDescription($form->get("description")->getData());
                $categories_edit->setActive($form->get("active")->getData());

                $em->persist($categories_edit);
                $flush = $em->flush();

                if ($flush == null) {
                    $estado = "la categoria se ha editado correctamente";
                } else {
                    $estado = "error a la editar la Categoria";
                }
            } else {
                $estado = "Error de Formulario";
            }
            $this->session->getFlashBag()->add("estado", $estado);//para los mensajes de confirmacion
            return $this->redirectToRoute("listcategory"); //redirigirnos a las lita
        }

        return $this->render("adminBundle:viewCategory:editCategory.html.twig", array(
            'form'=> $form->createView()
        ));
    }


public function editestadoAction($id, $active)
{


    $em = $this->getDoctrine()->getManager();
    $cursos_repo = $em->getRepository("adminBundle:Category");
    $curso = $cursos_repo->find($id);

    $curso->setActive($active);


    $em->persist($curso); //guarda los datos dentro de cotrine
    $flush = $em->flush(); //volcar los datos a la BD

    if ($flush != null) {
        echo "404 ";
    } else {
        echo "cambio de estado exitoso";
     return $this->redirectToRoute("listcategory");
    }
    $categories = $cursos_repo->findAll(); //listar todos
    return $this->render("adminBundle:viewCategory:listCategory.html.twig", array (
        'category'=>$categories));

}

}