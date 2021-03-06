<?php
namespace Widgetizer\Service;

use Zend\Session\AbstractContainer;
use Zend\Session\Container as SessionContainer;

/**
 * Class PersistStorage
 * : store/fetch data within next request calls
 *
 * @package Widgetizer\Service
 */
class PersistStorage
{
    /**
     * @var AbstractContainer
     */
    protected $storage;

    /**
     * @var string Generated Token
     */
    protected $token;

    /**
     * Set Current Template
     *
     * @param string $template Template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->getStorage()->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->getStorage()->template;
    }

    /**
     * Set Current Layout
     *
     * @param string $layout Layout
     *
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->getStorage()->layout = $layout;

        return $this;
    }

    public function getLayout()
    {
        return $this->getStorage()->layout;
    }

    /**
     * Set Current Route Name
     *
     * @param string $route Route Name
     *
     * @return $this
     */
    public function setRoute($route)
    {
        $this->getStorage()->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->getStorage()->route;
    }

    /**
     * Set Identifier
     *
     * @param string $identifier Specific Identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->getStorage()->identifier = $identifier;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->getStorage()->identifier;
    }

    /**
     * Get Storage
     *
     * @return AbstractContainer
     */
    public function getStorage()
    {
        if (!$this->storage) {
            /** @var $session AbstractContainer */
            $session = new SessionContainer($this->getToken());
            $session->setExpirationHops(1);

            $this->storage = $session;
        }

        return $this->storage;
    }

    /**
     * Get Token
     *
     * @return string
     */
    public function getToken()
    {
        if (!$this->token) {
            $this->token = $this->generateToken();
        }

        return $this->token;
    }

    /**
     * Set Token
     *
     * @param string $token Token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Generate Token
     *
     * @return string
     */
    protected function generateToken()
    {
        $uniqStr = function($length) {
            $char = "abcdefghijklmnopqrstuvwxyz";
            $char = str_shuffle($char);
            for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
                $rand .= $char{mt_rand(0, $l)};
            }

            return $rand;
        };

        return $uniqStr(24);
    }
}
