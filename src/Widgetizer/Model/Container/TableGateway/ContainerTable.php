<?php
namespace Widgetizer\Model\Container\TableGateway;

use Poirot\Dataset;
use Widgetizer\Model\Container;
use yimaBase\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;

class ContainerTable extends AbstractTableGateway
{
	# db table name
    protected $table = 'widgetizer_container_widgets';

	// this way you speed up running by avoiding metadata call to reach primary key
	// exp. usage in Translation Feature
	protected $primaryKey = Container::CONTAINER_ID;

    /**
     * @var Container
     */
    protected $entityObject;

    /**
     * Init Table
     *
     * AddFeatures and .....
     */
    public function init()
    {
        $entityObject       = new Container();
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
