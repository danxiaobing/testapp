<?php
/**
 * 待入库车辆
 * Author: HR
 * Date: 2017/02/14
 * Time: 10:38
 */

class PendcarinController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $this->getView()->make('pendcarin.list', $params);
    }


    public function listJsonAction()
    {
        $request = $this->getRequest();

        $carid = $request->getPost('carid','');
        $customername = $request->getPost('customername','');
        $begin_time = $request->getPost('startDate','');
        $end_time = $request->getPost('endDate','');
  
        $search = array(
                'carid'=>$carid,
                'customername' => $customername,
                'begin_time'=>$begin_time,
                'end_time'=>$end_time,
                'pageCurrent' => COMMON:: P(),
                'pageSize' => COMMON:: PR(),

            );

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $data = $P->getList($search);

        // var_dump($customername);
        echo json_encode($data);

    }

    /*
        展示核单页面
     */
    public function editAction()
    {
        $params = array();
    	$request = $this->getRequest();
        $id = $request->getPost('id','0');
    	$carid = urldecode($request->getPost('carid','0'));
        $poundid = urldecode($request->getPost('poundid','0'));
    	$P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $infoview = $P->getinfoById($id,$carid,$poundid);
        #获取所有入库的罐号
        $storage_arr = [];
        foreach($infoview as $k => $v){
            if(!in_array($v['storagetank_sysno'], $storage_arr)) {
                $storage_arr[] = $v['storagetank_sysno'];
            }
        }
        foreach ($infoview as $key => $value) {
            if(floatval($value['beqty'])-floatval($value['tobeqty'])<0) {
                $info = $infoview[$key];
                break;
            }else
            {
                continue;
            }
        }
        if(empty($info)) {
            $info = $infoview[0];
        }
    	$params['list'] = $info;

        #根据车牌号 获取车主信息
        // $bookOutCarInstance = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $carDetail = $bookOutCarInstance -> getCarInfoByCarid($carid);
        // $params['carDetail'] = $carDetail;
        $params['id'] = 0;
        //$params['storagetankinfo'] = $P->getOutStoragetanks($info['goods_sysno']);
        $params['storagetankinfo'] = $P->getIntankMsg($id);
        $params['storagetank_arr'] = implode(",", $storage_arr);
        
        if($request->getParam('type',0)){
            $params['type'] = $request->getParam('type',0);
            $poundid = $request->getPost('poundid',0);
            $params['detail'] = $P->poundsDetail($poundid);
            $params['poundid'] = $poundid;
            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $uploaded1 = $A->getAttachByMAS('pendcarin','pendcarin_release',$poundid);
            if( is_array($uploaded1) && count($uploaded1)){
                $files1 = array();
                foreach ($uploaded1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['uploaded1']  =  join(',',$files1);
            }
        }else{
            $params['list']['memo'] = '';
        }

        $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $booking['sysno'] = $id;
        $booking['isqualitycheck'] = $info['isqualitycheck'];
        $pbqData = $Sout->getPBQ($booking, 3);
        if($pbqData){
            $params['qualitycheck'] = empty($pbqData['qualitycheck'][0]['qualitycheck_sysno'])? json_encode([]):json_encode($pbqData['qualitycheck']);
        }else{
            $params['qualitycheck'] = json_encode([]);
        }
        if(!$poundid){
            $user = Yaf_Registry::get(SSN_VAR);
            $params['detail']['create_username'] = $user['realname'];
        }
        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();
        
    	return $this->getView()->make('pendcarin.pendcarinedit',$params);
    }


    /**
    * 核单 生成磅码单
    */
    public function poundsinAction()
    {
        $request = $this->getRequest();
        $stockin_sysno = $request->getPost('stockin_sysno','');

        $input = array(
            'poundsinno'        => COMMON::getCodeId('A3'),
            'loadometer'        => $request->getPost('loadometer',''),
            'storagetankname'   => $request->getPost('storagetankname_pendcar',''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno_pendcar',''),
            'carid'             => $request->getPost('carid',''),
            'carname'           => $request->getPost('carname',''),
            'mobilephone'       => $request->getPost('mobilephone',''),
            'idcard'            => $request->getPost('idcard',''),
            'customername'      => $request->getPost('customername',''),
            'deliverycompany'   => $request->getPost('deliverycompany','--'),
            'unloadnumber'      => $request->getPost('unloadnumber',''),
            'goodsname'         => $request->getPost('goodsname',''),
            'stockin_sysno'     => $stockin_sysno,
            'stockinno'         => $request->getPost('stockinno',''),
            'create_username'   => $request->getPost('create_username',''),
            'memo'   => $request->getPost('memo',''),
            'takegoodsno'       => $request->getPost('takegoodsno',''),
            'stockindetail_sysno' => $request->getPost('in_detail_sysno',''),
            'isqualitycheck' => $request->getPost('isqualitycheck',''),
            'poundsinstatus'    => '2',
            'created_at'        => '=NOW()',
            'updated_at'        => '=NOW()',
            'cranename'         => $request->getPost('cranename',''), //鹤位号
            'isqueue'           => $request->getPost('isqueue', ''), //是否排对
            'goods_sysno'       => $request->getPost('goods_sysno', ''), //是否排对
            );

        //附件
        $attachment = $request->getPost('attachment',array());

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goodsnature = $P->getStockcarinDetailById($input['stockindetail_sysno'])['goodsnature'];

        //添加储罐和货物性质对应关系
        $tankId = $input['storagetank_sysno'];
        $goodsnature = $goodsnature;
        $res = $L->tankTonature($tankId,$goodsnature);
        if($res['code']==300){
            COMMON::result(300, $res['message']);
            return;
        }

        $result = $P->add($input,$attachment);

        // $P->update(array('ispound'=>'1'),$stockin_sysno);

        if ($result['code']==200) {
            COMMON::result(200, '操作成功。');
            exit;
        } else {
            COMMON::result(300,$result['message']);
            exit;
        }        
        
    }

    public function poundslistAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        return $this->getView()->make('pendcarin.poundscarinlist',$params);
    }

    /*
        磅码单列表页
     */
    public function poundslistJsonAction()
    {
        $request = $this->getRequest();

        $carid = $request->getPost('pounds_carid','');
        $begin_time = $request->getPost('startDate','');
        $end_time = $request->getPost('endDate','');
        $status = $request->getPost('pounds_status','');
        $customername = $request->getPost('customername','');
        $stockinno = $request->getPost('stockinno','');
        $poundsinno = $request->getPost('poundsinno','');
        $goodsname = $request->getPost('goodsname','');

        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'status' => $status,
            'stockinno' => $stockinno,
            'poundsinno' => $poundsinno,
            'goodsname' => $goodsname,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => $this->getRequest()->getPost('orders',''),
            );

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $result = $P->poundsList($search);

        echo json_encode($result);
    }


    /**
     * 获取磅码单详情
     */
    public function poundsdetailAction()
    {
        $id = $this->getRequest()->getParam('id','');

        $void = $this->getRequest()->getParam('void','');

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $params = $P->poundsDetail($id);

        $params['id'] = $id;

        $params['void'] = $void;
        $params['viewtype'] = $this->getRequest()->getParam('type','');
        $Company = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname  = $Company->getDefault();
        $params['companyname']  = $companyname['titlename'];

        //添加附件的显示
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $uploaded1 = $A->getAttachByMAS('pendcarin','pendcarin_release',$id);
        if( is_array($uploaded1) && count($uploaded1)){
            $files1 = array();
            foreach ($uploaded1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['uploaded1']  =  join(',',$files1);
        }
        $info= $P->getinfoById($params['stockin_sysno'],$params['carid']);
        $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $booking['sysno'] = $id;
        $booking['isqualitycheck'] = $info[0]['isqualitycheck'];
        $pbqData = $Sout->getPBQ($booking, 3);
        if($pbqData){
            $params['qualitycheck'] = empty($pbqData['qualitycheck'][0]['qualitycheck_sysno'])? json_encode([]):json_encode($pbqData['qualitycheck']);
        }else{
            $params['qualitycheck'] = json_encode([]);
        }
        $params['isqualitycheck'] = $info[0]['isqualitycheck'];

        return $this->getView()->make('pendcarin.poundsdetail',$params);
    }

    /**
     * 车入库磅码单作废
     */
    public function poundsVoidAction()
    {
        $request  = $this->getRequest();
        // var_dump($request->getPost('stockinno',''));exit();
        $id = $request->getPost('id',''); //磅码单sysno

        $stockin_sysno = $request->getPost('stockin_sysno','');

        $memo = $request->getPost('memo','');

        $num = $request->getPost('beqty','');

        $stockindetail_sysno = $request->getPost('stockindetail_sysno','');

        $storagetank_sysno = $request->getPost('storagetank_sysno','');

        $stockinno = $request->getPost('stockinno','');

        $poundsno = $request->getPost('poundsinno','');
        // var_dump($poundsno);exit;

        #获取信息end
        // var_dump($storagetank_sysno); exit();
        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $detail = $P->poundsDetail($id);
        $info = $S->getStockcarinById($stockin_sysno);
        $stockinstatus = $info['stockinstatus'];
        $customer_sysno = $info['customer_sysno'];
        if($stockinstatus>3){ //如果入库订单的状态不是入库中不允许作废
            COMMON::result(300,'该入库单已经完成入库不能作废！');
        }else{
            $pounds = array(
                'poundsinstatus' => 5,
                'abandonreason' => $memo,
                );
            $beqty = floor($num)/1000 ; //需要加上损耗
            $res = $P->poundsVoid($pounds,$beqty,$customer_sysno,$stockin_sysno,$id,$storagetank_sysno,$stockindetail_sysno, $detail['ullage']);
            // var_dump($res);exit;
            if($res['code']==200){
                #库存管理业务操作日志
                // $stockmarks =  '作废入库磅码单';
                $user = Yaf_Registry::get(SSN_VAR);
                $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                $data= array(
                    'doc_sysno'  =>  $id,
                    'doctype'  =>  7,
                    'opertype'  => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime'    => '=NOW()',
                    'operdesc'  =>  $memo,
                );
                $S->addDocLog($data);
                #库存管理业务操作日志end

                //储罐日志
                if($detail['poundsinstatus'] != 3){
                    $storagetank = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                    $data = array(
                        'storagetank_sysno' => $storagetank_sysno,
                        'doc_sysno' => $stockin_sysno,
                        'docno' => $stockinno,
                        'doctype' => 2,
                        'pounds_sysno' => $id,
                        'poundsno' => $poundsno,
                        'pounds_type' => 1,
                        'beqty' => '-'.$beqty,
                    );
                    $storagetank->addStoragetankLog($data);
                }
                COMMON::result(200,'作废成功!');
            }else{
                COMMON::result(300,$res['message']);
            }
        }
    }

    public function poundsdelAction()
    {
        $id = $this->getRequest()->getParam('id','');
        // var_dump($id);
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $data = array(
            'isdel' => 1,
            );

        $res = $P->updatePounds($data, $id, '', 'del');

        if($res){
            COMMON::result(200,'删除成功!');
        }else{
            COMMON::result(300,'删除失败!');
        }
    }

    public function poundsbackAction(){
        $id = $this->getRequest()->getParam('id','');
        // var_dump($id);
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $data = array(
            'poundsinstatus' => 6,
        );

        $res = $P->updatePounds($data, $id, '', 'back');

        if($res){
            COMMON::result(200,'退单成功!');
        }else{
            COMMON::result(300,'退单失败!');
        }
    }

    /**
     * 查看车入库磅码单导出
     */
    public function ExcellistAction()
    {
        $request = $this->getRequest();


        $request = $this->getRequest();

        $carid = $request->getPost('pounds_carid','');
        $begin_time = $request->getPost('startDate','');
        $end_time = $request->getPost('endDate','');
        $status = $request->getPost('pounds_status','');
        $customername = $request->getPost('customername','');
        $stockinno = $request->getPost('stockinno','');
        $poundsinno = $request->getPost('poundsinno','');

        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'status' => $status,
            'stockinno' => $stockinno,
            'poundsinno' => $poundsinno,
            'page' => false,
            'orders' => $this->getRequest()->getPost('orders',''),
            );

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $result = $P->poundsList($search);
        // var_dump($result);exit();
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("车入库磅码单列表")
            ->setSubject("列表")
            ->setDescription("车入库磅码单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '单据编号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '发货公司'),
            array('D1:D1', 'D1', '0094CE58', '地磅编号'),
            array('E1:E1', 'E1', '0094CE58', '车牌号'),
            array('F1:F1', 'F1', '0094CE58', '储罐号'),
            array('G1:G1', 'G1', '0094CE58', '品名'),
            array('H1:H1', 'H1', '003376B3', '计量单位'),
            array('I1:I1', 'I1', '003376B3', '预卸数量(kg)'),
            array('J1:J1', 'J1', '0094CE58', '实际重量(kg)'),
            array('K1:K1', 'K1', '003376B3', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('车入库磅码单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K');

        $poundsinstatus = array();
        $poundsinstatus['1'] = '新建';
        $poundsinstatus['2'] = '核单完成';
        $poundsinstatus['3'] = '重车过磅';
        $poundsinstatus['4'] = '空车过磅';
        $poundsinstatus['5'] = '作废';

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['poundsinno'];
                        break;
                    case 1:
                        $value = $item['customername'];
                        break;
                    case 2:
                        $value = $item['deliverycompany'];
                        break;
                    case 3:
                        $value = $item['loadometer'];
                        break;
                    case 4:
                        $value = $item['carid'];
                        break;
                    case 5:
                        $value = $item['storagetankname'];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = 'kg';
                        break;
                    case 8:
                        $value = $item['unloadnumber'];
                        break;
                    case 9:
                        $value = $item['beqty'];
                        break;
                    case 10:
                        $value = $poundsinstatus[$item['poundsinstatus']];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="车入库磅码单列表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');        
    }

    /*
        获取储罐当前可存放吨数
     */
    public function AjaxgetCanstoreAction()
    {
        $request = $this->getRequest();
        $storagetank_sysno = $request->getPost('storagetank_sysno','');

        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $actualcapacity = $P->getStoragetankInfo($storagetank_sysno)['actualcapacity'];
        $tank_stockqty = $P->getStoragetankInfo($storagetank_sysno)['tank_stockqty'];
        $canstore = $actualcapacity-$tank_stockqty;
        echo $canstore;
    }

    /**
     * 入库磅码单编辑
     * @return [type] [description]
     */
    public function poundsdetaileditAction()
    {
        $request = $this->getRequest();

        $poundid = $request->getPost('poundid',0);
        $input = array(
            'loadometer'        => $request->getPost('loadometer',''),
            'storagetankname'   => $request->getPost('storagetankname_pendcar',''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno_pendcar',''),
            'deliverycompany'   => $request->getPost('deliverycompany','--'),
            'unloadnumber'      => $request->getPost('unloadnumber',''),
            'stockindetail_sysno' => $request->getPost('in_detail_sysno',''),
            'updated_at'        => '=NOW()',
            'cranename'         => $request->getPost('cranename',''), //鹤位号
            'create_username'         => $request->getPost('create_username',''),
            'memo'         => $request->getPost('memo',''), //备注
            'carid'         => $request->getPost('carid',''), //车牌号
            'carname'         => $request->getPost('carname',''), //司机名字
            'mobilephone'         => $request->getPost('mobilephone',''), //电话
            'idcard'         => $request->getPost('idcard',''), //身份证号
            'stockin_sysno'         => $request->getPost('stockin_sysno',''),
            'stockinno'         => $request->getPost('stockinno',''),
            'poundsinno'         => $request->getPost('poundsinno',''),
            );
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goodsnature = $P->getStockcarinDetailById($input['stockindetail_sysno'])['goodsnature'];

        //添加储罐和货物性质对应关系
        $tankId = $input['storagetank_sysno'];
        $goodsnature = $goodsnature;
        $res = $L->tankTonature($tankId,$goodsnature);
        if($res['code']==300){
            COMMON::result(300, $res['message']);
            return;
        }
        //附件
        $attachment = $request->getPost('attachment',array());

        $result = $P->updatePounds($input, $poundid, $attachment, 'update');

        if ($result) {
            COMMON::result(200, '操作成功。');
            exit;
        } else {
            COMMON::result(300,'修改失败!');
            exit;
        }  
    }
}