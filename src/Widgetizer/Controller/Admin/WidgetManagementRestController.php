<?php
namespace Widgetizer\Controller\Admin;

use yimaWidgetator\Service;
use yimaWidgetator\View\Helper\WidgetAjaxy;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container as SessionContainer;
use Zend\Json;

class WidgetManagementRestController extends AbstractRestfulController
{
    const REST_SUCCESS = 'rest_success';

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->proccessData($data);
    }

    protected function proccessData($data)
    {
        d_e($data);

        $exception = false;
        $message   = self::REST_SUCCESS;
        $result    = null;

        try {
            // Call Widget
            $result = $this->processWidget(
                $this->getValidateData($data)
            );
        }
        catch (\Exception $e)
        {
            $exception = true;
            $message   = get_class($e);
            $result    = $e->getMessage();

            $this->response
                ->setStatusCode(417);
        }

        // set response
        $response = $this->response;
        $response->setContent(Json\Json::encode(array(
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
     * Validate Data
     *
     * @param array $data Data
     *
     * @return array
     *
     * @throws \yimaWidgetator\Service\Exceptions\UnauthorizedException
     * @throws \yimaWidgetator\Service\Exceptions\InvalidArgumentException
     */
    protected function getValidateData($data)
    {
        if (!isset($data['widget'])) {
            // : Widget Name
            throw new Service\Exceptions\InvalidArgumentException('{widget} param is absent.');
        }

        if (!isset($data['method'])) {
            // : Method must call from widget
            throw new Service\Exceptions\InvalidArgumentException('{method} param is absent.');
        }

        $params = array();
        if(isset($data['params'])) {
            // : Params that constructed widget
            if (is_array($data['params'])) {
                // ajaxq send object as array here
                $params = $data['params'];
            } else {
                $params = Json\Json::decode($data['params']);
            }
        }
        $data['params'] = $params;

        $data['interfunc'] = isset($data['interfunc']) ? $data['interfunc'] : array();
        $interfunc = $data['interfunc'];
        $interfunc = explode(';', $interfunc);
        $data['interfunc'] = array();
        foreach($interfunc as $if) {
            // key:value, value returned from (method) will returned as (key) in last result
            // call a method and append returned value to result array
            $if = explode(':', $if);
            if (count($if) > 2 || count($if) < 2)
                throw new Service\Exceptions\InvalidArgumentException('{interfunc} param is invalid.');
            $data['interfunc'][] = $if;
        }

        if (!$this->request->isXmlHttpRequest()) {
            // No Token Needed for ajax requests
            if (!isset($params['request_token'])) {
                throw new Service\Exceptions\UnauthorizedException('{request_token} param is absent.');
            } else {
                // validate token
                $token = $params['request_token'];

                $sesCont = new SessionContainer(WidgetAjaxy::SESSION_KEY);
                if (!$sesCont->offsetGet($token)) {
                    // invalid token
                    throw new Service\Exceptions\UnauthorizedException('{request_token} is mismatch.');
                }

                unset($params['request_token']);
            }
        }

        return $data;
    }
}
