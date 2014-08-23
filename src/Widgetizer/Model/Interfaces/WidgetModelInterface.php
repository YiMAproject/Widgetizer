<?php
namespace Widgetator\Model\Interfaces;

use Widgetator\Model\WidgetEntity;
use yimaLocali\LocaleAwareInterface;

interface WidgetModelInterface
    extends
    LocaleAwareInterface // also locale injected by SM Initializer
{
    /**
     * Get Widget Entity By ID
     *
     * @param string $uid Unique Widget App. ID
     *
     * @return mixed
     */
    public function getWidgetByUid($uid);

    /**
     * Insert new widget entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function insert(WidgetEntity $widgetEntity);

    /**
     * Update an existing widget by entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function update(WidgetEntity $widgetEntity);

    /**
     * Delete widget by entity
     *
     * @param WidgetEntity $widgetEntity
     *
     * @return mixed
     */
    public function delete(WidgetEntity $widgetEntity);
}
