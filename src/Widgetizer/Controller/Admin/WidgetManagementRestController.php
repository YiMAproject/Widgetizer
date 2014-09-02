<?php
namespace Widgetizer\Controller\Admin;

use Widgetizer\Model\ContainerWidgetsEntity as cwEntity;
use Widgetizer\Model\ContainerWidgetsModel;
use Widgetizer\Model\WidgetEntity;
use Widgetizer\Model\WidgetModel;
use Widgetizer\Service\PersistStorage;
use yimaWidgetator\Service;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container as SessionContainer;
use Zend\Json;

class WidgetManagementRestController extends AbstractRestfulController
{
    const REST_SUCCESS = 'rest_success';
    const REST_FAILED  = 'rest_failed';

    /**
     * Create a new resource
     * : called from processPostData
     *
     * - Save widget
     *
     * @param  mixed $data
     * @return mixed
     */
    public function create($data)
    {
        $exception = false;
        $message   = null;
        $result    = self::REST_SUCCESS;

        $sm = $this->getServiceLocator();

        try {
            $data = $this->getValidatedPostData($data);

            /** @var $wm WidgetModel */
            $wm = $sm->get('Widgetizer.Model.Widget');
            $wm->insert(new WidgetEntity(
                array(
                    WidgetEntity::WIDGET => $data['widget'],
                    WidgetEntity::UID    => $data['uid'],
                )
            ));

            /** @var $cm ContainerWidgetsModel */
            $cm = $sm->get('Widgetizer.Model.ContainerWidgets');
            $cm->insert(new cwEntity(
                array(
                    cwEntity::TEMPLATE          => $data['template'],
                    cwEntity::TEMPLATE_LAYOUT   => $data['layout'],
                    cwEntity::TEMPLATE_AREA     => $data['area'],
                    cwEntity::ROUTE_NAME        => $data['route'],
                    cwEntity::IDENTIFIER_PARAMS => $data['identifier'],
                    cwEntity::WIDGET_UID        => $data['uid'],
                    cwEntity::ORDER             => 5,
                )
            ));
        } catch (\Exception $e)
        {
            $exception = true;
            $message   = $e->getMessage();
            $result    = self::REST_FAILED;

            $this->response
                ->setStatusCode(417);
        }

        // set response
        $response = $this->response;
        $response->setContent(Json\Json::encode(
            array(
                'exception' => $exception,
                'message'   => $message,
                'result'    => $result,
            )
        ));

        $header = new \Zend\Http\Header\ContentType();
        $header->value = 'Application/Json';
        $response->getHeaders()->addHeader($header);

        return $response;
    }

    /**
     * Validate and made Data
     *
     * @param array $data Post Data
     *
     * @return array
     */
    public function getValidatedPostData(array $data)
    {
        // Check against needed data >>>>>>
        $keyData = array(
            'token',
            'uid',
            'widget',
            'area',
        );

        if (array_diff($keyData, array_keys($data))) {
            throw new \InvalidArgumentException('Invalid request data provided.');
        }
        // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

        // Get Data From Persist Storage
        $sm = $this->getServiceLocator();
        /** @var $storage PersistStorage */
        $storage = $sm->get('Widgetizer.PersistStorage');
        $storage->setToken($data['token']);

        $data['template']   = $storage->getTemplate();
        $data['layout']     = $storage->getLayout();
        $data['route']      = $storage->getRoute();
        $data['identifier'] = $storage->getIdentifier();

        return $data;
    }
}
