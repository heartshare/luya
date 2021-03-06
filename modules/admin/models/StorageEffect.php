<?php

namespace admin\models;

/**
 * This is the model class for table "admin_group".
 *
 * @property integer $group_id
 * @property string $name
 * @property string $text
 */
class StorageEffect extends \admin\ngrest\base\Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_storage_effect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'identifier', 'imagine_name', 'imagine_json_params'], 'required'],
        ];
    }

    public function scenarios()
    {
        return [
            'restcreate' => ['name', 'identifier', 'imagine_name', 'imagine_json_params'],
            'restupdate' => ['name', 'identifier', 'imagine_name', 'imagine_json_params'],
        ];
    }

    // ngrest

    public function ngRestApiEndpoint()
    {
        return 'api-admin-effect';
    }

    public function ngRestConfig($config)
    {
        $config->list->field('name', 'Name')->text()->required();
        $config->list->field('identifier', 'Unique Identifier')->text()->required();
        $config->list->field('imagine_name', 'imagine_name')->text()->required();
        $config->list->field('imagine_json_params', 'imagine_json_params')->text();
        $config->list->field('id', 'ID')->text();

        return $config;
    }
}
