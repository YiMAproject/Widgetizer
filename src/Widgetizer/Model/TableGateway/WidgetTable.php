<?php
namespace Widgetator\Model\TableGateway;

use Poirot\Dataset;
use yimaBase\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;
use Widgetator\Model\WidgetEntity;

class WidgetTable extends AbstractTableGateway
{
	# db table name
    protected $table = 'widgetator_widgets';

	// this way you speed up running by avoiding metadata call to reach primary key
	// exp. usage in Translation Feature
	protected $primaryKey = WidgetEntity::WIDGET_ID;

    /**
     * @var WidgetEntity
     */
    protected $entityObject;

    /**
     * Init Table
     *
     * AddFeatures and .....
     */
    public function init()
    {
        $entityObject       = new WidgetEntity();
        $this->entityObject = $entityObject;

        // set default table columns on initialize, can used within features
        $this->columns      = array_keys($entityObject->getArrayCopy());
    }

    /**
     * Post Initialize Table
     *
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
