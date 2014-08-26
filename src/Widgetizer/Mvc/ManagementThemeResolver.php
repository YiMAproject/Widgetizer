<?php
namespace Widgetizer\Mvc;

use yimaTheme\Resolvers\LocatorResolverAwareInterface;
use yimaTheme\Resolvers\ResolverInterface;
use yimaTheme\Theme\LocatorDefaultInterface;

/**
 * Class ManagementThemeResolver
 *
 * By loading template:
 * - Add Necessary assets for widgets management
 * - Include Template Area and Widgets Surround Decorator
 *
 * @package yimaAdminor\Mvc
 */
class ManagementThemeResolver implements
    ResolverInterface,
    LocatorResolverAwareInterface
{
    /**
     * @var \yimaTheme\Theme\Locator
     */
    protected $themeLocator;

    /**
     * Get default admin template name from merged config
     *
     * @return bool
     */
    public function getName()
    {
        $name = false;

        $sm = $this->themeLocator->getServiceLocator();
        $config = $sm->get('config');
        if (isset($config['widgetizer']) && is_array($config['widgetizer'])) {
            $config = $config['widgetizer'];
            $name   = (isset($config['management_template']))
                  ? $config['management_template']
                  : false;
        }

        // get registered PermissionsManager service and retrieve plugin
        $permissionsManager = $sm->get('yimaAuthorize.PermissionsManager');
        /** @var $permission \yimaAuthorize\Permission\PermissionInterface */
        $permission = $permissionsManager->get($config['authorize_permission']);
        if (!$permission->isAllowed()) {
            // user not authorized to permission
            return false;
        }

        return $name;
    }

    /**
     * Set theme locator
     *
     * @param LocatorDefaultInterface $l
     *
     * @return $this
     */
    public function setThemeLocator(LocatorDefaultInterface $l)
    {
        $this->themeLocator = $l;

        return $this;
    }
}
