<?php

namespace Phpro\AnnotatedCache\Interception;

/**
 * Class ProxyInterceptionSuffix
 *
 * @package Phpro\AnnotatedCache\Interception
 */
class ProxyInterceptionSuffix implements InterceptionSuffixInterface
{
    /**
     * @var mixed
     */
    private $instance;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var mixed
     */
    private $returnValue;

    /**
     * ProxyInterceptionSuffix constructor.
     */
    public function __construct($instance, $method, $params, $returnValue)
    {
        $this->instance = $instance;
        $this->method = $method;
        $this->params = $params;
        $this->returnValue = $returnValue;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }
}