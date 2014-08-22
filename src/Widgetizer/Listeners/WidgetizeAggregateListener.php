<?php
namespace Widgetizer\Listeners;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Class WidgetizeAggregateListener
 * @package Widgetizer\Listeners
 */
class WidgetizeAggregateListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRenderWidgetator'), -9000);
    }

    /**
     * Insert Defined Widgets into Layout Sections(area)
     *
     * @param MvcEvent $e MVC Event
     * @throws \Exception
     */
    public function onRenderWidgetator(MvcEvent $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        $viewModel = $e->getViewModel();
        if (! $viewModel instanceof ViewModel) {
            return;
        }

        return false;

        // load widgets into {
        $themeObject  = $this->manager->getThemeObject();
        if (!$themeObject) {
            // we are not attained theme
            return;
        }

        $sm = $this->sm;

        /*
         * [
         *  'layout_name' =>
         *      [
         *          'area' => [
         *              toStringObject,
         *              ViewModel,
         *          ]
         *      ]
         * ]
         */
        $widgetsContainer = array();
        do {
            // get merged widgets of child themes
            $widgetsContainer = ArrayUtils::merge($widgetsContainer, (array) $themeObject->getParam('widgets'));
        } while($themeObject = $themeObject->getChild());

        $layout           = $viewModel->getTemplate();
        $areas            = isset($widgetsContainer[$layout]) ? $widgetsContainer[$layout] : array();
        foreach($areas as $area => $widgets)
        {
            if (! is_array($widgets) ) {
                // convert it to array for itterate over
                $widgets = array($widgets);
            }

            foreach ($widgets as $w) {
                if (is_string($w)) {
                    if ($sm->has($w))
                        $w = $sm->get($w);
                    elseif (class_exists($w))
                        $w = new $w();
                }

                if (is_object($w)) {
                    if ($w instanceof ViewModel)
                        $viewModel->addChild($w, $area, true);
                    elseif (method_exists($w, '__toString'))
                        $w = (string) $w;
                    else
                        throw new \Exception('Invalid Widget Provided, Widget "'.get_class($w).'" is not toString implemented or ViewModel instance.');
                }

                if (is_string($w))
                    $viewModel->{$area} .= $w;
                elseif (! $w instanceof ViewModel)
                    throw new \Exception('Invalid Widget Provided, Widget "'.gettype($w).'"');
            }
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
}
