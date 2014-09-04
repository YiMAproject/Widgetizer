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
     * Name of request or query parameter containing identifier
     *
     * @var string
     */
    protected $identifierName = 'uid';

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
                    cwEntity::ORDER             => $data['order'],
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
     * Delete an existing resource
     *
     * @param  string $uid Widget UID
     *
     * @return mixed
     */
    public function delete($uid)
    {
        $exception = false;
        $message   = null;
        $result    = self::REST_SUCCESS;

        $sm = $this->getServiceLocator();

        try {
            /** @var $wm WidgetModel */
            $wm = $sm->get('Widgetizer.Model.Widget');
            $wm->delete(new WidgetEntity(
                array(
                    WidgetEntity::UID => $uid,
                )
            ));

            /** @var $cm ContainerWidgetsModel */
            $cm = $sm->get('Widgetizer.Model.ContainerWidgets');
            $cm->delete(new cwEntity(
                array(
                    cwEntity::WIDGET_UID => $uid,
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
     * Retrieve the identifier, if any
     *
     * Attempts to see if an identifier was passed in either the URI or the
     * query string, returning it if found. Otherwise, returns a boolean false.
     *
     * @param  \Zend\Mvc\Router\RouteMatch       $routeMatch
     * @param  \Zend\Http\PhpEnvironment\Request $request     Request
     *
     * @return false|mixed
     */
    protected function getIdentifier($routeMatch, $request)
    {
        $identifier = $this->getIdentifierName();
        $id = $routeMatch->getParam($identifier, false);
        if ($id !== false) {
            return $id;
        }

        $id = $request->getQuery()->get($identifier, false);
        if ($id !== false) {
            return $id;
        }

        // Get Identifier from heads, cause we have scrambled admin uri
        $header = $request->getHeader($identifier);
        if ($header) {
            return $header->getFieldValue();
        }

        return false;
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
