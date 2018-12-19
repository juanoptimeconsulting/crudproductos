<?php

namespace adminBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;



class ProductRepository extends \Doctrine\ORM\EntityRepository {

//paginacin de productos
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
}
