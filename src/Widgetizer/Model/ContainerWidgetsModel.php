<?php
namespace Widgetizer\Model;

use Widgetizer\Model\Interfaces\ContainerWidgetsModelInterface;
use Widgetizer\Model\TableGateway\ContainerWidgetsTable;
use yimaBase\Model\AbstractEventModel;
use yimaBase\Model\TableGatewayProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
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
