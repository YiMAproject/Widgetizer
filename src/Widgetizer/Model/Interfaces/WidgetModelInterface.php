<?php
namespace Widgetizer\Model\Interfaces;

use Widgetizer\Model\WidgetEntity;

interface WidgetModelInterface
{
    /**
     * Get Widget Entity By UID
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
