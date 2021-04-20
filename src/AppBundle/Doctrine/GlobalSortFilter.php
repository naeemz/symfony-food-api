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
        // get column names of current entity 
        // to compare and find if column exists
        $columnNames = $targetEntity->getColumnNames();
        // TODO: Check if column name exists
        //
        if (empty($fieldName)) {
            return '';
        }

        // TODO: Edit query to include ORDER BY
        // below is not correct because SQLFilter only allows where clause statements
        //$query = sprintf(' ORDER BY %s.%s %s', $targetTableAlias, $fieldName, $sortByOrder);
        //return $query;
        
        return '';
    }

}
