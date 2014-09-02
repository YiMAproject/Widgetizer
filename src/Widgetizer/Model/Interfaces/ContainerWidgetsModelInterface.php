<?php
namespace Widgetizer\Model\Interfaces;

use Widgetizer\Model\ContainerWidgetsEntity;
use Zend\Db\ResultSet\ResultSet;

interface ContainerWidgetsModelInterface
{
    /**
     * Insert new entity
     *
     * @param ContainerWidgetsEntity $entity
     *
     * @return mixed
     */
    public function insert(ContainerWidgetsEntity $entity);

    /**
     * Finds widgets by given entity criteria
     *
     * @param ContainerWidgetsEntity $entity Conditions
     * @param string                 $order  Ordering
     * @param int                    $offset Offset
     * @param int                    $count  Count
     *
     * @return ResultSet
     */
    public function find(ContainerWidgetsEntity $entity, $order = 'DESC', $offset = null, $count = null);
}
