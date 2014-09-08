<?php
namespace Widgetizer\Model;

interface WidgetInterface
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
     * @param Widget $widgetEntity
     *
     * @return mixed
     */
    public function insert(Widget $widgetEntity);

    /**
     * Update an existing widget by entity
     *
     * @param Widget $widgetEntity
     *
     * @return mixed
     */
    public function update(Widget $widgetEntity);

    /**
     * Delete widget by entity
     *
     * @param Widget $widgetEntity
     *
     * @return mixed
     */
    public function delete(Widget $widgetEntity);
}
