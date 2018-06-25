<?php
class CarcheckController extends Yaf_Controller_Abstract
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
    	return $this->getView()->make('carcheck.list', $params);
    }

    /**
     * 返回前台list数据
     * @return [json] [description]
     */
    public function listJsonAction()
    {
    	$request = $this->getRequest();

    	$search = array(
    		'carid'	=> $request->getPost('carid', ''),
    		'operationtype'	=> $request->getPost('operationtype', ''),
    		'carcheckstatus'	=> $request->getPost('carcheckstatus', ''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
    		);

    	$Q = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$data = $Q->getCarcheckList($search);

    	echo json_encode($data);
    }


    public function editAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('id', 0);
    	if(!$id)
    	{
            COMMON::result('300', '请检查参数');
            return false;
    	}
        $action = '/Carcheck/editJson';
        $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $carCheckInstance -> getCarcheckByid($id);
        $params['id'] = $id;
        $params['action'] = $action;
        $attachInstance = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach  = $attachInstance -> getAttachByMAS('carCheck', 'aduit', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }

            $params['uploaded'] = join(',', $files);
        }
        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();
    	return $this->getView()->make('carcheck.edit', $params);
    }

    public function lookAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        if(!$id)
        {
            COMMON::result('300', '请检查参数');
            return false;
        }
        $action = '';
        $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $carCheckInstance -> getCarcheckByid($id);
        $params['id'] = $id;
        $params['action'] = $action;
        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();
        $attachInstance = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach  = $attachInstance -> getAttachByMAS('carCheck', 'aduit', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }

            $params['uploaded'] = join(',', $files);
        }
        return $this->getView()->make('carcheck.lookdetail', $params);
    }

    public function editJsonAction()
    {
    	$id = $this -> getRequest() -> getPost('id', 0);
        if(!$id){
            COMMON::result(300, '参数错误');
            return false;
        }
    	$params = [
            'audittime'  => $this -> getRequest() -> getPost('audittime', ''),
            'carcheckstatus' => $this -> getRequest() -> getParam('status', ''),
            'auditreason' => $this -> getRequest() -> getPost('auditreason', ''),
            'businesstype' => $this -> getRequest() -> getPost('businesstype', ''),
            'business_sysno' => $this -> getRequest() -> getPost('business_sysno', ''),
            'updated_at' => '=NOW()',
        ];
        if(!$params['businesstype']){
            echo json_encode(['code' => 300, 'message' => '参数错误2']);
            return false;
        }
        if(!$params['business_sysno']){
            echo json_encode(['code' => 300, 'message' => '参数错误3']);
            return false;
        }
        $attach = $this -> getRequest() -> getPost('attachment', array());
        if(!$params['auditreason'] && $params['carcheckstatus'] == 5){
            COMMON::result(300, '审核意见必填');
            return false;
        }

    	$carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    	$res = $carCheckInstance -> aduitCarcheck($params, $id, $attach);

    	if($res['code'] != 200){
			COMMON::result(300, '审核失败');
    	}else{
    		COMMON::result(200, '审核成功');
    	}
    }

    public function ajaxgetCarcheckAction()
    {
        $id = $this -> getRequest() -> getParam('id', 0);
        if(!$id){
            echo json_encode(['code' => 300, 'message' => '参数错误']);
            return false;
        }
        $search = array(
            'carcheckstatus' => 3,
            'businesstype' => $this -> getRequest() -> getParam('businesstype', ''),
            'business_sysno' => $this -> getRequest() -> getParam('business_sysno', ''),
            'auditreason' => '',
        );
        if(!$search['businesstype']){
            echo json_encode(['code' => 300, 'message' => '参数错误2']);
            return false;
        }
        if(!$search['business_sysno']){
            echo json_encode(['code' => 300, 'message' => '参数错误3']);
            return false;
        }
        #查询磅码单信息
        $pendcarInInstance = new PendcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $poundsInDetail = $pendcarInInstance -> getPoundInfoById($search['business_sysno']);
        if($poundsInDetail['quaulitycheck'] !=0 ){
            echo json_encode(['code' => 300, 'message' => '该单据已经品检, 不能返回上一步']);
            return false;
        }

        $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $carCheckInstance -> aduitCarcheck($search, $id, '', true);

        echo json_encode($data);
    }
}