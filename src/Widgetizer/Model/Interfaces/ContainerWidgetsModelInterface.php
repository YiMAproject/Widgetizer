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
     * @param string                 $order  Ordering
     * @param int                    $offset Offset
     * @param int                    $count  Count
     *
     * @return ResultSet
     */
    public function find(ContainerWidgetsEntity $entity, $order = 'DESC', $offset = null, $count = null);

    /**
     * Change Entity Order and Shift Elements Orders
     *
     * @param ContainerWidgetsEntity $entity Entity Object
     * @param int                    $order  Order
     *
     * @return mixed
     */
    public function changeOrder(ContainerWidgetsEntity $entity, $order);

    /**
     * Insert new entity
     *
     * @param ContainerWidgetsEntity $entity
     *
     * @return mixed
     */
    public function insert(ContainerWidgetsEntity $entity);

    /**
     * Delete widget by entity
     *
     * @param ContainerWidgetsEntity $entity
     *
     * @return mixed
     */
    public function delete(ContainerWidgetsEntity $entity);
}
