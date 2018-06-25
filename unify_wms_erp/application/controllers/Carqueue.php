<?php
/**
 * 
 * User: HR
 * Date: 2017/07/26 
 * Time: 11:12
 */
class CarqueueController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();

    }


    public function listAction()
    {
        $Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));        
        $params['queuelist'] = $Q ->getBaseinfo(array('queuetype'=>2));
        $params['data'] =  json_encode([]);
    	return $this->getView()->make('carqueue.list', $params);
    }

    public function listJsonAction()
    {
    	$request = $this->getRequest();

    	$search = array(
    		'doc_type'    => $request->getPost('doc_type', ''),
    		'tp_sysno'	  => $request->getPost('tp_sysno', ''),
            'page'        => $request->getPost('page', ''),
    		);

        $C = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $data = $C->getCarqueueList($search);

        echo json_encode($data);

    }

    //排队位置操作
    public function carqueueChangeAction()
    {
        $request = $this->getRequest();
        $action = $request->getPost('action', '');
        $key = $request->getPost('key', '');
        $data = json_decode($request->getPost('data', []), true);
        // var_dump($data);exit;
        if(empty($data['sysno']))
        {
            COMMON::result(300, '参数错误!');
            exit;
        }
        $C = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $res = $C->updateCarqueue($data, $action, $key);

        if($res){
            COMMON::result(200, '操作成功!');
        }else{
            COMMON::result(300, '操作失败,请刷新页面重试!');
        }
    }

    //取消排队
    public function carqueuedelAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        if(empty($id))
        {
            COMMON::result(300, '参数错误!');
            exit;
        }
        $C = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $data = array(
            'isdel'      => 1,
            'updated_at' => '=NOW()',
            );
        $res = $C->update($data,$id);

        if($res){
            COMMON::result(200, '操作成功!');
        }else{
            COMMON::result(300, '操作失败,请刷新页面重试!');
        }        
    }


    public function carrecordListAction()
    {
        return $this->getView()->make('carqueue.carrecord', []);
    }


    public function carrecordlistJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'doc_type'    => $request->getPost('doc_type', ''),
            'carstatus'   => $request->getPost('carstatus', '1'),
            'carid'   => $request->getPost('carid', ''),
            'begin_time'  => $request->getPost('begin_time', ''),
            'end_time'    => $request->getPost('end_time', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize'    => COMMON:: PR(),            
            );

        $C = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $data = $C->getCarrecordList($search);

        echo json_encode($data);
    }


    public function carrecordChangeAction()
    {
        $request = $this->getRequest();

        $id = $request->getPost('id', '');
        $action = $request->getPost('action', '');

        if(empty($id))
        {
            COMMON::result(300, '参数错误!');
            exit;
        }

        if($action=='void')
        {
            $params = array(
                'carstatus'   => 3,
                'updated_at'  => '=NOW()',
                );
        }elseif ($action=='over') {
            $params = array(
                'carstatus'   => 2,
                'updated_at'  => '=NOW()',
                );
        }

        $C = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $res = $C->updateCarrecord($params, $id);

        if($res){
            COMMON::result(200, '操作成功!');
        }else{
            COMMON::result(300, '操作失败,请刷新页面重试!');
        }
    }
}