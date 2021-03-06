<?php

namespace fortuneBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * QuoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuoteRepository extends EntityRepository {

    protected $name = "quote"; //short name used for the query

    /**
     * Find all quotes by date inversed
     * 
     * @return  object
     */

    public function myFindAllQuotes() {
        // prepare the query
        $q = $this->createQueryBuilder($this->name)
                ->orderBy($this->name . ".date", 'DESC')
        ;

        return $q->getQuery()->getResult();
    }

    /**
     * Find last quotes 
     * 
     * @return  object
     */
    public function myFindLastQuote() {
        // prepare the query
        $q = $this->createQueryBuilder($this->name)
                ->orderBy($this->name . ".id", 'DESC')
                ->setMaxResults(1)
        ;

        return $q->getQuery()->getSingleResult();
    }

}
