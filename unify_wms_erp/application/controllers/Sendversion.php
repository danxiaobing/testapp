<?php 
/**
 * 汇签版本配置
 * User:hr
 * Date: 2017/01/17 
 * Time: 13:30
 */

class SendversionController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    /**
    * 列表展示
    */
    public function listAction()
    {
    	$params=array();

        $this->getView()->make('system.sendversion.list', $params);
    }

    /**
    *  版本信息
    */

    public function versoionlistJsonAction()
    {
        $request = $this->getRequest();

    	$search = array(
            'versionname' => $request->getPost('versionname',''),
            'status'  =>  $request->getPost('status',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            );

        $S = new SendversionModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

    	$data = $S->getVersionInfo($search);

    	echo json_encode($data);

    }

    public function versioneditAction()
    {
    	$request = $this->getRequest();

    	$id = $request->getParam('id',0);

    	if(!$id){
    		$action = '/sendversion/add/';
    		$params = array();
            $params['status'] = 3;

    	}else{
    		$S = new SendversionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $action = "/sendversion/edit/";
            $params = $S->getVersionById($id);

            $params['id'] = $id;    		
    	}

    	$params['sysno'] = $id;

    	$params['action'] = $action;


    	$this->getView()->make('system.sendversion.edit',$params);

    }

    /**
    * 添加版本
    */

    public function AddAction()
    {
        $request = $this->getRequest();

        $detail_data = json_decode($request->getPost('sendversion-detail-data',''));

        $versionno = COMMON::getCodeId(' ');

        $input = array(
            'versionname' => $request->getPost('versionname',''),
            'versionno' => $versionno,
            'versionshow' => $request->getPost('versionshow','1'),
            'versiontype' => $request->getPost('versiontype',''),
            'status'  =>  $request->getPost('status','3'),
            'isdel'=>  $request->getPost('isdel','0'),
            'created_at'	=> 	'=NOW()',
            'updated_at'	=> 	'=NOW()'
        );
//         var_dump($input);
//         exit;
        $S = new SendversionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        
        if($S->isonly($request->getPost('versionname',''))){
            COMMON::result(300,'版本名称重复!');
            exit;

        }
        $id = $S->add($input);

        $C = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        foreach ($detail_data as  $value) {

            $review_config = array(

                'department_sysno' => $value->department_sysno,
                'departmentname' => $value->departmentname,
                'version_sysno' => $id,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                'memo' => $value->memo,
                );

            $C->add($review_config);

        }


        if($id){
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }    	
    }


    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $versionshow = $request->getPost('versionshow','1');

        $status = $request->getPost('status','1');

        $input = array(
            'versionname' => $request->getPost('versionname',''),
            'versionno' => $request->getPost('versionno',''),
            'versionshow' => ($versionshow+1),
            'status'  =>  $status,
            'updated_at'	=> 	'=NOW()'
        );

        $C = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $S = new SendversionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $sink = $C->getSinkInfo(array('status'=>1,'vid'=>$id));

        if($status==3){

            $detail_data = json_decode($request->getPost('sendversion-detail-data',''));

            $C->del($id);

            foreach ($detail_data as  $value) {

                $review_config = array(

                    'department_sysno' => $value->department_sysno,
                    'departmentname' => $value->departmentname,
                    'version_sysno' => $id,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'memo' => $value->memo,
                    );

                $C->add($review_config);

            }

        }

        if($id = $S->update($id,$input)){
            $row = $S->getVersionById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }  

    }


    public function versionchangeAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id',0);

        $state = $request->getParam('state','');
        $versiontype = $request->getParam('versiontype','');
        $status = $request->getParam('status','');

        if($status == 3){
            echo  3;
            exit;

        }

    	$S = new SendversionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	if($S->change($id,$state,$versiontype)){
            echo 1;
    	}else{
            echo 2;
    	}
    }

    /**
        选择汇签部门
    */
    public function sinkeditAction()
    {
        $params = array();

        $search = array(
            'status' => '1',
        );
        $S = new SendversionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $data = $S->getdepartmentinfo();

        $params['data'] = $data;

        // var_dump($params['list']);
        // exit;
        return $this->getView()->make('system.sendversion.detailedit',$params);
    }


    public function sinkdeailAction()
    {
        $request = $this->getRequest();
        
        $vid = $request->getParam('vid','');

        if(!$vid){
            $vid = '-100';
        }

        $search = array(
            'status' => '1',
            'vid' => $vid,
            );

        $S = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $data['list'] = $S->getSinkInfo($search);


        echo json_encode($data);
    }

}
