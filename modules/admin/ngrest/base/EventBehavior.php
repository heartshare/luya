<?php

namespace admin\ngrest\base;

use yii\db\ActiveRecord;

class EventBehavior extends \yii\base\Behavior
{
    public $ngRestConfig = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'eventBeforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventBeforeUpdate',
            ActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
        ];
    }

    private $_instances = [];

    private function createPluginObject(array $plugin, $model)
    {
        $class = $plugin['class'];
        $args = $plugin['args'];
        if (array_key_exists($class, $this->_instances)) {
            return $this->_instances[$class];
        }

        $obj = new \ReflectionClass($class);
        $instance = $obj->newInstanceArgs($args);
        $this->_instances[$class] = $instance;
        $instance->setModel($model);

        return $instance;
    }

    public function eventAfterInsert($event)
    {
        $events = $this->ngRestConfig->getPlugins('create'); // ngCrud is create not insert

        foreach ($events as $field => $plugins) {
            foreach ($plugins as $plugin) {
                $obj = $this->createPluginObject($plugin, $event->sender);
                $response = $obj->onAfterCreate($event->sender->$field);
                if ($response !== false) {
                    $event->sender->$field = $response;
                }
            }
        }
    }

    public function eventBeforeInsert($event)
    {
        $events = $this->ngRestConfig->getPlugins('create'); // ngCrud is create not insert

        foreach ($events as $field => $plugins) {
            foreach ($plugins as $plugin) {
                $obj = $this->createPluginObject($plugin, $event->sender);
                $response = $obj->onBeforeCreate($event->sender->$field);
                if ($response !== false) {
                    $event->sender->$field = $response;
                }
            }
        }
    }

    public function eventBeforeUpdate($event)
    {
        $events = $this->ngRestConfig->getPlugins('update');

        foreach ($events as $field => $plugins) {
            foreach ($plugins as $plugin) {
                $obj = $this->createPluginObject($plugin, $event->sender);
                $response = $obj->onBeforeUpdate($event->sender->$field, $event->sender->getOldAttribute($field));
                if ($response !== false) {
                    $event->sender->$field = $response;
                }
            }
        }
    }

    public function eventAfterFind($event)
    {
        $events = $this->ngRestConfig->getPlugins('list'); // ngCrud ist list not find

        foreach ($events as $field => $plugins) {
            foreach ($plugins as $plugin) {
                $obj = $this->createPluginObject($plugin, $event->sender);
                $response = $obj->onAfterList($event->sender->$field);
                if ($response !== false) {
                    $event->sender->$field = $response;
                }
            }
        }
    }
}
