<?php
/**
 * 清库管理.
 * User: ty
 * Date: 2016/11/23 0023
 * Time: 16:43
 */
class ClearstockController extends Yaf_Controller_Abstract
{
    /**
     *新增编辑页面
     * @return string
     */
    public function AddAction(){

        $params = array();

        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        if(!$id){
            $action = '/clearstock/newJson';
        }
        else{
            $C = new ClearstockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $action = '/clearstock/editJson';
            $params = $C->getClearstockById($id);
            $printdata= $C->getNewPrint($id);
          //  print_r($printdata);die;
            $type = $request->getParam('type','');
            if($type=='audit'){
              $action = '/clearstock/auditJson';
            }
            if($type=='edit'&& ($params['stockclearstatus'] !=2 && $params['stockclearstatus']!=6)){
                COMMON::result('300','请选择暂存的数据');
                return false;
            }

            //获取附件
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('clearstock','clear-edit',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
        //  print_r($printdata);die;
            if(!empty($printdata)){
                $worktool=array(
                    '1'=>'船',
                    '2'=>'车',
                    '3'=>'货权转移',
                    '4'=>'管',
                );
             //   $time=date('d',strtotime($printdata['outdate'])-strtotime($printdata['stockindate']));
                if($printdata['outdate']){
                    $time = $printdata['stockindate'].'/'.$printdata['outdate'];
                }else{
                    $time = $printdata['stockindate'].'/'. $printdata['stockindate'];
                }
                if($printdata['instockqty'] && $printdata['outstockqty'] && $printdata['instockqty'] != 0){
                    $ganlv=($printdata['instockqty']-$printdata['outstockqty'])/$printdata['instockqty']*1000;
                }else{
                    $ganlv='0';
                }
                $params['printdata'] =array(
                    // 入库单号
                    'stockinno'=>$printdata['stockinno'],
                    // 清库单号
                    'stockclearno'=>$printdata['stockclearno'],
                    //品名
                    'goodsname'=>$printdata['goodsname'],
                    //用户名
                    'customername'=>$printdata['customername'],
                    //商检量
                    'tobeqtynum'=>$printdata['tobeqtynum'],
                    //入库数量
                    'instockqty'=>$printdata['instockqty'],
                    // 实发量
                    'beqtynum'=>$printdata['beqtynum'],
                    // 实发量2
                    'outstockqty'=>$printdata['outstockqty'],
                    //提单量
                    'takegoodsnum'=>$printdata['takegoodsnum'],
                    'worktool'=>$worktool[$printdata['stockintype']],
                    //计提时间
                    'time'=>$time,
                    //损溢率
                    'ganlv'=>$ganlv,
                    'sunliang' => $printdata['okqty'],
                    'intime' => $printdata['stockindate'],
                    //商品规格
                    'goodsqualityname'=>$printdata['goodsqualityname'],
                );
            }
            //打印用
            $params['print'] = $request->getParam('print','');
        }
        $params['id'] = $id;
        $params['action'] = $action;
        $params['stockclearstatusname'] = array(
            '2'=>'暂存',
            '3'=>'待审核',
            '4'=>'已审核',
            '5'=>'已完成',
            '6'=>'退回',
            '7'=>'作废',
        );

        //客户
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false
        );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];
//print_r($params['printdata']);die;
        //客服
        $search =array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $E = new EmployeeModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] =  $list['list'];

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname  = $Company->getDefault();
        $params['companyname']  = $companyname['companyname'];
    //  print_r($params);die;
        $this->getView()->make('clearstock.edit',$params);

    }
    /*
     * 查看
     * */
public function lookclearstockAction(){
    $params = array();
    $request = $this->getRequest();
    $id = $request->getParam('id','0');
    if(!$id){
        return false;
    }
    else{
        $C = new ClearstockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $action = '/clearstock/editJson';
        $params = $C->getClearstockById($id);
        $printdata= $C->getNewPrint($id);
        $type = $request->getParam('type','');
        if($type=='edit'&& ($params['stockclearstatus'] !=2 && $params['stockclearstatus']!=6)){
            COMMON::result('300','请选择暂存的数据');
            return false;
        }

        //获取附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach1 = $A->getAttachByMAS('clearstock','clear-edit',$id);
        if( is_array($attach1) && count($attach1)){
            $files1 = array();
            foreach ($attach1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['attach1']  =  join(',',$files1);
        }
        //  print_r($printdata);die;
        if(!empty($printdata)){
            $worktool=array(
                '1'=>'船',
                '2'=>'车',
                '3'=>'货权转移',
                '4'=>'管',
            );
            //   $time=date('d',strtotime($printdata['outdate'])-strtotime($printdata['stockindate']));
            $time = $printdata['stockindate'].'/'.$printdata['outdate'];
            if($printdata['instockqty'] && $printdata['outstockqty'] && $printdata['instockqty'] != 0){
                $ganlv=($printdata['instockqty']-$printdata['outstockqty'])/$printdata['instockqty']*1000;
            }else{
                $ganlv='0';
            }
            $params['printdata'] =array(
                // 入库单号
                'stockinno'=>$printdata['stockinno'],
                //品名
                'goodsname'=>$printdata['goodsname'],
                //用户名
                'customername'=>$printdata['customername'],
                //商检量
                'tobeqtynum'=>$printdata['tobeqtynum'],
                // 实发量
                'beqtynum'=>$printdata['beqtynum'],
                //运输工具
                'worktool'=>$worktool[$printdata['stockintype']],
                //计提时间
                'time'=>$time,
                //损溢率
                'ganlv'=>$ganlv.'%',
                'sunliang' => $printdata['tobeqtynum']-$printdata['tobeqtynum'],
                'intime' => $printdata['stockindate'],
                //商品规格
                'goodsqualityname'=>$printdata['goodsqualityname'],
            );
        }
        //打印用
        $params['print'] = $request->getParam('print','');
    }
    $params['id'] = $id;
    $params['action'] = $action;
    $params['stockclearstatusname'] = array(
        '2'=>'暂存',
        '3'=>'待审核',
        '4'=>'已审核',
        '5'=>'已完成',
        '6'=>'退回',
        '7'=>'作废',
    );

    //客户
    $search = array(
        'bar_status' => '1',
        'bar_isdel' => '0',
        'page' => false
    );
    $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
    $list = $C->searchCustomer($search);
    $params['customerlist'] =  $list['list'];

    //客服
    $search =array(
        'bar_status' => '1',
        'bar_isdel' => '0',
        'page' => false,
    );
    $E = new EmployeeModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
    $list = $E->searchEmployee($search);
    $params['employeelist'] =  $list['list'];

    $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    $companyname  = $Company->getDefault();
    $params['companyname']  = $companyname['companyname'];
    $this->getView()->make('clearstock.lookcleardata',$params);
}



    /**
     *编辑数据显示
     * @return string
     */
    public function AdddetailAction(){

        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        $params = array();
        if($id){
            $Clearstock = new ClearstockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $params = $Clearstock->getNewClearstockByIds($id);
        }
//       print_r($params);die;
        echo json_encode($params);
    }

    /**
     *清库详情新增页
     * @return string
     */
    public function adddataAction(){
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('customer_sysno','0');
        $detailtype = $request->getParam('detailtype','');
        $params= array();
        $params['customer_sysno']=$customer_sysno;
        $params['detailtype']=$detailtype;
        $this->getView()->make('clearstock.adddetail',$params);
    }

    /**
     *清库详情数据选择
     * @return string
     */
    public function addselectAction(){
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('customer_sysno','');
        $S = new ClearstockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
           // 'page'=>false,
            'customer_sysno'=>$customer_sysno,
            'stockinno'=>trim($request->getPost('stockinno','')),
            'goodsname'=>trim($request->getPost('goodsname','')),
            'shipname'=>trim($request->getPost('shipname','')),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
      //  $detail = $S->getstockinno($search);
       // $detail = $S->getNewstockinno($search);
        $detail =$S->getstockList($search);
       // echo '<pre>'; print_r($detail);die;
        echo json_encode($detail);
    }

    /**
     *清库详情页面数据
     * @return string
     */
    public function submitdetailAction(){
        $request = $this->getRequest();
        $stock_sysno=$request->getPost('stock_sysno','0');//库存单主键
        $memo = $request->getPost('memo','');
        $S = new ClearstockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
            'page'=>false,
            'stock_sysno'=>$stock_sysno,
          // 'customer_sysno'=>$request->getPost('customer_sysno',''),
        );
       // $stockdetail = $S->getNewDetail($search);
      //  $stockdetail = $S->getNewstockinno($search);
          $stockdetail = $S->getstockList($search);
       //echo '<pre>';  print_r($stockdetail);die;
        $stockdetail['list'][0]['memo'] = $memo;
        echo json_encode($stockdetail['list']);
    }

    /**
     *新增清库单详情新增数据库保存操作
     * @return string
     */
    public function newJsonAction(){
        $request = $this->getRequest();
        $clearstockdetail = $request->getPost('clearstockdetail',"");
        $clearstockdetail = json_decode($clearstockdetail,true);
//        var_dump($clearstockdetail);die();
        // echo '<pre>';  print_r($clearstockdetail);die();
        if(count($clearstockdetail)==0) {
            COMMON::result(300,'清库单明细不能为空');
            return;
        }
        $S = new ClearstockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $step = $request->getPost('stockclearstatus','');
        $stockclearstatus = array(
            '1'=>'2',
            '2'=>'2',
            '3'=>'3',
            '4'=>'4'
        );

        $input = array(
            'stockclearno' => COMMON::getCodeId('Q'),
            'stockcleardate' => $request->getPost('stockcleardate',""),
            'customer_sysno' => $request->getPost('customer_sysno',""),
            'cs_employee_sysno'=>$request->getPost('cs_employee_sysno',""),
            'cs_employeename'=>$request->getPost('cs_employeename',""),
            'stockclearstatus' => isset($stockclearstatus[$step])?$stockclearstatus[$step]:'2',
            'status' => $request->getPost('status','1'),
            'isdel' => $request->getPost('isdel','0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        //附件
        $attachment = $request->getPost('attachment',array());
 //print_r($attachment);die;

        $res = $S->addClearstock($input,$clearstockdetail,$request->getPost('operdesc',""),$step,$attachment);
        if($res['statusCode']==200) COMMON::result(200, '提交成功：'.$res['msg']);
        else   COMMON::result(300, '提交失败：'.$res['msg'] );
    }


    /**
     *   清库详单编辑保存
     * @return string
     */
    public function editJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $clearstockdetail = $request->getPost('clearstockdetail',"");
        $clearstockformdata = json_decode($clearstockdetail,true);
//        var_dump($clearstockformdata);die();
        $C = new ClearstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        if($request->getPost('stockclearstatus','') == 6 && $request->getPost('operdesc')== null ){
            COMMON::result(300, '审核备注不能为空');
            return false;
        }
        $step = $request->getPost('stockclearstatus','');
        $stockclearstatus = array(
            '1'=>'2',
            '2'=>'2',
            '3'=>'3',
            '4'=>'4'
        );
        $input = array(
            'stockcleardate' => $request->getPost('stockcleardate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'stockclearstatus'=>isset($stockclearstatus[$step])?$stockclearstatus[$step]:'2',
            'updated_at' => '=NOW()'
        );
        //附件
        $attachment = $request->getPost('attachment',array());
        $res = $C->updateClearstock($id,$input,$clearstockformdata,$request->getPost('operdesc', ''),$step,$request->getPost('clearstatus',''),$request->getPost('abandonreason',''),$attachment);
    //    print_r($res);
         if($res['statusCode']==200) COMMON::result(200, '提交成功：'.$res['msg']);
         else   COMMON::result(300, '提交失败：'.$res['msg'] );

    }

    /**
     *审核
     * @return string
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $clearstockdetail = $request->getPost('clearstockdetail',"");
        $clearstockformdata = json_decode($clearstockdetail,true);

        $C = new ClearstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        if($request->getPost('stockclearstatus','') == 6 && $request->getPost('operdesc')== null ){
            COMMON::result(300, '审核备注不能为空');
            return false;
        }
        $step = $request->getPost('stockclearstatus','');
        $stockclearstatus = array(
            '1'=>'2',
            '2'=>'2',
            '3'=>'3',
            '4'=>'4'
        );
        $input = array(
            'stockclearno' => $request->getPost('stockclearno',''),
            'stockcleardate' => $request->getPost('stockcleardate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'abandonreason' => $request->getPost('abandonreason', ''),
            'stockclearstatus'=>isset($stockclearstatus[$step])?$stockclearstatus[$step]:'2',
            'updated_at' => '=NOW()'
        );

        $res = $C->updateClearstock($id,$input,$clearstockformdata,$request->getPost('operdesc', ''),$step,$request->getPost('clearstatus',''));
        if($res['statusCode']==200) COMMON::result(200, '审核成功：'.$res['msg']);
         else   COMMON::result(300, '审核失败：'.$res['msg']);
    }

    /**
     *清库列表展示
     * @return string
     */
    public function listAction(){
        $params=array();

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false
        );
        //客户
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];

        $this->getView()->make('clearstock.list', $params);
    }

    /**
     *清库列表数据
     * @return string
     */
    public function listJsonAction(){
        $request = $this->getRequest();
        $search = array(
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'cleardate_start'=>$request->getPost('cleardate_start',''),
            'cleardate_end'=>$request->getPost('cleardate_end',''),
            'customername'=>$request->getPost('customername',''),
            'stockclearstatus' => $request->getPost('stockclearstatus','')
        );

        $C = new ClearstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $params = $C->getNewList($search);
     //  print_r($params);die;
        echo json_encode($params);
    }


    /**
     *删除
     * @return string
     */
    public function delJsonAction(){

        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $S = new ClearstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $data = $S->getClearstockById($id);
        if($data['stockclearstatus']>2 && $data['stockclearstatus']!=6){
            COMMON::result(300,'不允许删除');
            return false;
        }

        $input = array(
            'isdel' => 1
        );
        if($S->delClearstock($id,$input)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'删除失败');
        }

    }
    /*
     * 查看附件
     * */
    public function lookImglistAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $module = 'clearstock';
        $action = 'clear-edit';
        $imageData = $A->getAttachMAS($id,$module,$action);
        $params['imageData'] = $imageData;
        // print_r($imageData);die();
        $this->getView()->make('clearstock.clearstockimglist',$params);
    }

    public function stockListJsonAction(){
        $request = $this->getRequest();
        $cid = $request->getParam('cid','0');
//        var_dump($cid);die();
        $m = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = [ 'page' => false, 'customer_sysno' => $cid,'iscurrent' => 1 ];
        $stockdata =  $m->getstockList($search);
        //   $stockdata =  $S-> getStockList($search);
        //  echo "<pre>";  print_r($stockdata);die;
        echo json_encode($stockdata['list']);
    }
    /**
     * 详细提交
     */
    public function detailsubmitAction(){
        $request = $this->getRequest();
        $stock_sysno = $request->getPost('sysno',0);
        $transqty = $request->getPost('transqty');
        $goods_sysno = $request->getPost('obj_goods_sysno');
        $unitname = $request->getPost('obj_unitname');
        $goodsname = $request->getPost('obj_goodsname');
        $storagetank_sysno = $request->getPost('storagetank_sysno');
        $storagetankname = $request->getPost('storagetankname');
        $shipname = $request->getPost('shipname');
//        var_dump($stock_sysno);die();
        $m = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        //  $list = $s->getElementById([$stock_sysno]);
        $list = $m->getStockdetailById([$stock_sysno]);
//        var_dump($list);die();
        $info = $list?$list[0]:null;
        // print_r($info);die;
        $list = [
            'sysno' => $request->getPost('sysno',0),
            'out_stock_sysno'=> $stock_sysno,
            'stockno' => $info['stockno'],
            'stockin_no'=>$info['stockinno'],
            'instockdate' => $info['instockdate'],
            // 'goodsname' => $info['goodsname2'],
            'goodsname'=>$goodsname,
            'goods_quality_sysno'=> $info['goods_quality_sysno'],
            'goodsnature' => $info['goodsnature'],
            'unit' => '吨',
            'instockqty'=>$info['instockqty'],
            'stockqty' => $info['stockqty']-$info['clockqty'],
            'transqty' => $transqty,
            'goods_sysno' => $goods_sysno,
            'shipname' => $shipname,
            'storagetank_sysno' => $info['storagetank_sysno'],
            'memo' => $request->getPost('memo'),
            'qualityname' => $info['qualityname'],
            'storagetankname' => $info['storagetankname'],
            'unitname'=>$unitname,
            'firstfrom_sysno'=>$info['firstfrom_sysno'],
            'contract_sysno'=>$info['contract_sysno'],
            'storagetank_sysno'=>$storagetank_sysno,
            'storagetankname'=>$storagetankname,
            'release_num'=>$info['release_num'],
            'unrelease_num'=>$info['unrelease_num'],
            'tank_stockqty'=>$info['tank_stockqty'],
        ];
//        var_dump($list);die();
        $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = ['type'=>7,'data'=>['sysno'=>$stock_sysno]];
        $ret = $stock->pubstockoperation($params);
        //  print_r($request->getPost());
        //    print_r($list);die;
        //判断库存
        if($ret >= $transqty)
            echo json_encode($list);
        else
            COMMON::result(300, '添加失败，库存不足');
    }
    /**
     *导出
     * @return string
     */
    public function ExcelAction(){
        $request = $this->getRequest();
        $search = array(
            'page'=>false,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'created_at'=>$request->getPost('created_at',''),
            'updated_at'=>$request->getPost('updated_at',''),
            'customername'=>$request->getPost('customername',''),
            'stockclearstatus' => $request->getPost('stockclearstatus','')
        );

        $C = new ClearstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $clearstock = $C->getNewList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("清库列表")
            ->setSubject("列表")
            ->setDescription("清库列表");

        $mainTitle = array(
            array('A1:A1', 'A1','005E9CD3', '清库单号'),
            array('B1:B1', 'B1', '005E9CD3', '盈亏率‰'),
            array('C1:C1', 'C1', '0094CE58', '损耗计提期间'),
            array('D1:D1', 'D1', '0094CE58', '清库日期'),
            array('E1:E1', 'E1', '0094CE58', '品名'),
            array('F1:F1', 'F1', '0094CE58', '客户'),
            array('G1:G1', 'G1', '0094CE58', '计量单位'),
            array('H1:H1', 'H1', '003376B3', '商检量'),
            array('I1:I1', 'I1', '003376B3', '发货量'),
            array('J1:J1', 'J1', '0094CE58', '盈亏量'),
            array('k1:k1', 'k1', '0094CE58', '工具'),
            array( 'L1:L1', 'L1','0094CE58', '单据状态'),
            array( 'M1:M1', 'M1','0094CE58', '进货日期'),
            array( 'N1:N1', 'N1','0094CE58', '入库单号'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('清库列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','k','L','M','N');

        $doctype = array();
        $doctype['0'] = '新建';
        $doctype['1'] = '新建';
        $doctype['2'] = '暂存';
        $doctype['3'] = '待审核';
        $doctype['4']= '已审核';
        $doctype['5']= '已完成';
        $doctype['6']= '退回';
        $doctype['7']= '作废';

        foreach ($clearstock['list'] as $item) {

            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockclearno'];
                        break;
                    case 1:
                        $value = $item['loss'];
                        break;
                    case 2:
                        $value = $item['dateperiod'];
                        break;
                    case 3:
                        $value =  $value = $item['stockcleardate'];
                        break;
                    case 4:
                        $value = $item['goodsname'];
                        break;
                    case 5:
                        $value = $item['customername'];
                        break;
                    case 6:
                        $value = $item['unitname'];
                        break;
                    case 7:
                        $value = $item['instockqty'];
                        break;
                    case 8:
                        $value = $item['outstockqty'];
                        break;
                    case 9:
                        $value = $item['okqty'];
                        break;
                    case 10:
                        if($item['shipname']==''){
                            $value='车';
                        }else(
                        $value=$item['shipname']
                        );
                        break;
                    case 11:
                        $value = $doctype[$item['stockclearstatus']];
                        break;
                    case 12:
                        $value = $item['stockindate'];
                        break;
                    case 13:
                        $value = $item['stockinno'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="清库列表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}