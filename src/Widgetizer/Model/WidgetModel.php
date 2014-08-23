<?php
namespace Widgetator\Model;

use yimaBase\Model\AbstractEventModel;
use yimaBase\Model\TableGatewayProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Widgetator\Model\Interfaces\WidgetModelInterface;
use Widgetator\Model\TableGateway\UserTable;
use Widgetator\Model\TableGateway\WidgetTable;

class WidgetModel extends AbstractEventModel
    implements
    WidgetModelInterface,
    TableGatewayProviderInterface
{
    /**
     * @var string Locale Language
     */
    protected $language;

    /**
     * @var  UserTable Gateway
     */
    protected $tableGateway;

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Get TableGateway
     *
     * @return UserTable|WidgetTable
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
        $result = $this->getTableGateway()->select(array(WidgetEntity::UID => $uid));
        if ($result->count()) {
            $r = $result->current();

            return $r;
        }

        return false;
    }

    /**
     * Insert new widget entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function insert(WidgetEntity $widgetEntity)
    {
        $this->getTableGateway()->insert($widgetEntity->getArrayCopy());
    }

    /**
     * Update an existing widget by entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function update(WidgetEntity $widgetEntity)
    {
        $ev = $widgetEntity->get(WidgetEntity::WIDGET_ID);
        if ($ev) {
            $where = array(WidgetEntity::WIDGET_ID => $ev);
        } else {
            $ev = $widgetEntity->get(WidgetEntity::UID);
            if (!$ev) {
                throw new \Exception('Entity Widget Must Contains "widget_id" or "uid" to update.');
            }

            $where = array(WidgetEntity::UID => $ev);
        }

        $this->getTableGateway()->update($widgetEntity->getArrayCopy(), $where);
    }

    /**
     * Delete widget by entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function delete(WidgetEntity $widgetEntity)
    {
        $ev = $widgetEntity->get(WidgetEntity::WIDGET_ID);
        if ($ev) {
            $where = array(WidgetEntity::WIDGET_ID => $ev);
        } else {
            $ev = $widgetEntity->get(WidgetEntity::UID);
            if (!$ev) {
                throw new \Exception('Entity Widget Must Contains "widget_id" or "uid" to delete.');
            }

            $where = array(WidgetEntity::UID => $ev);
        }

        $this->getTableGateway()->delete($where);
    }

    // -- implemented LocaleAwareInterface ----------------------------------------------------------------------------

    /**
     * Set Locale
     *
     * @param $locale
     *
     * @return static
     */
    public function setLocale($locale)
    {
        $this->language = (string) $locale;
    }
}
