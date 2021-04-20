<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class GlobalSortFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        //
        $fieldName      = $this->getParameter('fieldName');
        $sortByOrder    = $this->getParameter('sortField');

        if (empty($fieldName)) {
            return '';
        }

        //$query = sprintf(' ORDER BY %s.%s %s', $targetTableAlias, $fieldName, $sortByOrder);

        //return $query;
        return '';
    }

}
