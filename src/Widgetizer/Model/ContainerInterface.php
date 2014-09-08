<?php
namespace Widgetizer\Model;

use Zend\Db\ResultSet\ResultSet;

interface ContainerInterface
{
    /**
     * Finds widgets by given entity criteria
     *
     * @param Container $entity Conditions
     * @param string                 $order  Ordering
     * @param int                    $offset Offset
     * @param int                    $count  Count
     *
     * @return ResultSet
     */
    public function find(Container $entity, $order = 'DESC', $offset = null, $count = null);

    /**
     * Change Entity Order and Shift Elements Orders
     *
     * @param Container $entity Entity Object
     * @param int                    $order  Order
     *
     * @return mixed
     */
    public function changeOrder(Container $entity, $order);

    /**
     * Insert new entity
     *
     * @param Container $entity
     *
     * @return mixed
     */
    public function insert(Container $entity);

    /**
     * Delete widget by entity
     *
     * @param Container $entity
     *
     * @return mixed
     */
    public function delete(Container $entity);
}
