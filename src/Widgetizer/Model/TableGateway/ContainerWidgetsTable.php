<?php
namespace Widgetizer\Model\TableGateway;

use Poirot\Dataset;
use Widgetizer\Model\ContainerWidgetsEntity;
use yimaBase\Db\TableGateway\AbstractTableGateway;
use yimaBase\Db\TableGateway\Feature\DmsFeature;
use Zend\Db\ResultSet\ResultSet;
use Widgetizer\Model\WidgetEntity;

class ContainerWidgetsTable extends AbstractTableGateway
{
	# db table name
    protected $table = 'widgetizer_container_widgets';

	// this way you speed up running by avoiding metadata call to reach primary key
	// exp. usage in Translation Feature
	protected $primaryKey = ContainerWidgetsEntity::CONTAINER_ID;

    /**
     * @var ContainerWidgetsEntity
     */
    protected $entityObject;

    /**
     * Init Table
     *
     * AddFeatures and .....
     */
    public function init()
    {
        $entityObject       = new ContainerWidgetsEntity();
        $this->entityObject = $entityObject;

        // set default table columns on initialize, can used within features
        $this->columns      = array_keys($entityObject->getArrayCopy());
    }

    /**
     * Post Initialize Table
     * - add ContainerWidgetEntity as Row Result Prototype
     */
    public function postInit()
    {
        if (!$this->resultSetPrototype instanceof ResultSet) {
            // this table work with ResultSet
            $this->resultSetPrototype = new ResultSet;
        }

        // add WidgetEntity as Row Result Prototype
        $this->resultSetPrototype
           ->setArrayObjectPrototype($this->entityObject);
    }
}
