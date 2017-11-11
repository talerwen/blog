<?php
namespace Gii\controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller{
    private $fields = array();
    private $pk = '';
    public function index(){
        if(IS_POST){
            // 由于自定义模型类要有相应的数据表，所以这里自己验证
            $tableName = I('post.table_name');
            $moduleName = I('post.module_name');

            if($tableName && $moduleName){
                $db = M();
                $fields = $db->query('SHOW FULL FIELDS FROM '.$tableName);
                if($fields === FALSE){
                    $this->error('表不存在');
				}else{
                    $this->fields = $fields;
                    if(!($this->pk = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name =  '$tableName' AND COLUMN_KEY =  'PRI'")[0]['COLUMN_NAME'])){
                        $this->error('该表没有主键');
                    }
                }
                //创建 目录
                $moduleName = ucfirst($moduleName);
                $controllerDir = APP_PATH.$moduleName.'/Controller/';
                $modelDir = APP_PATH.$moduleName.'/Model/';
                $viewDir = APP_PATH.$moduleName.'/View/';
                if(!is_dir($controllerDir)){
                    mkdir($controllerDir,0777,TRUE);
                }
                if(!is_dir($modelDir)){
                    mkdir($modelDir,0777,TRUE);
                }
                if(!is_dir($viewDir)){
                    mkdir($viewDir,0777,TRUE);
                }
                // 拼接文件文件
                $mvcName = $this->_tableNameToMVCName($tableName);

                if( file_exists($controllerDir.$mvcName.'Controller.class.php')){
                    $this->error('控制器文件存在不能添加,请先删除源文件！');
                }
                if( file_exists($modelDir.$mvcName.'Model.class.php')){
                    $this->error('模型文件存在不能添加,请先删除源文件');
                }
                if( file_exists($viewDir.'/'.$mvcName.'/add.html')){
                    $this->error('添加的模板文件存在不能添加,请先删除源文件');
                }
                if( file_exists($viewDir.'/'.$mvcName.'/edit.html')){
                    $this->error('编辑的模板文件存在不能添加,请先删除源文件');
                }
                if( file_exists($viewDir.'/'.$mvcName.'/index.html')){
                    $this->error('列表的模板文件存在不能添加,请先删除源文件');
                }

                // 创建控制器文件
                ob_start();
                include(APP_PATH.'Gii/Template/Controller.php');
                $str = ob_get_clean();
                $re = file_put_contents($controllerDir.$mvcName.'Controller.class.php',"<?php \r\n".$str);
                if(!$re)$this->error('创建失败！');
                // 创建模型文件
                ob_start();
                include(APP_PATH.'Gii/Template/Model.php');
                $str = ob_get_clean();
                $re = file_put_contents($modelDir.$mvcName.'Model.class.php',"<?php \r\n".$str);
                if(!$re)$this->error('创建失败！');

                // 先生成静态页所在的控制器的目录
                if(!is_dir($viewDir.'/'.$mvcName))
                    mkdir($viewDir.'/'.$mvcName,0777,TRUE);

                // 创建模板文件
                ob_start();
                include(APP_PATH.'Gii/Template/add.html');
                $str = ob_get_clean();
                $re = file_put_contents($viewDir.$mvcName.'/add.html',$str);
                if(!$re)$this->error('创建失败！');

                ob_start();
                include(APP_PATH.'Gii/Template/edit.html');
                $str = ob_get_clean();
                $re = file_put_contents($viewDir.$mvcName.'/edit.html',$str);
                if(!$re)$this->error('创建失败！');

                ob_start();
                include(APP_PATH.'Gii/Template/index.html');
                $str = ob_get_clean();
                $re = file_put_contents($viewDir.$mvcName.'/index.html',$str);
                if(!$re)$this->error('创建失败！');
                $this->success('创建成功！');
                exit;
            }else{
                $this->error('请填写全表名与模块名称!');
            }
        }
        $this->display();
    }

    // 获取MVC的前缀名称
    private function _tableNameToMVCName($tableName){
        // bg_admin_name ---> AdminName
        // 1.去掉表前缀
        $tableName = str_replace(C('DB_PREFIX'),'',$tableName);
        // 2.去掉下划线
        $tableName = explode('_',$tableName);
        // 3.首字母大写
        $tableName = array_map('ucfirst',$tableName);
        // 4.将所有值连接起来
        $tableName = join('',$tableName);
        return $tableName;
    }

    private function getData(){
        $update = $insert = '';
        foreach($this->fields as $k => $v){
            if($v['Key'] == 'PRI') {
                $update .= ','.$v['Field'];
                continue;
            }
            if(strpos($v['Field'],'time') !== FALSE ){
                continue;
            }
            $insert .= ','.$v['Field'];
            $update .= ','.$v['Field'];
        }
        $data['insert'] = trim($insert,',');
        $data['update'] = trim($update,',');
        return $data;
    }

    private function getVerifyArray(){
        $str = '';
        foreach($this->fields as $k => $v){
            if($v['Key'] == 'PRI')
                continue;
            if($v['Null'] == 'NO'){
                if(strpos($v['Field'],'time') !== FALSE){
                    continue;
                }
                $str .= 'array("'.$v['Field'].'","require","'.$v['Comment'].'不能为空",1,"regex",3),'.$this->endSpace();
            }
            if($v['Key'] == 'UNI'){
                $str .= 'array("'.$v['Field'].'","","'.$v['Comment'].'已经存在",1,"unique",3),'.$this->endSpace();
            }
            if(strpos($v['Type'],'char') !== FALSE){
                preg_match('/\w+\((\d+)\)/',$v['Type'],$charlen);
                $str .= 'array("'.$v['Field'].'","1,'.$charlen[1].'","'.$v['Comment'].'不能超过'.$charlen[1].'个字符",1,"length",3),'.$this->endSpace();
            }
        }
        return $str;
    }

    // 拼接添加前的模型中的钩子函数字符串
    private function getBeforeInsert(){
        $str = '';
        foreach($this->fields as $k => $v){
            if(strpos($v['Field'],'pass') !== FALSE){
                $str .= '$data[\''.$v['Field'].'\'] = md5($data[\''.$v['Field'].'\'].C(\'salt\'));'.$this->endSpace();
                continue;
            }
            if(strpos($v['Field'],'add') !== FALSE && strpos($v['Field'],'time') !== FALSE){
                $str .=  '$data[\''.$v['Field'].'\'] = time();'.$this->endSpace();
                continue;
            }
            if($v['Type']=='text'){
                $data['content'] = clean_xss($_POST['content']);
                $str .=  '$data[\''.$v['Field'].'\'] = clean_xss($_POST[\''.$v['Field'].'\']);'.$this->endSpace();
                continue;
            }
    }
        return $str;
    }

    // 拼接更新前的模型中的钩子函数字符串
    private function getBeforeUpdate(){
        $str = '';
        foreach($this->fields as $k => $v){
            if(strpos($v['Field'],'pass') !== FALSE){
                $str .= 'if($data[\''.$v['Field'].'\']){
                    $data[\''.$v['Field'].'\'] = md5($data[\''.$v['Field'].'\'].C(\'salt\'));
                }else{
                    unset($data[\''.$v['Field'].'\']);
                }'.$this->endSpace();
                continue;
            }
            if(strpos($v['Field'],'save') !== FALSE && strpos($v['Field'],'time') !== FALSE){
                $str .=  '$data[\''.$v['Field'].'\'] = time();'.$this->endSpace();
                continue;
            }
            if($v['Type']=='text'){
                $data['content'] = clean_xss($_POST['content']);
                $str .=  '$data[\''.$v['Field'].'\'] = clean_xss($_POST[\''.$v['Field'].'\']);'.$this->endSpace();
                continue;
            }

        }
        return $str;
    }

    private function endSpace($len=8){
        return "\r\n".str_repeat(' ',$len);
    }

}