<?php

namespace admin\ngrest\plugins;

/**
 * @todo rename to SelectModel instead of SelectClass
 *
 * @author nadar
 */
class SelectClass extends \admin\ngrest\plugins\Select
{
    public function __construct($class, $valueField, $labelField)
    {
        if (is_object($class)) {
            $class = $class::className();
        }

        foreach ($class::find()->asArray()->all() as $item) {
            $this->data[] = [
                'value' => (int) $item[$valueField],
                'label' => $item[$labelField],
            ];
        }
    }
}
