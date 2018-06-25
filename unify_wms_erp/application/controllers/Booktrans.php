<?php
/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/12/7 0007
 * Time: 9:36
 */
class BooktransController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
        $this->request = $this->getRequest();
        $this->m = new BooktransModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $this->prefix = $this->getRequest()->getControllerName().'_'.$this->getRequest()->getActionName().'_';
    }

    public function listAction()
    {
        $params = array();
        $this->getView()->make('booktrans.list', $params);
    }

    /**
     * @title 查询已审核的货存转移预约单
     */
    public function detailAction(){
        $request = $this->getRequest();
        $search = array(
            'bookingtransno' => $request->getPost('bookingtransno', ''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'bookingtransstatus' => 4
        );
        $reservation = new BooktransModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $reservation->getList($search);

        echo json_encode($data);
    }
    
    /**
     * 预约单查询
     */
    public function booklistAction(){
         $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        $this->getView()->make('booktrans.booklist', $params);
    }
    /**
     * 列表数据
     */
    public function booklistJsonAction() {
        $request = $this->getRequest();
        $search = array (
            'bookingtransdate_start'=>$request->getPost('bookingtransdate_start',''),
            'bookingtransdate_end' => $request->getPost('bookingtransdate_end',''),
            'sale_customer_sysno' => $request->getPost('sale_customer_sysno',''),
            'buy_customer_sysno' => $request->getPost('buy_customer_sysno',''),
            'bookingtransstatus'=>$request->getPost('bookingtransstatus',''),            
            'orders'  => $request->getPost('orders',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
        );
        $list = $this->m->getList($search);
    
        echo json_encode($list);
    
    }
    /**
     * 添加和修改
     */
    public function editAction(){
        $id = $this->request->getParam('id', '0');
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        if($id){
            $params = $this->m->getTransById($id);

            //附件
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('booktrans','edit-1',$id);//回单
            $attach2 = $A->getAttachByMAS('booktrans','edit-2',$id);//提货单

            if( is_array($attach1) && count($attach1)){
				$files1 = array();
				foreach ($attach1 as $file){
					$files1[] = $file['sysno'];
				}
				$params['attach1']  =  join(',',$files1);
			}
			if( is_array($attach2) && count($attach2)){
			    $files2 = array();
			    foreach ($attach2 as $file){
			        $files2[] = $file['sysno'];
			    }
			    $params['attach2']  =  join(',',$files2);			    
			}
			$params['prefix'] = $this->prefix.'1_';
			
			$search = array(
                'bar_id'=>$params['buy_customer_sysno'],
                'bar_contractstatus'=>6,
                'bar_status' => 1,
                'bar_isdel' => 0,
                'page' => false
                );
			$params['contractlist'] = $C->searchCustomercontractlist($search);

			//print_r($params);exit;
            $params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['sale_customer_sysno']);
			
			//print_r($params);exit;
        }else{
            $params['prefix'] = $this->prefix.'2_';
        }

        
        //客户列表
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        //有效合同客户列表
        $search = array(
            'contractstatus'=>6,
            'contractenddate'=>'NOW()',
            'page' =>false,
            'bar_status'=>1,
            'bar_isdel' => 0,
        
        );
        $list = $C->searchCustomerContract($search);
        $params['customerlistContract'] =  $list;
        //print_r(array_keys($params));exit;        

        //$params['codeid'] = COMMON ::getCodeId('B');
        $this->getView()->make('booktrans.edit', $params);
    }
    /**
     * 审核预约单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno');
        $detaildata = $request->getPost('detaildata');
        $detaildata = json_decode($detaildata, true);

        $status = $request->getPost('status');
        $stockmarks = $request->getPost('stockmarks');
        
        $res = $this->m->audit($id,$detaildata,$status,$stockmarks);
        
        if($res['statusCode']==200) COMMON::result(200, '审核成功：'.$res['msg']);
        else     COMMON::result(300, '审核失败：'.$res['msg'] );
    }
    /**
     * 添加和修改预约单信息
     */
    public function editJsonAction(){

        $id = $this->request->getPost('sysno');
        $bookingtransstatus = $this->request->getPost('bookingtransstatus');
        $data = [
            'bookingtransno' =>  COMMON ::getCodeId('B'),
            'bookingtransdate' => $this->request->getPost('bookingtransdate'),
            'sale_customer_sysno' => $this->request->getPost('sale_customer_sysno'),
            'sale_customername' => $this->request->getPost('sale_customername'),
            'buy_customer_sysno' => $this->request->getPost('buy_customer_sysno'),
            'buy_customername' => $this->request->getPost('buy_customername'),
            'buystartdate' => $this->request->getPost('buystartdate'),
            'docresource' => $this->request->getPost('docresource'),//通知单编号暂时没有            
            'bookingtransstatus' => $this->request->getPost('bookingtransstatus'),
            'contract_sysno' => $this->request->getPost('contract_sysno'),
            'contractno' => $this->request->getPost('contractno'),
        ];

        if($data['sale_customer_sysno']==$data['buy_customer_sysno']){
            COMMON::result(300, '转让方与受让方不能相同');
            return;
        }

        $detail = $this->request->getPost('detaildata', "");
        $detail = json_decode($detail, true);
        if (count($detail) == 0) {
            COMMON::result(300, '预约单明细不能为空');
            return;
        }
        if(in_array($bookingtransstatus, [4,5,6])){
            COMMON::result(300, '请按照正规流程审核');
            return;
        }
        //附件        
        $attachment =  $this->request->getPost('attachment',array());

        //添加或编辑
        if ($id){            
           
            $text = $this->request->getPost('stockmarks');
            if(!trim($text)){
                if($bookingtransstatus==1) $text = '新建';
                if($bookingtransstatus==2) $text = '暂存';
                if($bookingtransstatus==3) $text = '已提交';
                if($bookingtransstatus==4) $text = '已审核';
                if($bookingtransstatus==5) $text = '已完成';
                if($bookingtransstatus==6) $text = '作废';
            }
            //print_r($data);exit;
            $res = $this->m->update($id,$data,$detail,$text,$attachment);
            if($res['statusCode']==200) COMMON::result(200, '修改成功：'.$res['msg']);
            else     COMMON::result(300, '修改失败：'.$res['msg'] );
        }else{
            $res = $this->m->add($data,$detail,'新建',$attachment);
                        
            if($res['statusCode']==200) COMMON::result(200, '添加成功：'.$res['msg']);
            else     COMMON::result(300, '添加失败：'.$res['msg'] );
        }
    }
    /**
     * 明细列表json
     */
    public function detailListJsonAction(){
        //查询主表
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        if($id){
            $where['bookingtrans_sysno'] = $id;
            $list = $this->m->getListDetials($where);
        }
    
        $list = $list?$list:array();
        //$a = json_encode($list);
        echo json_encode($list);
    }
    /**
     * 添加和编辑明细
     */
    public function editDetailAction(){
        $id = $this->request->getParam('id', '0');
        $prefix = $this->request->getParam('prefix', '');
        if($id){
            $where['bookingtrans_sysno'] = $id;
            $list = $this->m->getListDetials($where);       
            $params = $list?$list[0]:null;
        }
        
        $params['prefix'] = $prefix;        
        $this->getView()->make('booktrans.adddetail',$params);
    }
    /**
     * 删除数据
     */
    public function delJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $res = $this->m->updateTrans($id,['isdel' => 1]);
    
        if ($res) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }
    /**
     * 详细提交
     */
    public function detailsubmitAction(){
        $request = $this->getRequest();
        $stock_sysno = $request->getPost('obj_sysno',0);
    
        $s = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $s->getElementById([$stock_sysno]);
        $info = $list?$list[0]:null;
        
        $list = [
            'sysno' => $request->getPost('sysno',0),
            'stock_sysno'=> $stock_sysno,
            'stockno' => $info['stockno'],
            'instockdate' => $info['instockdate'],
            'goodsname' => $info['goodsname2'],
            'goods_quality_sysno'=> $info['goods_quality_sysno'],
            'goodsnature' => $info['goodsnature'],
            'unit' => '吨',
            'stockqty' => $info['stockqty'],
            'transqty' => $request->getPost('transqty'),
            'storagetank_sysno' => $info['goodsname'],
            'memo' => $request->getPost('memo'),
            'qualityname' => $info['qualityname'],
            'storagetankname' => $info['storagetankname'],
            'stockinno' => $info['stockinno'],
        ];
        echo json_encode($list);
    }
    /**
     * 库存列表
     */
    public function stockListJsonAction(){
        $cid = $this->request->getParam('cid','0');
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = [ 'page' => false, 'customer_sysno' => $cid,'iscurrent' => 1 ];
      // $stockdata =  $S->getList($search);
        $stockdata =  $S->getStockList($search);

        echo json_encode($stockdata['list']);
    }
    /**
     * 导出预约单
     */
    public function exportAction(){
        $request = $this->getRequest();
        $search = array (
            'bookingtransdate_start'=>$request->getPost('bookingtransdate_start',''),
            'bookingtransdate_end' => $request->getPost('bookingtransdate_end',''),
            'sale_customer_sysno' => $request->getPost('sale_customer_sysno',''),
            'buy_customer_sysno' => $request->getPost('buy_customer_sysno',''),
            'bookingtransstatus'=>$request->getPost('bookingtransstatus',''),
            'orders'  => $request->getPost('orders',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
        );
        $list = $this->m->getList($search,0);
        
        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=货权转移列表.xls");
        
        
        
        echo
        iconv("UTF-8","GBK//IGNORE", "id")."\t".
        iconv("UTF-8","GBK//IGNORE", "单据编号")."\t".
        iconv("UTF-8", "GBK//IGNORE","转让名称")."\t".
        iconv("UTF-8", "GBK//IGNORE", "受让方名称")."\t".
        iconv("UTF-8", "GBK//IGNORE", "预约日期")."\t".
        iconv("UTF-8", "GBK//IGNORE", "受让方起始日")."\t".
        iconv("UTF-8", "GBK//IGNORE", "单据状态")."\t".
        iconv("UTF-8", "GBK//IGNORE", "创建时间")."\t";
        
        
        foreach ($list['list'] as $item) {
            if($item['bookingtransstatus']==1) $status = '新建';
            else if($item['bookingtransstatus']==2) $status = '暂存';
            else if($item['bookingtransstatus']==3) $status = '已提交';
            else if($item['bookingtransstatus']==4) $status = '已审核';
            else if($item['bookingtransstatus']==5) $status = '已完成';
            else if($item['bookingtransstatus']==6) $status = '作废';
        
            echo "\n";
            echo $item['sysno'].
            "\t".'D' .$item['bookingtransno']                                    //单据编号
            ."\t".iconv("UTF-8", "GBK//IGNORE", $item['sale_customername'])    //转让名称
            ."\t".iconv("UTF-8", "GBK//IGNORE", $item['buy_customername'])    //受让方名称
            ."\t".$item['bookingtransdate']                                       //转移日期
            ."\t".$item['buystartdate']                                         //受让方起始日
            ."\t".iconv("UTF-8", "GBK//IGNORE", $status)         //单据状态
            ."\t".iconv("UTF-8", "GBK//IGNORE", $item['created_at'])   ;
                   
        }
    }
}