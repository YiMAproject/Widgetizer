<?php
namespace Widgetizer\Listeners;

use Widgetizer\Model\ContainerWidgetsEntity as CWE;
use Widgetizer\Model\Interfaces\ContainerWidgetsModelInterface;
use Widgetizer\Model\Interfaces\WidgetModelInterface;
use Widgetizer\Model\WidgetEntity;
use yimaTheme\Theme\ThemeDefaultInterface;
use yimaWidgetator\Service\WidgetManager;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Model\ViewModel;

/**
 * Class WidgetizeAggregateListener
 * @package Widgetizer\Listeners
 */
class WidgetizeAggregateListener implements
    ServiceManagerAwareInterface,
    ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRenderWidgetizer'), -9000);
    }

    /**
     * Insert Defined Widgets into Layout Sections(area)
     *
     * @param MvcEvent $e MVC Event
     *
     * @throws \Exception
     */
    public function onRenderWidgetizer(MvcEvent $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response) {
            return false;
        }

        $viewModel = $e->getViewModel();
        if (! $viewModel instanceof ThemeDefaultInterface) {
            return false;
        }

        // Get widgets loaded into each area by template and layout ... {
        /** @var $cntModel ContainerWidgetsModelInterface */
        $cntModel = $this->sm->get('Widgetizer.Model.ContainerWidgets');

        $criteria = new CWE(array(
            CWE::TEMPLATE        => $viewModel->getName(),
            CWE::TEMPLATE_LAYOUT => $viewModel->getTemplate(),
        ));
        $result = $cntModel->find($criteria);
        foreach ($result as $r) {
            // Render Widget
            $this->renderWidget($r, $viewModel);
        }
        // ... }
    }

    /**
     * Render Widget From Container Result
     *
     * @param CWE $r
     */
    protected function renderWidget(CWE $r, ViewModel $viewModel)
    {
        /** @var $widgetManager WidgetManager */
        $widgetManager = $this->sm->get('yimaWidgetator.WidgetManager');

        /** @var $widgetModel WidgetModelInterface */
        $widgetModel = $this->sm->get('Widgetizer.Model.Widget');
        /** @var $w WidgetEntity */
        $w = $widgetModel->getWidgetByUid( $r->get(CWE::WIDGET_UID) );
        if (!$w || !$widgetManager->has($w->get(WidgetEntity::WIDGET))) {
            // we don't have a widget with this name registered.
            // ...
            return false;
        }

        // get widget from widgetManager by Widget Name Field
        /** @var $widget WidgetInterface */
        $widget = $widgetManager->get($w->get(WidgetEntity::WIDGET));
        if (method_exists($widget, 'setFromArray')) {
            // load prop. entities into widget
            $widget->setFromArray($w->getArrayCopy());
        }
        $template_area = $r->get(CWE::TEMPLATE_AREA);
        $viewModel->{$template_area} .= $widget->render();
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
}
