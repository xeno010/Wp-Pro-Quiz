<?php

/**
 * Created by PhpStorm.
 * User: xeno
 * Date: 08.10.2014
 * Time: 17:53
 */
class WpProQuiz_Model_PluginContainer extends WpProQuiz_Model_Model
{

    private $_data = array();

    public function get($name, $default = null)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    public function add($name, $value)
    {
        $this->_data[$name] = $value;

        return $this;
    }

    public function set($values)
    {
        $this->_data = $values;

        return $this;
    }

    public function exists($name)
    {
        return isset($name);
    }
} 