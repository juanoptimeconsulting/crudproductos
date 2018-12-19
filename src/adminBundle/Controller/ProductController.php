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

    public function agregarentradasAction(Request $request) {

        $entry = new Product();
        $form = $this->createForm(ProductType::class, $entry); //llamar a la vista

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,
                $em = $this->getDoctrine()->getEntityManager();

                $entry = new Product();
                $entry->setTitle($form->get("title")->getData()); //seteo de los datos de FRM
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                //mandar la imagen a la BD
                $file = $form['image']->getData();
                $extension = $file->guessExtension(); //sacar la extension
                $file_name = time() . "." . $extension; //time para sacar un unico digito
                $file->move("uploads", $file_name); // moverlo a un directorio
                //en la bD poner el mismo nombre

                $entry->setImage($file_name);


                //para el combo box
                $categori_repo = $em->getRepository("blogBundle:Category");

                $category = $categori_repo->find($form->get("category")->getData());

                $entry->setCategory($category); //pasamos un objeto
                //sacar el usuario que hay logeado
                $user = $this->getUser();
                $entry->setUser($user);

                //otra manera de instanciar un objeto?
                $entry_repo = $em->getRepository("blogBundle:Entry");

                $em->persist($entry);
                $flush = $em->flush();


                $entry_repo->saveEntryTags(
                    $form->get("Tags")->getData(), $form->get("title")->getData(), $category, $user
                );


                if ($flush == null) {
                    $estado = "la Entrada se ha creado correctamente";
                } else {
                    $estado = "error a la añdir la Entrada";
                }
            } else {
                $estado = "Eror de Formlario";
            }
            $this->session->getFlashBag()->add("estado", $estado); //para los mensajes de confirmacion
            //return $this->redirectToRoute("listaentradas"); //redirigirnos a las lita
        }




        return $this->render("@admin/viewProduct/addProduct.html.twig", array(
            'form' => $form->createView()
        ));
    }

    //listar entradas
    public function listaEntryAction($page) {
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("blogBundle:Entry");

        $category_repo = $em->getRepository("blogBundle:Category"); //listar categorias en el menu

        //$entries = $entry_repo->findAll(); //listar entradas
        $pagesize = 5;
        $entries = $entry_repo->getPaginaterEntries($pagesize,$page);


        //mostrar los links
        $totalitems = count($entries);
        $pagesCount  =  ceil($totalitems/$pagesize);



        $categories = $category_repo->findAll(); //listar categorias en el menu
        return $this->render("blogBundle:EntryVista:listaEntry.html.twig", array(
            "entries" => $entries,
            "categories" => $categories,
            "totalitems"=> $totalitems,
            "pagecount"=>$pagesCount,
            "page" => $page,
            "pagesumar" => $page
        ));
    }

    public function deleteEntryAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("blogBundle:Entry");
        $entry_tag = $em->getRepository("blogBundle:EntryTag");

        $entry =  $entry_repo->find($id);
        $entrytags =   $entry_tag->findBy(array("entry"=>$entry));

        foreach ($entrytags as $entgs){

            $em->remove($entgs);
            $em->flush();
        }


        $em->remove($entry);
        $em->flush();


        return $this->redirectToRoute("listaentradas");


    }


    public function editEntryAction(Request $request, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("blogBundle:Entry");

        //para el combo box
        $categori_repo = $em->getRepository("blogBundle:Category");





        //generar el formulario en base a una entrada
        $entry = $entry_repo->find($id);


        //para recoger las tags (bucle)
        $tags  = "";
        foreach ($entry->getEntryTag() as $entryTas) {

            $tags.=$entryTas->getTag()->getName().",";

        }

        $form = $this->createForm(EntryType::class,$entry);
        $form->handleRequest($request);
        //comprovar si los datos son validos

        if ($form->isSubmitted()) {

            if ($form->isValid()) {//Aqui se lleva la validacion desde validation.yml,



                $entry->setTitle($form->get("title")->getData()); //seteo de los datos de FRM
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                //mandar la imagen a la BD
                $file = $form['image']->getData();
                $extension = $file->guessExtension(); //sacar la extension
                $file_name = time() . "." . $extension; //time para sacar un unico digito
                $file->move("uploads", $file_name); // moverlo a un directorio
                //en la bD poner el mismo nombre

                $entry->setImage($file_name);


                //para el combo box
                $category = $categori_repo->find($form->get("category")->getData());

                $entry->setCategory($category); //pasamos un objeto
                //sacar el usuario que hay logeado
                $user = $this->getUser();
                $entry->setUser($user);


                $em->persist($entry);
                $flush = $em->flush();


                //para listar las tags, aqui eliminamos y luego volvemos a meter la tag, claramente no se va a rrepetir la tag
                $entry_tag = $em->getRepository("blogBundle:EntryTag");
                $entrytags =   $entry_tag->findBy(array("entry"=>$entry));

                foreach ($entrytags as $entgs) {

                    $em->remove($entgs);
                    $em->flush();
                }



                $entry_repo->saveEntryTags(
                    $form->get("Tags")->getData(), $form->get("title")->getData(), $category, $user
                );


                if ($flush == null) {
                    $estado = "la Entrada se ha editado correctamente";
                } else {
                    $estado = "error al editar  la Entrada";
                }
            } else {
                $estado = "Error de Formulario";
            }
            $this->session->getFlashBag()->add("estado", $estado); //para los mensajes de confirmacion
            return $this->redirectToRoute("listaentradas"); //redirigirnos a las lita
        }




        return $this->render("blogBundle:EntryVista:editEntradas.html.twig", array(
            'form' => $form->createView(),
            'entry' => $entry,
            'Tags'=>$tags
        ));




    }

}
