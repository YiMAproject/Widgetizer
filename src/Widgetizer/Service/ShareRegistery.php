<?php
namespace Widgetizer\Service;


class ShareRegistery extends ParentalShare
{
    /**
     * Is Allowed to implement ui management
     * @see \Widgetizer\Module::onBootstrap();
     *
     * @return bool
     */
    public static function isManagementAllowed()
    {
        return self::$isAllowedManagement;
    }
}
