<?php
namespace Widgetizer\Model\Widget;

use Widgetizer\Model\Widget;
use Widgetizer\Model\WidgetInterface;
use Zend\ServiceManager\ServiceManager;
use Widgetizer\Model\Widget\TableGateway\WidgetTable;
use yimaBase\Model\TableGatewayProviderInterface;
use yimaBase\Model\AbstractEventModel;

class Model extends AbstractEventModel
    implements
    WidgetInterface,
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
            $tableGateway = new WidgetTable();
            $this->tableGateway = $tableGateway;
        }

        if (!$this->isInitialize) {
            // Initialize Model and Prepare To Store And Retrieve Data
            $this->initialize();
        }

        return $this->tableGateway;
    }

    /**
     * Get Widget Entity By UID
     *
     * @param string $uid Unique Widget App. UID
     *
     * @return mixed
     */
    public function getWidgetByUid($uid)
    {
        $result = $this->getTableGateway()->select(array(Widget::UID => $uid));
        if ($result->count()) {
            $r = $result->current();

            return $r;
        }

        return false;
    }

    /**
     * Insert new widget entity
     *
     * @param Widget $widgetEntity
     *
     * @return mixed
     */
    public function insert(Widget $widgetEntity)
    {
        $this->getTableGateway()->insert($widgetEntity->getArrayCopy());
    }

    /**
     * Update an existing widget by entity
     *
     * @param Widget $widgetEntity
     *
     * @throws \Exception
     * @return mixed
     */
    public function update(Widget $widgetEntity)
    {
        $ev = $widgetEntity->get(Widget::WIDGET_ID);
        if ($ev) {
            $where = array(Widget::WIDGET_ID => $ev);
        } else {
            $ev = $widgetEntity->get(Widget::UID);
            if (!$ev) {
                throw new \Exception('Entity Widget Must Contains "widget_id" or "uid" to update.');
            }

            $where = array(Widget::UID => $ev);
        }

        $this->getTableGateway()->update($widgetEntity->getArrayCopy(), $where);
    }

    /**
     * Delete widget by entity
     *
     * @param Widget $widgetEntity
     *
     * @throws \Exception
     * @return mixed
     */
    public function delete(Widget $widgetEntity)
    {
        $where = $widgetEntity->getArrayCopy();
        foreach($where as $f => $v) {
            if ($v === null) {
                // remove null fields
                unset($where[$f]);
            }
        }

        $this->getTableGateway()->delete($where);
    }
}
