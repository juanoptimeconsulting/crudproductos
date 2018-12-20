<?php

namespace adminBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;



class ProductRepository extends \Doctrine\ORM\EntityRepository {

//paginacion de productos
    public   function getPaginaterProducts($pagesizes = 5, $currentPage = 1){

        //CONSULTA CON DQL
        $em = $this->getEntityManager();

        $dql = "SELECT p FROM adminBundle\Entity\Product p ORDER BY p.id DESC";//consulta a un objeto
        $query = $em->createQuery($dql)//generar la consulta y calculo de paginas
        ->setFirstResult($pagesizes*($currentPage-1))
            ->setMaxResults($pagesizes);//limitar el numero de resultados



        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return $paginator;

    }



    public function getCategoryProducts($category, $pagesizes = 5, $currentPage = 1) {
        $em = $this->getEntityManager();
        $dql = "SELECT p FROM adminBundle\Entity\Product p"
            . "  WHERE p.category = :category ORDER BY p.id DESC";

        $query = $em->createQuery($dql)//generar la consulta y calculo de paginas
        ->setParameter("category", $category)
            ->setFirstResult($pagesizes * ($currentPage - 1))
            ->setMaxResults($pagesizes); //limitar el numero de resultados

        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return $paginator;
    }

}
