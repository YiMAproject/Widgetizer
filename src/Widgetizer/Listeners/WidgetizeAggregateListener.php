<?php
namespace Widgetizer\Listeners;

use Widgetizer\Model\ContainerWidgetsEntity as CWE;
use Widgetizer\Model\Interfaces\ContainerWidgetsModelInterface;
use Widgetizer\Model\Interfaces\WidgetModelInterface;
use Widgetizer\Model\WidgetEntity;
use Widgetizer\Service\ParentalShare;
use Widgetizer\Service\ShareRegistery;
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
use Zend\View\Renderer\RendererInterface;

/**
 * Class WidgetizeAggregateListener
 * @package Widgetizer\Listeners
 */
class WidgetizeAggregateListener extends ParentalShare implements
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRenderHighlightAreas'), -9000);
    }

    /**
     * Insert Defined Widgets into Layout Sections(area)
     *
     * @param MvcEvent $e MVC Event
     *
     * @return bool
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
     * @param CWE       $r         Container Widget Entity
     * @param ViewModel $viewModel View Model
     *
     * @return bool
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
        if (ShareRegistery::isManagementAllowed()) {
            // Decorate widgets with ui management partial template
            $view = $this->getViewRenderer();
            $widgetViewModel = new ViewModel(array('widget' => $widget));
            $widgetViewModel->setTemplate('partial/builderfront/surround-widgets-decorator');
            $content = $view->render($widgetViewModel);
        } else {
            // Render Widget
            $content = $widget->render();
        }

        $viewModel->{$template_area} .= $content;
    }

    /**
     * Decorate Area For UI Management
     *
     * @param MvcEvent $e Event
     *
     * @return bool
     */
    public function onRenderHighlightAreas(MvcEvent $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response)
            return false;

        $viewModel = $e->getViewModel();
        if (! $viewModel instanceof ThemeDefaultInterface)
            return false;

        if (! ShareRegistery::isManagementAllowed())
            return false;

        $config     = $viewModel->config();
        $template   = $viewModel->getTemplate();
        $areaPlaces = array();
        if ($config->layouts && $config->layouts->{$template}) {
            if ($config->layouts->{$template}->areas) {
                $areaPlaces = $config->layouts->{$template}->areas;
                foreach($areaPlaces as $area) {
                    // editable area places
                    if (!$viewModel->{$area}) {
                        // fill for empty area template
                        $view = $this->getViewRenderer();
                        $deContent = new ViewModel(array('area' => $area));
                        $deContent->setTemplate('partial/builderfront/empty-area-decorator');
                        $content = $view->render($deContent);
                        $viewModel->{$area} = $content;
                    }
                }
            }
        }

        if ($areaPlaces) {
            foreach ($viewModel->getVariables() as $var => $content) {
                if (!in_array($var, $areaPlaces))
                    continue;
                $view = $this->getViewRenderer();
                $deContent = new ViewModel(array('area' => $var,'content' => $content));
                $deContent->setTemplate('partial/builderfront/surround-area-decorator');

                $content = $view->render($deContent);
                $viewModel->{$var} = $content;
            }
        } else {
            // No Need Management UI
            ShareRegistery::$isAllowedManagement = false;
        }
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

    /**
     * Get View Renderer
     *
     * @return RendererInterface
     */
    public function getViewRenderer()
    {
        $view = $this->sm->get('ViewRenderer');

        return $view;
    }
}
