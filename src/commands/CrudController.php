<?php

namespace luya\commands;

use Yii;
use yii\helpers\Console;

class CrudController extends \yii\console\Controller
{

    public function actionCreate()
    {
        $module = $this->prompt('Module Name (e.g. galleryadmin):');
        $modulePre = $new_str = preg_replace('/admin$/', '', $module);
        $modelName = $this->prompt('Model Name (e.g. Album)');
        $apiEndpoint = $this->prompt('Api Endpoint (e.g. api-'.$modulePre.'-'.strtolower($modelName).')');
        $sqlTable = $this->prompt('Database Table name (e.g. '.strtolower($modulePre).'_'.strtolower($modelName).')');
        
        if (!$this->confirm("Create '$modelName' controller, api & model based on sql table '$sqlTable' in module '$module' for api endpoint '$apiEndpoint'?")) {
            exit(1);
        }
        
        $shema = Yii::$app->db->getTableSchema($sqlTable);
        
        if (!$shema) {
            echo "you have to create a migration script and execute the migration first. the table must exists!";
            exit(0);
        }
        
        $yiiModule = Yii::$app->getModule($module);
        
        $basePath = $yiiModule->basePath;
        
        $ns = $yiiModule->getNamespace();
        
        $modelName = ucfirst($modelName);
        
        $modelNs = '\\' . $ns . '\\models\\' . $modelName;
        $data = [
            'api' => [
                'folder' => 'apis',
                'ns' => $ns . '\\apis',
                'file' => $modelName . 'Controller.php',
                'class' => $modelName . 'Controller',
                'route' => strtolower($module) . '-' . strtolower($modelName) . '-index',
            ],
            'controller' => [
                'folder' => 'controllers',
                'ns' => $ns . '\\controllers',
                'file' => $modelName . 'Controller.php',
                'class' => $modelName . 'Controller',
            ],
            'model' => [
                'folder' => 'models',
                'ns' => $ns . '\\models',
                'file' => $modelName . '.php',
                'class' => $modelName,
            ]
        ];
        $apiClass = null;
        foreach($data as $name => $item) {
            $folder = $basePath . DIRECTORY_SEPARATOR . $item['folder'];
            
            if (!file_exists($folder)) {
                mkdir($folder);
            }
            
            if (file_exists($folder . DIRECTORY_SEPARATOR . $item['file'])) {
                echo $this->ansiFormat("Can not create $folder" . DIRECTORY_SEPARATOR . $item['file'] . ", file does already exists!", Console::FG_RED) . PHP_EOL;
            } else {
                
                $content = '<?php' . PHP_EOL . PHP_EOL;
                $content.= 'namespace ' . $item['ns'] . ';' . PHP_EOL . PHP_EOL;
                switch($name) {
                    
                    case "api":
                        $content.= 'class '.$item['class'].' extends \admin\base\RestActiveController' . PHP_EOL;
                        $content.= '{' . PHP_EOL;
                        $content.= '    public $modelClass = \''.$modelNs.'\';' . PHP_EOL;
                        $content.= '}';
                        break;
                        
                    case "controller":
                        $content.= 'class '.$item['class'].' extends \admin\ngrest\base\Controller' . PHP_EOL;
                        $content.= '{' . PHP_EOL;
                        $content.= '    public $modelClass = \''.$modelNs.'\';' . PHP_EOL;
                        $content.= '}';
                        break;
                        
                    case "model":
                        
                        $names = [];
                        $ngrest = [
                            'text' => [],
                            'textarea' => [],
                        ];
                        foreach($shema->columns as $k => $v) {
                            if ($v->phpType == 'string') {
                                $names[] = $v->name;
                                if ($v->type == 'text') {
                                    $ngrest['textarea'][] = $v->name;
                                }
                                if ($v->type == 'string') {
                                    $ngrest['text'][] = $v->name;
                                }
                            }
                        }
                        
                        $content.= 'class '.$item['class'].' extends \admin\ngrest\base\Model' . PHP_EOL;
                        $content.= '{' . PHP_EOL;
                        $content.= '    /* yii model properties */'. PHP_EOL . PHP_EOL;
                        $content.= '    public static function tableName()' . PHP_EOL;
                        $content.= '    {' . PHP_EOL;
                        $content.= '        return \''.$sqlTable.'\';' . PHP_EOL;
                        $content.= '    }' . PHP_EOL . PHP_EOL;
                        $content.= '    public function rules()' . PHP_EOL;
                        $content.= '    {' . PHP_EOL;
                        $content.= '        return [' . PHP_EOL;
                        $content.= '            [[\''.implode("', '", $names).'\'], \'required\'],' . PHP_EOL;
                        $content.= '        ];' . PHP_EOL;
                        $content.= '    }' . PHP_EOL . PHP_EOL;
                        $content.= '    public function scenarios()' . PHP_EOL;
                        $content.= '    {' . PHP_EOL;
                        $content.= '        return [' . PHP_EOL;
                        $content.= '            \'restcreate\' => [\''.implode("', '", $names).'\'],' . PHP_EOL;http://luya.io/
                        $content.= '            \'restupdate\' => [\''.implode("', '", $names).'\'],' . PHP_EOL;
                        $content.= '        ];' . PHP_EOL;
                        $content.= '    }' . PHP_EOL . PHP_EOL;
                        $content.= '    /* ngrest model properties */'. PHP_EOL . PHP_EOL;
                        $content.= '    public function ngRestApiEndpoint()' . PHP_EOL;
                        $content.= '    {' . PHP_EOL;
                        $content.= '        return \''.$apiEndpoint.'\';' . PHP_EOL;
                        $content.= '    }' . PHP_EOL . PHP_EOL;
                        $content.= '    public function ngRestConfig($config)' . PHP_EOL;
                        $content.= '    {' . PHP_EOL;
                        foreach($ngrest['text'] as $n) {
                        $content.= '        $config->list->field(\''.$n.'\', \''.ucfirst($n).'\')->text()->required();'. PHP_EOL;
                        }
                        foreach($ngrest['textarea'] as $n) {
                        $content.= '        $config->list->field(\''.$n.'\', \''.ucfirst($n).'\')->textarea()->required();'. PHP_EOL;
                        }
                        $content.= '        $config->create->copyFrom(\'list\', [\'id\']);' . PHP_EOL;
                        $content.= '        $config->update->copyFrom(\'list\', [\'id\']);' . PHP_EOL;
                        $content.= '        return $config;' . PHP_EOL;
                        $content.= '    }' . PHP_EOL;
                        $content.= '}';
                        break;
                }
                
                if(file_put_contents($folder . DIRECTORY_SEPARATOR . $item['file'], $content)) {
                    echo $this->ansiFormat('- File ' . $folder . DIRECTORY_SEPARATOR . $item['file'] . ' created.', Console::FG_GREEN) . PHP_EOL;
                }
            }
            

        }
        
        $getMenu = 'public function getMenu()
{
    return $this->node(\''.ucfirst($modelName).'\', \'http://materializecss.com/icons.html\')
        ->group(\'GROUP\')
            ->itemApi(\'YOUR NAME\', \''.$data['api']['route'].'\', \'http://materializecss.com/icons.html\', \''.$apiEndpoint.'\')
    ->menu();
}
            ';
        
        $mname = $this->ansiFormat($basePath . '/Module.php', Console::BOLD);
        $a = $this->ansiFormat('$apis', Console::BOLD);
        echo PHP_EOL . 'Modify the '.$a.' var in ' . $mname . ' like below:' . PHP_EOL . PHP_EOL;
        echo $this->ansiFormat('public static $apis = [
    \''.$apiEndpoint.'\' => \''.$data['api']['ns'].'\\' . $data['api']['class'].'\',
];', Console::FG_YELLOW);
        echo PHP_EOL . PHP_EOL . 'Update the getMenu() method like below:' . PHP_EOL . PHP_EOL;
        echo $this->ansiFormat($getMenu, Console::FG_YELLOW);
        echo PHP_EOL;
    }
}