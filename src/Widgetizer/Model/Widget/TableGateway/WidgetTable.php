<?php
namespace Widgetizer\Model\Widget\TableGateway;

use Poirot\Dataset;
use Widgetizer\Model\Widget;
use yimaBase\Db\TableGateway\AbstractTableGateway;
use yimaBase\Db\TableGateway\Feature\DmsFeature;
use Zend\Db\ResultSet\ResultSet;

class WidgetTable extends AbstractTableGateway
{
	# db table name
    protected $table = 'widgetizer_widgets';

	// this way you speed up running by avoiding metadata call to reach primary key
	// exp. usage in Translation Feature
	protected $primaryKey = Widget::WIDGET_ID;

    /**
     * @var Widget
     */
    protected $entityObject;

    /**
     * Init Table
     *
     * AddFeatures and .....
     */
    public function init()
    {
        $entityObject       = new Widget();
        $this->entityObject = $entityObject;

        // set default table columns on initialize, can used within features
        $this->columns      = array_keys($entityObject->getArrayCopy());

        // put this on last, reason is on pre(Action) manipulate columns raw dataSet
        $feature = new DmsFeature(array(), new WidgetDmsTable());
        $this->featureSet->addFeature($feature);
        $this->featureSet->setTableGateway($this);
    }

    /**
     * Post Initialize Table
     * - add WidgetEntity as Row Result Prototype
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
