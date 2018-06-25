<?php
/**
 * User: HR
 * Date: 2017/07/25 
 * Time: 10:01
 */
class QueuebaseController extends Yaf_Controller_Abstract
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
    	$params = [];
    	return $this->getView()->make('queuebase.list', $params);
    }

    /**
     * 返回前台list数据
     * @return [json] [description]
     */
    public function listJsonAction()
    {
    	$request = $this->getRequest();

    	$search = array(
    		'queuetype'	=> $request->getPost('bar_queuetype', ''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
    		);

    	$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$data = $Q->getQueuebaseList($search);

    	echo json_encode($data);
    }


    public function editAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('id', 0);

    	if(!$id)
    	{
    		$action = '/Queuebase/newJson';
    		$params['action'] = $action;
    	}else{
    		$action = '/Queuebase/editJson';
    		$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    		$params = $Q->getQueuebaseByid($id);
    		$params['queuenoList'] = $Q->getQueueno($params['queuetype']);
    		$params['action'] = $action;
    		$params['id'] = $id;
    	}
    	return $this->getView()->make('queuebase.edit',$params);

    }


    public function newJsonAction()
    {
    	$request = $this->getRequest();
    	

        $data = array(
            'queuetype'          => $request->getPost('queuetype', ''),
            'queuetype_sysno'    => $request->getPost('queuetype_sysno', ''),
            'queueno'            => $request->getPost('queueno', ''),
            'goods_sysno'        => $request->getPost('goods_sysno', ''),
            'goodsname'          => $request->getPost('goodsname', ''),
            'queuetime'          => $request->getPost('queuetime', ''),
            'isdel'              => 0,
            'created_at'         => '=NOW()',
            'updated_at'         => '=NOW()',
            );


		$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

		$result = $Q->is_existence($data['queuetype'], $data['queuetype_sysno']);

		if($result)
		{
			COMMON::result(300, '该排号已经存在!');
			exit;
		}

    	$res = $Q->addQueuebase($data);

    	if($res){
    		COMMON::result(200, '添加成功');
    	}else{
    		COMMON::result(300, '添加失败');
    	}

    }


    public function editJsonAction()
    {
    	$request = $this->getRequest();

    	$id = $request->getRequest('id', 0);

    	$params = array(
    		'queuetime'  => $request->getPost('queuetime', ''),
    		'status'     => $request->getPost('status', ''),
    		'updated_at' => '=NOW()',
    		);

    	$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$res = $Q->updateQueuebase($params,$id);

    	if(!$res){
			COMMON::result(300, '修改失败');
    	}else{
    		COMMON::result(200, '修改成功');
    	}

    }


    public function getQueuenoAction()
    {
    	$request = $this->getRequest();
    	$queuetype = $request->getParam('queuetype', '');
    	$id = $request->getParam('sysno', '');

    	$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$data = $Q->getQueueno($queuetype,$id);
    	// var_dump($data);exit;
        $list[] = array('value'=>'','label'=>'请选择','goodsname'=>'','goods_sysno'=>'');
        foreach ($data as $key => $value) {
            $list[] = array(
            	'value'       =>$value['sysno'],
            	'label'       =>$value['name'],
            	);
        }
        if($id){
        	echo json_encode($data);
        	exit;
        }
    	echo json_encode($list);
    }



    public function queuebasechangeAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id', 0);
    	$action = $request->getParam('action', '');

    	if($action=='stop'){
    		$params['status'] = 2;
    	}elseif ($action=='start') {
    		$params['status'] = 1;
    	}elseif ($action=='del') {
    		$params['isdel'] = 1;
    	}

    	$Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$res = $Q->updateQueuebase($params,$id);

    	if(!$res){
			COMMON::result(300, '修改失败');
    	}else{
    		COMMON::result(200, '修改成功');
    	}

    }



    public function ajaxgetQueuebaseAction()
    {
        $request = $this->getRequest();

        $search = array(
            'queuetype' => $request->getPost('bar_queuetype', '1')==1 ? 2 : 1,
            );
        $Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));        
        $data = $Q -> getBaseinfo($search);

        echo json_encode($data);
    }
}