<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class GlobalSortPersister
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;    
    }

    public function prePersist(LifecycleEventArg $args) 
    {
        $entity = $args->getEntity();
        
        dd($args);

        // sorting filter
        $request = $this->requestStack->getCurrentRequest();
        $allQueries = $request->query->all();
        //
        foreach($allQueries as $key => $val ) {
            $valUpper = strtoupper($val);
            if( $valUpper == 'ASC' || $valUpper == 'DESC' ) {
                $filter = $this->em->getFilters()->enable('global_sort_filter');
                $filter->setParameter('fieldName', $key);
                $filter->setParameter('sortField', $valUpper);
                // exit loop
                break;
            }
        }
    }
}