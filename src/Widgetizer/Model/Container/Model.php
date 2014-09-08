<?php
namespace Widgetizer\Model\Container;

use Widgetizer\Model\Container;
use Widgetizer\Model\ContainerInterface;
use Widgetizer\Model\Container\TableGateway\ContainerTable;
use yimaBase\Model\AbstractEventModel;
use yimaBase\Model\TableGatewayProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ContainerWidgetsModel
 * : Container Of Widgets for each template layout
 *
 * @package Widgetizer\Model
 */
class Model extends AbstractEventModel
    implements
    ContainerInterface,
    TableGatewayProviderInterface
{
    /**
     * @var ContainerTable Gateway
     */
    protected $tableGateway;

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Get TableGateway
     *
     * @return ContainerTable
     */
    public function getTableGateway()
    {
        if (!$this->tableGateway) {
            $tableGateway = new ContainerTable();
            $this->tableGateway = $tableGateway;
        }

        if (!$this->isInitialize) {
            // Initialize Model and Prepare To Store And Retrieve Data
            $this->initialize();
        }

        return $this->tableGateway;
    }

    /**
     * Finds widgets by given entity criteria
     *
     * @param Container $entity Conditions
     * @param string $order
     * @param int $offset Offset
     * @param int $count Count
     *
     * @return ResultSet
     */
    public function find(Container $entity, $order = 'ASC', $offset = null, $count = null)
    {
        $order = ($order) ?: 'DESC';

        // create criteria condition from Entity
        $conditions = array();
        foreach ($entity as $key => $val) {
            if ($val === Container::getDefaultEmptyValue())
                continue;

            $conditions[$key] = $val;
        }

        // just not important
        unset($conditions[$entity::ORDER]);

        $select = $this->getTableGateway()->getSql()
            ->select()
            ->where($conditions)
            ->order(Container::ORDER.' '.$order)
        ;

        if ($offset)
            $select->offset($offset);
        if ($count)
            $select->limit($count);

        $result = $this->getTableGateway()->selectWith($select);

        return $result;
    }

    /**
     * Change Entity Order and Shift Elements Orders
     *
     * @param Container $entity Entity Object
     * @param int $order Order
     *
     * @return mixed
     */
    public function changeOrder(Container $entity, $order)
    {
        $inc_order = function (&$order) {
            $order = ($order < 0) ? 0 : $order;
            $order ++;
            $order *= 5;
        };
        $inc_order($order);
        $entity->set($entity::ORDER, $order);

        // Set new order -----------------------------------------------------------\
        $where = $entity->getArrayCopy();
        unset($where[$entity::ORDER]);
        foreach($where as $f => $v) {
            if ($v === $entity::getDefaultEmptyValue()) {
                // remove null fields
                unset($where[$f]);
            }
        }

        $this->getTableGateway()->update(array($entity::ORDER => $order), $where);

        // Shift other Entities down
        $this->reorder($entity, 1);
    }

    /**
     * Insert new entity
     *
     * @param Container $entity
     *
     * @return mixed
     */
    public function insert(Container $entity)
    {
        $order = $entity->get($entity::ORDER);

        $inc_order = function (&$order) {
            $order = ($order < 0) ? 0 : $order;
            $order ++;
            $order *= 5;
        };
        $inc_order($order);
        $entity->set($entity::ORDER, $order);

        // insert widget ------------------------------------------------------\
        $this->getTableGateway()->insert($entity->getArrayCopy());

        // Shift other Entities down
        $this->reorder($entity, 1);
    }

    /**
     * Delete widget by entity
     *
     * @param Container $entity
     *
     * @return mixed
     */
    public function delete(Container $entity)
    {
        // Shift other Entities up
        $this->reorder($entity, -1);

        $where = $entity->getArrayCopy();
        unset($where[Container::ORDER]); // order not important to remove
        foreach($where as $f => $v) {
            if ($v === $entity::getDefaultEmptyValue()) {
                // remove null fields
                unset($where[$f]);
            }
        }

        $this->getTableGateway()->delete($where);
    }

    protected function reorder(Container $entity, $flag)
    {
        $newOrder = 5 * $flag;
        $entities = $this->find($entity);
        /** @var $e Container */
        foreach($entities as $e) {
            $order = $e->get($entity::ORDER);

            $wc = function(\Zend\Db\Sql\Select $select) use ($e, $entity, $order) {
                $select->where
                    ->greaterThanOrEqualTo($e::ORDER, $order)
                    ->equalTo($e::TEMPLATE,          $e->get($e::TEMPLATE))
                    ->equalTo($e::TEMPLATE_LAYOUT,   $e->get($e::TEMPLATE_LAYOUT))
                    ->equalTo($e::TEMPLATE_AREA,     $e->get($e::TEMPLATE_AREA))
                    ->equalTo($e::ROUTE_NAME,        $e->get($e::ROUTE_NAME))
                    ->equalTo($e::IDENTIFIER_PARAMS, $e->get($e::IDENTIFIER_PARAMS))

                    ->notEqualTo($e::WIDGET_UID, $entity->get($e::WIDGET_UID))
                ;
            };

            $rs = $this->getTableGateway()->select($wc);

            /** @var $r Container */
            foreach ($rs as $r) {
                $eOrder = $r->get($e::ORDER);
                $this->getTableGateway()->update(
                    array($e::ORDER => $eOrder + $newOrder)
                    ,array($e::WIDGET_UID => $r->get($e::WIDGET_UID))
                );
            }
        }
    }
}
