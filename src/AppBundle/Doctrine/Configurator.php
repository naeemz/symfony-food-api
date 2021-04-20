<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;  
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\RequestStack;

class Configurator  
{
    protected $em;
    protected $reader;
    protected $requestStack;

    public function __construct(EntityManagerInterface $em, Reader $reader, RequestStack $requestStack)
    {
        $this->em           = $em;
        $this->reader       = $reader;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest()
    {
        // sorting filter
        // $request = $this->requestStack->getCurrentRequest();
        // $allQueries = $request->query->all();
        // //
        // foreach($allQueries as $key => $val ) {
        //     $valUpper = strtoupper($val);
        //     if( $valUpper == 'ASC' || $valUpper == 'DESC' ) {
        //         $filter = $this->em->getFilters()->enable('global_sort_filter');
        //         $filter->setParameter('fieldName', $key);
        //         $filter->setParameter('sortField', $valUpper);
        //         // exit loop
        //         break;
        //     }
        // }

        $filter = $this->em->getFilters()->enable('deleted_filter');
        $filter->setAnnotationReader($this->reader);
    }
}
