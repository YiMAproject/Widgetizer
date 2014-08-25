<?php
namespace Widgetizer\Model\Interfaces;

use Widgetizer\Model\ContainerWidgetsEntity;
use Zend\Db\ResultSet\ResultSet;

interface ContainerWidgetsModelInterface
{
    /**
     * Finds widgets by given entity criteria
     *
     * @param ContainerWidgetsEntity $entity Conditions
     * @param int                    $offset Offset
     * @param int                    $count  Count
     *
     * @return ResultSet
     */
    public function find(ContainerWidgetsEntity $entity, $offset = null, $count = null);
}
