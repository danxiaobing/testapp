<?php

class CustomerController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
         // $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
         // $test = $C->test();
         // var_dump($test);
         // exit;

    }

	/**
	 * 显示整个后台页面框架及菜单
	 *
	 * @return string
	 */
	public function listAction() {
		$params = array(
			'bar_no'=>'',
			'bar_name'=>''
		);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$search = array(
			'bar_status' => '1',
			'bar_isdel' => '0',
			'page' => false,
		);
		$list = $C->searchCustomercategory($search);
		$params['customercategorylist'] =  $list['list'];

		$this->getView()->make('customer.customerlist',$params);
	}

	public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $C->searchCustomer($search);

        echo json_encode($list);

	}

	public function listAllJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_status' => '1',
			'bar_isdel' => '0',
			'page' => false,

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $C->searchCustomer($search);

        echo json_encode($list['list']);

	}

	public function EditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id','0');

		if(!isset($id)) {
			$id = 0;
		}

		$C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/customer/newJson/";
			$params =  array();
		}

		else{
			$action = "/customer/editJson/";
			$params = $C->getCustomerById($id);
			$params['attach'] = array();

			$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

			$sysno = $id;
			$attach = $A->getAttachByMAS('customer','customercertificates',$sysno);
			$params['attach'] = array_merge($params['attach'],$attach);

			if( is_array($attach) && count($attach)){
				$files1 = array();
				foreach ($attach as $file){
					$files1[] = $file['sysno'];
				}

				$params['uploaded1']  =  join(',',$files1);
			}

			$sysno = $id;
			$attach = $A->getAttachByMAS('customer','customerlading',$sysno);
			$params['attach'] = array_merge($params['attach'],$attach);

            if( is_array($attach) && count($attach)){
				$files2 = array();
				foreach ($attach as $file){
					$files2[] = $file['sysno'];
				}

				$params['uploaded2']  =  join(',',$files2);
			}

		}

		$search = array(
			'bar_status' => '1',
			'bar_isdel' => '0',
			'page' => false,
		);
		$list = $C->searchCustomercategory($search);
		$params['customercategorylist'] =  $list['list'];
		$params['customerchannellist'] = array(
			0=>array('sysno'=>1,'channelname'=>'自有客户'),
			1=>array('sysno'=>2,'channelname'=>'渠道客户'),
		);
		$params['customerclasslist'] = array(
			0=>array('sysno'=>1,'classname'=>'重要'),
			1=>array('sysno'=>2,'classname'=>'一般'),
		);

		$customerGoods = $this->goodslistJsonAction($id);
		$params['customerGoods'] = $customerGoods;

		$search = array(
			'bar_status' => '1',
			'bar_isdel' => '0',
            'page' => false,
        );
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
		$params['employeelist'] = $list['list'];

		$user = Yaf_Registry::get(SSN_VAR);
		$params['user'] = $user;

        foreach ($list['list'] as $key => $value) {
        	if($value['sysno']==$params['created_user_sysno'])
        	{
        		$employeename = $value['employeename'];
        		break;
        	}
        }
		$params['employeename'] = $employeename;

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('customer.customeredit',$params);
    }
    public function showCustomerAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','0');

        if(!isset($id)) {
            $id = 0;
        }

        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if(!$id){
            $action = "/customer/newJson/";
            $params =  array();
        }

        else{
            $action = "/customer/editJson/";
            $params = $C->getCustomerById($id);
            $params['attach'] = array();

            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            $sysno = $id;
            $attach = $A->getAttachByMAS('customer','customercertificates',$sysno);
            $params['attach'] = array_merge($params['attach'],$attach);

            if( is_array($attach) && count($attach)){
                $files1 = array();
                foreach ($attach as $file){
                    $files1[] = $file['sysno'];
                }

                $params['uploaded1']  =  join(',',$files1);
            }

            $sysno = $id;
            $attach = $A->getAttachByMAS('customer','customerlading',$sysno);
            $params['attach'] = array_merge($params['attach'],$attach);

            if( is_array($attach) && count($attach)){
                $files2 = array();
                foreach ($attach as $file){
                    $files2[] = $file['sysno'];
                }

                $params['uploaded2']  =  join(',',$files2);
            }

        }

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $C->searchCustomercategory($search);
        $params['customercategorylist'] =  $list['list'];
        $params['customerchannellist'] = array(
            0=>array('sysno'=>1,'channelname'=>'自有客户'),
            1=>array('sysno'=>2,'channelname'=>'渠道客户'),
        );
        $params['customerclasslist'] = array(
            0=>array('sysno'=>1,'classname'=>'重要'),
            1=>array('sysno'=>2,'classname'=>'一般'),
        );

        $customerGoods = $this->goodslistJsonAction($id);
        $params['customerGoods'] = $customerGoods;

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $user = Yaf_Registry::get(SSN_VAR);
        $params['user'] = $user;

        foreach ($list['list'] as $key => $value) {
            if($value['sysno']==$params['created_user_sysno'])
            {
                $employeename = $value['employeename'];
                break;
            }
        }
        $params['employeename'] = $employeename;

        $params['id'] =  $id;
        $params['action'] =  $action;
        $params['views'] = 'look';
        $this->getView()->make('customer.customeredit',$params);
    }

    public function newJsonAction()
    {
        $request = $this->getRequest();
        $contactsdata = $request->getPost('contactsdata',"");
        $contactsdata = json_decode($contactsdata,true);
		if(count($contactsdata)==0) {
			COMMON::result(300,'业务联系人不能为空');
			return;
		}
		foreach ($contactsdata as $key => $value) {
			if(empty($value['contactsname']) ) {
				COMMON::result(300,'请先保存业务联系人');
				return;
			}
		}
		$goodsdata = $request->getPost('goodsdata',"");
		/*
		if($goodsdata=="" || $goodsdata=="undefined") {
			COMMON::result(300,'经营品种不能为空');
			return;
		}
        */

        $companydata = $request->getPost('companydata',"");
        $companydata = json_decode($companydata,true);

        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $input = array(
            'customerno' => COMMON::getCodeId('C'),
            'customername' => $request->getPost('customername', ''),
            'customerabbreviation' => $request->getPost('customerabbreviation', ''),
            'customerchannel' => $request->getPost('customerchannel', '1'),
            'customercategory_sysno' => $request->getPost('customercategory_sysno', ''),
            'customerclass' => $request->getPost('customerclass', '1'),
            'customerrelation' => $request->getPost('customerrelation', ''),
            'customerdeal' => $request->getPost('customerdeal', ''),
            'customerfax' => $request->getPost('customerfax', ''),
            'customercredit' => $request->getPost('customercredit', ''),
            'customerterm' => $request->getPost('customerterm', ''),
            'business_user_sysno' => $request->getPost('business_user_sysno', ''),
            'created_user_sysno' => $request->getPost('created_user_sysno', ''),
            'customerrepresentative' => $request->getPost('customerrepresentative', ''),
            'customerbank' => $request->getPost('customerbank', ''),
            'customeraccount' => $request->getPost('customeraccount', ''),
            'customertaxid' => $request->getPost('customertaxid', ''),
            'customeraddress' => $request->getPost('customeraddress', ''),
            'customertelephone' => $request->getPost('customertelephone', ''),
            'customerorganizationcode' => $request->getPost('customerorganizationcode', ''),
            'customerlicense' => $request->getPost('customerlicense', ''),
            'customercreditcodechecked' => $request->getPost('customercreditcodechecked', ''),
            'customercreditcode' => $request->getPost('customercreditcode', ''),
            'customermarks' => $request->getPost('customermarks', ''),
            'auditstatus' => $request->getPost('auditstatus', '1'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        #后台验证客户名称
		$search = array (
			'customername'=>$input['customername'],
			'page' => false,
		);
		$customername = $C->searchCustomer($search);
		if(!empty($customername['list'])){
			COMMON::result(300,'客户名称不能重复');
			return;
		}

        if ($id = $C->addCustomer($input,$contactsdata,$goodsdata,$companydata)) {
        	$attach =  $request->getPost('attachment',array());
            if(count($attach) > 0){
				$res = 	$A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}

            $row = $C->getCustomerById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function editJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$contactsdata = $request->getPost('contactsdata',"");
        $contactsdata = json_decode($contactsdata,true);
		if(count($contactsdata)==0) {
			COMMON::result(300,'业务联系人不能为空');
			return;
		}
		foreach ($contactsdata as $key => $value) {
			if(empty($value['contactsname']) ) {
				COMMON::result(300,'请先保存业务联系人');
				return;
			}
		}

		$goodsdata = $request->getPost('goodsdata',"");
		/*
		if($goodsdata=="" || $goodsdata=="undefined") {
			COMMON::result(300,'经营品种不能为空');
			return;
		}
        */

		$companydata = $request->getPost('companydata',"");
        $companydata = json_decode($companydata,true);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'customerno' => $request->getPost('customerno', COMMON::getCodeId('C')),
            'customername' => $request->getPost('customername', ''),
            'customerabbreviation' => $request->getPost('customerabbreviation', ''),
            'customerchannel' => $request->getPost('customerchannel', '1'),
            'customercategory_sysno' => $request->getPost('customercategory_sysno', ''),
            'customerclass' => $request->getPost('customerclass', '1'),
            'customerrelation' => $request->getPost('customerrelation', ''),
            'customerdeal' => $request->getPost('customerdeal', ''),
            'customerfax' => $request->getPost('customerfax', ''),
            'customercredit' => $request->getPost('customercredit', ''),
            'customerterm' => $request->getPost('customerterm', ''),
            'business_user_sysno' => $request->getPost('business_user_sysno', ''),
            'created_user_sysno' => $request->getPost('created_user_sysno', ''),
            'customerrepresentative' => $request->getPost('customerrepresentative', ''),
            'customerbank' => $request->getPost('customerbank', ''),
            'customeraccount' => $request->getPost('customeraccount', ''),
            'customertaxid' => $request->getPost('customertaxid', ''),
            'customeraddress' => $request->getPost('customeraddress', ''),
            'customertelephone' => $request->getPost('customertelephone', ''),
            'customerorganizationcode' => $request->getPost('customerorganizationcode', ''),
            'customerlicense' => $request->getPost('customerlicense', ''),
            'customercreditcodechecked' => $request->getPost('customercreditcodechecked', ''),
            'customercreditcode' => $request->getPost('customercreditcode', ''),
            'customermarks' => $request->getPost('customermarks', ''),
            'auditstatus' => $request->getPost('auditstatus', '1'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
			'updated_at'	=> 	'=NOW()'
		);

		#后台验证储罐编号
		$search = array (
				'customername' =>$input['customername'],
				'pageCurrent'=>COMMON::P(),
				'pageSize'=>COMMON::PR(),
				'orders'=> $request->getPost('orders','')
		);
		$customername = $C->searchCustomer($search);

		if(!empty($customername['list']) && $customername['list'][0]['sysno']!=$id){
			COMMON::result(300,'客户名称不能重复');
			return;
		}
 
		if($C->updateCustomer($id,$input,$contactsdata,$goodsdata,$companydata)){
			$attach =  $request->getPost('attachment',array());
            if(count($attach) > 0){
				$res = 	$A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}

			$row = $C->getCustomerById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
    }

	public function delJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
			'isdel' => 1,
			'updated_at'=>'=NOW()'
		);

		if($C->delCustomer($id,$input,$msg)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,$msg);
		}
    }

	public function categorylistAction() {
		$params = array(
			'bar_no'=>'',
			'bar_name'=>''
		);

		$this->getView()->make('customer.customercategorylist',$params);
	}

	public function categorylistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $C->searchCustomercategory($search);

        echo json_encode($list);

	}

	public function categoryEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$module = array();

		$S = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/customer/categorynewJson/";
			$params =  array();
		}

		else{
			$action = "/customer/categoryeditJson/";
			$params = $S->getCustomercategoryById($id);
		}

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('customer.customercategoryedit',$params);
    }

	public function categorynewJsonAction(){
		$request = $this->getRequest();

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'categoryname'=>  $request->getPost('categoryname',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'created_at'	=> 	'=NOW()',
			'updated_at'	=> 	'=NOW()'
		);

		if($id = $C->addCustomercategory($input)){
			$row = $C->getCustomercategoryById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
    }

    public function categoryeditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'categoryname'=>  $request->getPost('categoryname',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'updated_at'	=> 	'=NOW()'
		);
 
		if($C->updateCustomercategory($id,$input)){
			$row = $C->getCustomercategoryById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
    }

	public function categorydelJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
			'isdel' => 1
		);

		if($a = $C->delCustomercategory($id,$input,$msg)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,$msg);
		}
    }

    public function contactslistJsonAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$search = array (
			'bar_id'=>$id,
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'page' => false,
			'orders'  => $request->getPost('orders',''),

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $C->searchCustomercontacts($search);
		$list = $data['list'];

        echo json_encode($list);

	}

    public function contactsdelJsonAction(){
		$request = $this->getRequest();
		$json = $request->getPost('json',"");
		$id = $request->getPost('sysno',0);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'isdel' => 1
		);

		if($C->updateCustomercontacts($id,$input)){
			$row = $C->getCustomercontactsById($id);
			COMMON::result(200,'删除成功',$row);
		}else{
			COMMON::result(300,'删除失败');
		}
		
    }

    public function companylistJsonAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$search = array (
			'bar_id'=>$id,
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'page' => false,
			'orders'  => $request->getPost('orders',''),

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $C->searchCustomercompany($search);
		$list = $data['list'];

        echo json_encode($list);

	}

    public function companydelJsonAction(){
		$request = $this->getRequest();
		$json = $request->getPost('json',"");
		$id = $request->getPost('sysno',0);

		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'isdel' => 1
		);

		if($C->updateCustomercompany($id,$input)){
			$row = $C->getCustomercompanyById($id);
			COMMON::result(200,'删除成功',$row);
		}else{
			COMMON::result(300,'删除失败');
		}
		
    }

    public function goodslistAction()
    {
    	$request = $this->getRequest();
		$id = $request->getParam('id',0);

        $params = array(
        	'id'=>$id,
        );

        $this->getView()->make('customer.customergoodslist',$params);
    }

    public function basislistJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'goodsname' => $request->getPost('goodsname',''),
            'status' => 1,
            'isdel' => $request->getPost('isdel','0'),
            'page' => false,
            'orders'  => $request->getPost('orders',''),

        );
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $G->getBaseGoodslist($search);

        foreach($params['list'] as $row){
            if($row['parent_sysno'] ==0 )
                $row['parent_sysno'] = null;
            $list[] = $row;
        }


        echo json_encode($params);
    }

    public function goodslistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_id'=>$request->getParam('id',0),
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'page' => false,
			'orders'  => $request->getPost('orders',''),

		);
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $C->searchCustomergoods($search);
		// $list = $data['list'];

        return json_encode($data);

	}

    /*
     * @title 查询客户合同货品
     * @author wu xianneng
     */
	public function customergoodslistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'customer_sysno'=>$request->getPost('customer_sysno',0),
            'contract_sysno'=>$request->getPost('contract_sysno',0),
			'page' => false,
			'orders'  => $request->getPost('orders',''),

		);

//		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//		$data = $C->searchCustomergoods($search);

		$C = new ContractModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$data = $C->searchCustomerContractgoods($search);

		$list = $data;

        echo json_encode($list);

	}

	public function customercontractJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $search = array(
        	'bar_id'=>$id,
            'bar_contractstatus'=>5,
        	'bar_status' => 1,
			'bar_isdel' => 0,
            'page' => false,
        );

        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $C->searchCustomercontractlist($search);
		$list[] = array('value'=>'','label'=>'请选择');
		foreach ($data['list'] as $key => $value) {
			$list[] = array('value'=>$value['sysno'],'label'=>$value['contractnodisplay']);
		}
        echo json_encode($list);

    }

    /*
     *用于预约管理中查询客户已审核的有效合同
     * @author wu xianneng
     */
    public function customercontractJson2Action()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
		$contracttype = $request->getParam('contracttype', 0);
		$berthcosttype = $request->getParam('berthcosttype', '');

        $search = array(
            'bar_id'=>$id,
            'bar_contractstatus'=>5,
			'contracttype'=>$contracttype,
			'berthcosttype'=>$berthcosttype,
			'time'=>date('Y-m-d',strtotime('-1 days',time())),
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false,
        );

        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $data = $C->searchCustomercontractlist($search);
        $list[] = array('value'=>'','label'=>'请选择');
        foreach ($data['list'] as $key => $value) {
            $list[] = array('value'=>$value['sysno'],'label'=>$value['contractnodisplay']?$value['contractnodisplay']:$value['contractno']);
        }
        echo json_encode($list);

    }

    public function customercontractJson3Action()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $search = array(
            'bar_id'=>$id,
            'bar_contractstatus'=>5,
			'time'=>date('Y-m-d',strtotime('-1 days',time())),
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false,
        );

        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $data = $C->searchCustomercontractlist($search);
        $list[] = array('value'=>'-100','label'=>'全部');
        foreach ($data['list'] as $key => $value) {
            $list[] = array('value'=>$value['sysno'],'label'=>$value['contractnodisplay']?$value['contractnodisplay']:$value['contractno']);
        }
        echo json_encode($list);

    }

    public function statuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'status'=>1,//启用
        );
        $res = $C->updateStatus($parms);
        if($res){
            COMMON::result(200, '启用成功');
        }else{
            COMMON::result('300','启用失败');
        }
    }

    public function statusoverAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $parms = array(
            'ids'=>$ids,
            'status'=>2,//停用
        );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $C->updateStatus($parms);
        if($res){
            COMMON::result(200, '停用成功');
        }else{
            COMMON::result('300','停用失败');
        }
    }

    public function categorystatuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'status'=>1,//启用
        );
        $res = $C->categoryupdateStatus($parms);
        if($res){
            COMMON::result(200, '启用成功');
        }else{
            COMMON::result('300','启用失败');
        }
    }

    public function categorystatusoverAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $parms = array(
            'ids'=>$ids,
            'status'=>2,//停用
        );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $C->categoryupdateStatus($parms);
        if($res){
            COMMON::result(200, '停用成功');
        }else{
            COMMON::result('300','停用失败');
        }
    }

    public function ExcelAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'page'=>false,
        );
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $C->searchCustomercontacts($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询客户")
            ->setSubject("列表")
            ->setDescription("客户列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '客户名称'),
            array('B1:B1', 'B1', '005E9CD3', '传真'),
            array('C1:C1', 'C1', '005E9CD3', '分管业务员'),
            array('D1:D1', 'D1', '0094CE58', '法人代表'),
            array('E1:E1', 'E1', '0094CE58', '开户银行网点'),
            array('F1:F1', 'F1', '0094CE58', '开户账号'),
            array('G1:G1', 'G1', '0094CE58', '是否三证合一'),
            array('H1:H1', 'H1', '0094CE58', '社会信用代码/组织机构代码'),
            array('I1:I1', 'I1', '0094CE58', '客户地址'),
            array('J1:J1', 'J1', '003376B3', '电话'),
            array('K1:K1', 'K1', '003376B3', '纳税识别号'),
            array('L1:L1', 'L1', '003376B3', '业务联系人'),
            array('M1:M1', 'M1', '003376B3', '职位'),
            array('N1:N1', 'N1', '003376B3', '手机'),
            array('O1:O1', 'O1', '003376B3', 'Email'),
            array('P1:P1', 'P1', '003376B3', '座机'),
            array('Q1:Q1', 'Q1', '003376B3', '备注'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户列表');

        foreach ($mainTitle as $row) {
            $objActSheet->mergeCells($row[0]);
            $objActSheet->setCellValue($row[1], $row[3]);

            $objStyle = $objActSheet->getStyle($row[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }
        $line = 1;
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N','O','P','Q','R');

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['customername'];
                        break;
                    case 1:
                        $value = $item['customerfax'];
                        break;
                    case 2:
                        $value = $item['employeename'];
                        break;
                    case 3:
                        $value = $item['customerrepresentative'];
                        break;
                    case 4:
                        $value = " ".$item['customerbank'];
                        break;
                    case 5:
                        $value = " ".$item['customeraccount'];
                        break;
                    case 6:
                        if ($item['customercreditcodechecked']==1) {
                            $value = "是";
                        }else if ($item['customercreditcodechecked']==2) {
                            $value = "否";
                        }
                        break;
                    case 7:
                        $value = " ".$item['code'];
                        break;
                    case 8:
                        $value = $item['customeraddress'];
                        break;
                    case 9:
                        $value = " ".$item['customertelephone'];
                        break;
                    case 10:
                        $value = $item['customertaxid'];
                        break;
                    case 11:
                        $value = $item['contactsname'];
                        break;
                    case 12:
                        $value = $item['contactsposition'];
                        break;
                    case 13:
                        $value = $item['contactsmobilephone'];
                        break;
                    case 14:
                        $value = $item['contactsemail'];
                        break;
                    case 15:
                        $value = $item['contactstelephone'];
                        break;
                    case 16:
                        $value = $item['contactsmarks'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
                
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户列表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}
