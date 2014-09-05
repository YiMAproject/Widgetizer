<?php
namespace Widgetizer\Model;

use Widgetizer\Model\Interfaces\ContainerWidgetsModelInterface;
use Widgetizer\Model\TableGateway\ContainerWidgetsTable;
use yimaBase\Model\AbstractEventModel;
use yimaBase\Model\TableGatewayProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;
use Widgetizer\Model\TableGateway\WidgetTable;

/**
 * Class ContainerWidgetsModel
 * : Container Of Widgets for each template layout
 *
 * @package Widgetizer\Model
 */
class ContainerWidgetsModel extends AbstractEventModel
    implements
    ContainerWidgetsModelInterface,
    TableGatewayProviderInterface
{
    /**
     * @var  WidgetTable Gateway
     */
    protected $tableGateway;

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Get TableGateway
     *
     * @return WidgetTable
     */
    public function getTableGateway()
    {
        if (!$this->tableGateway) {
            $tableGateway = new ContainerWidgetsTable();
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
     * @param ContainerWidgetsEntity $entity Conditions
     * @param int $offset Offset
     * @param int $count Count
     *
     * @return ResultSet
     */
    public function find(ContainerWidgetsEntity $entity, $order = 'DESC', $offset = null, $count = null)
    {
        $order = ($order) ?: 'DESC';

        // create criteria condition from Entity
        $conditions = array();
        foreach ($entity as $key => $val) {
            if ($val === ContainerWidgetsEntity::getDefaultEmptyValue())
                continue;

            $conditions[$key] = $val;
        }

        // just not important
        unset($conditions[$entity::ORDER]);

        $select = $this->getTableGateway()->getSql()
            ->select()
            ->where($conditions)
            ->order(ContainerWidgetsEntity::ORDER.' '.$order)
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
     * @param ContainerWidgetsEntity $entity Entity Object
     * @param int $order Order
     *
     * @return mixed
     */
    public function changeOrder(ContainerWidgetsEntity $entity, $order)
    {
        $inc_order = function (&$order) {
            $order = ($order < 0) ? 0 : $order;
            $order ++;
            $order *= 5;
        };
        $inc_order($order);

        // Shift other Entities down
        $entity->set($entity::ORDER, $order);
        $this->reorder($entity, 1);

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
    }

    /**
     * Insert new entity
     *
     * @param ContainerWidgetsEntity $entity
     *
     * @return mixed
     */
    public function insert(ContainerWidgetsEntity $entity)
    {
        $order = $entity->get($entity::ORDER);

        $inc_order = function (&$order) {
            $order = ($order < 0) ? 0 : $order;
            $order ++;
            $order *= 5;
        };
        $inc_order($order);

        // Shift other Entities down
        $entity->set($entity::ORDER, $order);
        $this->reorder($entity, 1);

        // insert widget ------------------------------------------------------\
        $this->getTableGateway()->insert($entity->getArrayCopy());
    }

    /**
     * Delete widget by entity
     *
     * @param ContainerWidgetsEntity $entity
     *
     * @return mixed
     */
    public function delete(ContainerWidgetsEntity $entity)
    {
        $rs = $this->find($entity);
        foreach ($rs as $e) {
            // Shift other Entities up
            $this->reorder($e, -1);
        }

        $where = $entity->getArrayCopy();
        unset($where[ContainerWidgetsEntity::ORDER]); // order not important to remove
        foreach($where as $f => $v) {
            if ($v === $entity::getDefaultEmptyValue()) {
                // remove null fields
                unset($where[$f]);
            }
        }

        $this->getTableGateway()->delete($where);
    }

    protected function reorder(ContainerWidgetsEntity $entity, $flag)
    {
        $order = $entity->get($entity::ORDER);

        $newOrder = 5 * $flag;
        $entities = $this->find($entity);
        foreach($entities as $e) {
            $wc = function(\Zend\Db\Sql\Select $select) use ($e, $order) {
                $select->where
                    ->greaterThanOrEqualTo($e::ORDER, $order)
                    ->equalTo($e::TEMPLATE,          $e->get($e::TEMPLATE))
                    ->equalTo($e::TEMPLATE_LAYOUT,   $e->get($e::TEMPLATE_LAYOUT))
                    ->equalTo($e::TEMPLATE_AREA,     $e->get($e::TEMPLATE_AREA))
                    ->equalTo($e::ROUTE_NAME,        $e->get($e::ROUTE_NAME))
                    ->equalTo($e::IDENTIFIER_PARAMS, $e->get($e::IDENTIFIER_PARAMS))
                ;
            };

            $rs = $this->getTableGateway()->select($wc);

            /** @var $r ContainerWidgetsEntity */
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
