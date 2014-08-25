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
    public function find(ContainerWidgetsEntity $entity, $offset = null, $count = null)
    {
        // create criteria condition from Entity
        $conditions = array();
        foreach ($entity as $key => $val) {
            if ($val === ContainerWidgetsEntity::getDefaultEmptyValue())
                continue;

            $conditions[$key] = $val;
        }

        $result = $this->getTableGateway()->select($conditions);

        return $result;
    }
}
