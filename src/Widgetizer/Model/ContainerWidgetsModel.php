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
            ->order(ContainerWidgetsEntity::CONTAINER_ID.' '.$order)
        ;

        if ($offset)
            $select->offset($offset);
        if ($count)
            $select->limit($count);

        $result = $this->getTableGateway()->selectWith($select);

        return $result;
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
        $inc_order = function (&$order)
        {
            $order = ($order < 0) ? 0 : $order;
            $order ++;
            $order *= 5;
        };

        $order = $entity->get($entity::ORDER);
        $inc_order($order);

        $entity->set($entity::ORDER, $order);

        // shift other widgets order to next ----------------------------------\
        $where = function(\Zend\Db\Sql\Select $select) use ($entity, $order) {
            $select->where
                ->greaterThanOrEqualTo($entity::ORDER, $order)
                ->equalTo($entity::TEMPLATE,          $entity->get($entity::TEMPLATE))
                ->equalTo($entity::TEMPLATE_LAYOUT,   $entity->get($entity::TEMPLATE_LAYOUT))
                ->equalTo($entity::TEMPLATE_AREA,     $entity->get($entity::TEMPLATE_AREA))
                ->equalTo($entity::ROUTE_NAME,        $entity->get($entity::ROUTE_NAME))
                ->equalTo($entity::IDENTIFIER_PARAMS, $entity->get($entity::IDENTIFIER_PARAMS))
            ;
        };

        $rs = $this->getTableGateway()->select($where);
        /** @var $r ContainerWidgetsEntity */
        foreach ($rs as $r) {
            $eOrder = $r->get($entity::ORDER);
            $this->getTableGateway()->update(
                array($entity::ORDER => $eOrder + 5)
               ,array($entity::WIDGET_UID => $r->get($entity::WIDGET_UID))
            );
        }

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
        $where = $entity->getArrayCopy();
        unset($where[ContainerWidgetsEntity::ORDER]); // order not important to remove
        foreach($where as $f => $v) {
            if ($v === null) {
                // remove null fields
                unset($where[$f]);
            }
        }

        $this->getTableGateway()->delete($where);
    }
}
