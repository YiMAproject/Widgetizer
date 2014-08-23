<?php
namespace Widgetizer;

use Widgetizer\Listeners\WidgetizeAggregateListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package Widgetizer
 */
class Module implements
    InitProviderInterface,
    BootstrapListenerInterface,
    AutoloaderProviderInterface
{
    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        // Depends on yimaTheme Module
        $manager->loadModule('yimaTheme');

        $events = $manager->getEventManager();
        $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            array($this,'onLoadModulesPostAddServices'),
            -100000
        );
    }

    /**
     * @param ModuleEvent $e
     */
    public function onLoadModulesPostAddServices(ModuleEvent $e)
    {
        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $moduleManager = $e->getTarget();
        // $sharedEvents = $moduleManager->getEventManager()->getSharedManager();

        /** @var $sm ServiceManager */
        $sm      = $moduleManager->getEvent()->getParam('ServiceManager');
        // because of this is shared model and with cloning problem -
        // (we have to reset each model inside objects) in example, clear events -
        // on event manager or features on table gateway
        // this model service set with method with share by default as false value
        // on init module
        $sm->setInvokableClass('Widgetizer.Model.Widget', 'Widgetizer\Model\WidgetModel', false);
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface|MvcEvent $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $listenerAggr = new WidgetizeAggregateListener();

        $e->getApplication()
            ->getEventManager()
            ->attach($listenerAggr)
        ;
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
