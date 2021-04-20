<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

class DeletedFilter extends SQLFilter
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
        if( empty($this->reader) ) {
            return '';
        }
        //
        // The Doctrine filter is called for any query on any entity
        // Check if the current entity is "deleted check" (marked with an annotation)
        $deletedAtCheck = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            'AppBundle\\Annotation\\DeletedCheck'
        );

        if (!$deletedAtCheck) {
            return '';
        }

        $fieldName = $deletedAtCheck->deletedAtFieldName;

        if (empty($fieldName)) {
            return '';
        }

        $query = sprintf('%s.%s IS NULL', $targetTableAlias, $fieldName);

        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
