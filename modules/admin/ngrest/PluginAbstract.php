<?php

namespace admin\ngrest;

abstract class PluginAbstract
{
    protected $id = null;

    protected $name = null;

    protected $alias = null;

    protected $ngModel = null;

    protected $gridCols = null;
    
    //public $options = [];

    /*
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
        $this->init();
    }

    public function init()
    {
    }
    */

    /*
    public function hasOption($key)
    {
        return (isset($this->options[$key])) ? true : false;
    }

    public function getOption($key)
    {
        return (isset($this->options[$key])) ? $this->options[$key] : false;
    }

    public function setOptions(array $optionsArray)
    {
        foreach ($optionsArray as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    public function setOption($key, $value)
    {
        if (!$this->getOption($key)) {
            throw new \Exception("The requested set key does not exists in options list");
        }

        $this->options[$key] = $value;
    }

    */

    private $_model = null;

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setConfig($id, $name, $ngModel, $alias, $gridCols)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ngModel = $ngModel;
        $this->alias = $alias;
        $this->gridCols = $gridCols;
    }

    public function onBeforeCreate($fieldValue)
    {
        return false;
    }

    public function onAfterCreate($fieldValue)
    {
        return false;
    }

    public function onBeforeUpdate($fieldValue, $oldValue)
    {
        return false;
    }

    public function onAfterList($fieldValue)
    {
        return false;
    }

    abstract public function renderList($doc);

    abstract public function renderCreate($doc);

    abstract public function renderUpdate($doc);
}
