<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/07/05 0017
 * Time: 10:35
 */
class PipelineorderController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public  $businesstype = array(
                1=>'船入库预约',
                2=>'船入库订单',
                3=>'车入库预约',
                4=>'车入库订单',
                5=>'管入库预约',
                6=>'管入库订单',
                7=>'船出库预约',
                8=>'船出库订单',
                9=>'车出库预约',
                10=>'车出库订单',
                11=>'管出库预约',
                12=>'管出库订单',
                13=>'靠泊装卸入预约',
                14=>'靠泊装卸出预约',
                15=>'靠泊装货订单',
                16=>'靠泊卸货订单',
                17=>'倒罐预约单',
                18=>'倒罐订单',
                );
    public $P = null;
    public function init()
    {

        # parent::init();
        $this->P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    }

    /**
     * 管线管理
     * @author jp
     */

    public function listAction()
    {
        //业务类型
        $params = array();
        $params['list'] = $this->businesstype;
      //  print_r($params);die;
        $this->getView()->make('pipelineorder.list', $params);
    }

    /**
     * 片区管理列表JSON
     * @author jp
     */

    public function ListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'startTime' => $request->getPost('startTime',''),
            'endTime' => $request->getPost('endTime',''),
            'businesstype'   => $request->getPost('businesstype',''),
            'orderstatus'   => $request->getPost('orderstatus',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
//print_r($search);
        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $P->searchPipeline($search);
        $bookno = array(1,3,5,7,9,11,13,14,17);    //预约状态
        $stockno = array(2,4,6,8,10,12,15,16,18);  //入库状态

        $stockberthin = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stockberthout = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_stockintype' => 4,
            'bar_stockoutype' => 4,
            'page' => false
        );
        $stockberthindata = $stockberthin -> searchStockberthin($search);
        $stockberthindata = $stockberthindata['list'];
        $stockberthoutdata = $stockberthout ->searchStockberthout($search);
        $stockberthoutdata = $stockberthoutdata['list'];
        foreach($list['list'] as $key=>$value){
            //判断业务单号
            if(in_array($value['businesstype'],$bookno)){
               $list['list'][$key]['orderno'] = $value['bookingno'];
            }
            if(in_array($value['businesstype'],$stockno)){
                $list['list'][$key]['orderno'] = $value['stockno'];
            }
            $input=array(
                'sysno'=>$value['sysno'],
                'businesstype'=>$value['businesstype'],
                'booking_sysno'=>$value['booking_sysno'],
                'stock_sysno'=>$value['stock_sysno'],
            );
            if(in_array($value['businesstype'],[1,2])){//船进
                $shipname = $P->getshipnameIn($input);
                $list['list'][$key][shipname] = $shipname['shipname'];

            }elseif(in_array($value['businesstype'],[7,8])){//船出
                $shipname = $P->getshipnameOut($input);
                $list['list'][$key][shipname] = $shipname['shipname'];

            }elseif(in_array($value['businesstype'],[3,4,9,10])){//车进出
                $list['list'][$key][shipname] = '槽车';
            }elseif(in_array($value['businesstype'],[5,6,11,12])){//管进出
                $list['list'][$key][shipname] = '管输';

            }elseif(in_array($value['businesstype'],[15])){//靠泊入
                foreach($stockberthindata as $item){
                    if($value['stock_sysno'] == $item['sysno']){
                        $list['list'][$key][shipname] = $item['inshipname'];
                    }
                }
            }elseif(in_array($value['businesstype'],[16])){//靠泊入
                foreach($stockberthoutdata as $item){
                    if($value['stock_sysno'] == $item['sysno']){
                        $list['list'][$key][shipname] = $item['inshipname'];
                    }
                }
            }elseif(in_array($value['businesstype'],[17,18])){//倒灌
                $list['list'][$key][shipname] = '--';
            }
        }
       // print_r($list);die;
        echo json_encode($list);
    }


    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            COMMON::result(300,'数据异常');
            return;

        }
            $mode = $request->getParam('mode');
            $action = "/pipelineorder/EditJson/";
            //获取主表信息
            $params['list'] = $S->getPipelineMian($id);
            //业务类型
            $params['businesstype'] = $this->businesstype;
            $bookno = array(1,3,5,7,9,11,13,14,17);    //预约状态
            $stockno = array(2,4,6,8,10,12,15,16,18);  //入库状态
               //获取业务单号
              if(!$params['list']['businesstype']){
                  COMMON::result(300,'业务单号类型不能为空');
                  return;
              }
              if(in_array($params['list']['businesstype'],$bookno)){
                  $params['list']['orderno'] =$params['list']['bookingno'];
              }
             if(in_array($params['list']['businesstype'],$stockno)){
                $params['list']['orderno'] =$params['list']['stockno'];
             }

           //获取申请人和操作人
          //业务员列表
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

           $params['id'] = $id;
           $params['action'] = $action;
           $params['mode'] = $mode;
        // print_r($params);die;
        $this->getView()->make('pipelineorder.edit', $params);
    }


    public function EditJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        if(!$id){
            COMMON::result(300,'数据异常');
            return;
        }

        //明细表信息
      //  print_r($request->getRequest());die;
        $details = $request->getPost('pipedetaildata','');
        $details = json_decode($details,true);
        if (count($details) == 0) {
            COMMON::result(300, '管线单明细不能为空');
            return;
        }

        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        //主表信息
        $input = array(
            'pipelineorderno'=>$request->getPost('pipelineorderno',''), //管号no
            'orderno'=>$request->getPost('orderno',''), //管号no
            'businesstype'=>$request->getPost('businesstype',''), //业务单据类型
            'bookingdate' => $request->getPost('bookingdate', ''),//预约时间
            'orderstatus' => $request->getPost('orderstatus', ''),              //单据状态
            'applydate' => $request->getPost('applydate',''),                 //申请時間
            'apply_user_sysno' => $request->getPost('apply_user_sysno', ''),      //申请人id
            'created_user_sysno' => $request->getPost('created_user_sysno', ''),      //创建人id
            'apply_employeename' => $request->getPost('apply_employeename', ''),              //申请人
            'created_employeename' => $request->getPost('created_employeename', ''),              //创建人
            'step'=>$request->getpost('pipestatus',''),
            'updated_at' => '=NOW()'
        );

        $input['applydate'] = $input['applydate']?$input['applydate']:date('Y-m-d');

        $res = $P->addpipelineorder($details,$input,$id);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $P->searchPipeline($params);
            COMMON::result(200, '新增成功',$row);
        } else {
            COMMON::result(300, '新增失败：'.$res['msg'] );
        };
    }

    /*
     * 明细表json
     *
     *  */
    public function detailJsonAction(){

        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($id){
            $where['sysno'] = $id;
            $list = $P->getListDetials($where);
        }

        $list = $list?$list:array();
        //  print_r($list);die;
        echo json_encode($list);

    }

/*
 * 添加页面
 * */
    public function AddeditAction(){
        $request = $this->getRequest();
        $params['list'] = $request->getPost('selectedDatasArray',array());
      //  $params['goodsList'] = $request->getPost('goodsList', []);
        $goodslistData = json_decode($request->getPost('goodsList', []), true);
        $mide = array();
        foreach($goodslistData as $key=>$value){
            //去重
            if(!in_array($value['goods_sysno'],$mide)){
                $params['goodsList'][$key] =  $value;
                $mide[] = $value['goods_sysno'];
            }

        }

        $params['type'] = $request->getParam('type','');
        $P = new PipelineorderModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $T = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params['goods_sysno'] = $P->getgoodsInfo();
        $search = array(
            'pipelinetype'=>1
        );
        $params['wharf_pipeline'] = $P->getBasePipe($search);
        $search = array(
            'pipelinetype'=>2
        );
        $params['area_pipeline'] = $P->getBasePipe($search);
        if($params['list']['storagetank_sysno']){
           $params['list']['tank_goods_sysno'] = $T->storagetankgoodsbyid($params['list']['storagetank_sysno']);

        }

       $params['businesstype'] = $request->getPost('businesstype','');
    //  print_r($params);die;
        $this->getView()->make('pipelineorder.addedit',$params);

    }


    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1
        );

        if ($P->updateBerth($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }
    /*
       * 获取移入储罐罐容信息
       * */
    public function getStocklistJsonAction()
    {
        $P = new PipelineorderModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $P->getStockRetankInfo();

        echo json_encode($params);
    }

    /*
      * 获取所有商品
      * */
    public function getgoodslistJsonAction()
    {
        $P = new PipelineorderModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $P->getgoodsInfo();

        echo json_encode($params);
    }

    //获取基础资料--船舶管理数据
    public function ShipJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'page' => false,
            'iscurrent' => 1,
            'bar_status'=>1,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $list = $S->searchShipList($search);

        echo json_encode($list);
    }

    /*
     * 获取预约单明细
     * $businesstype = array(
                1=>'船入库预约',
                2=>'船入库订单',
                3=>'车入库预约',
                4=>'车入库订单',
                5=>'管入库预约',
                6=>'管入库订单',
                7=>'船出库预约',
                8=>'船出库订单',
                9=>'车出库预约',
                10=>'车出库订单',
                11=>'管出库预约',
                12=>'管出库订单',
                13=>'靠泊装卸入预约',
                14=>'靠泊装卸出预约',
                15=>'靠泊装卸入订单',
                16=>'靠泊装卸出订单',
                );
     * */
        public function getbookJsonAction(){
            $request = $this->getRequest();
            $pipe_id = $request->getParam('id', '0');
            if($pipe_id){
            //获取管线入库单详情
            $info =  $this->P->getPipelineMian($pipe_id);
            $id = $info['booking_sysno'];
            $businesstype = $info['businesstype'];

             switch($businesstype){
                 case ($businesstype==1 || $businesstype==2):
                     $S = new BookshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                     $list = $S->getBookshipindetailById($id);
                     foreach ($list as $key => $value) {
                         $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                         $data = $quality->getQualityById($value['goods_quality_sysno']);
                         $list[$key]['qualityname'] = $data['qualityname']? $data['qualityname']:'';
                         $list[$key]['shipbookingdate'] = $value['bookingindate'];
                         $list[$key]['tobeqty'] =$value['bookinginqty'];
                     }
                  //   print_r($list);die;
                     echo json_encode($list);
                     break;
                 case ($businesstype==3 || $businesstype==4):
                     $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                     $list = $S->getBookcarindetailById($id);
                     if (!empty($list)) {
                         foreach ($list as $key => $value) {
                             $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                             $data = $quality->getQualityById($value['goods_quality_sysno']);
                             $list[$key]['qualityname'] = $data['qualityname']?$data['qualityname']:'';
                             $list[$key]['goods_sysno'] = $value['goods_sysno'];
                             $list[$key]['goodsname'] = $value['goodsname'];
                             $list[$key]['tobeqty'] = $value['bookinginqty'];
                             $list[$key]['shipbookingdate'] = $value['bookingindate'];
                         }
                     }
                     echo json_encode($list);
                     break;
                 case ($businesstype==5 || $businesstype==6):
                     $S = new BookshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                     $list = $S->getBookshipindetailById($id);

                     if ($id && !empty($list)) {
                         foreach ($list as $key => $value) {
                             $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                             $data = $quality->getQualityById($value['goods_quality_sysno']);
                             $list[$key]['qualityname'] = $data['qualityname']?$data['qualityname']:'';
                             $list[$key]['goodsname'] = $value['goodsname'];
                             $list[$key]['tobeqty'] = $value['bookinginqty'];
                             $list[$key]['shipbookingdate'] = $value['bookingindate'];
                         }
                     }
                     //print_r($list);die;
                     echo json_encode($list);
                     break;
                 case ($businesstype==7 || $businesstype==8):
                     $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                     $search = array(
                         'bookingout_sysno' =>	$id,
                         'page' => false
                     );
                     $detailData =  $B->getBookoutDetailList($search);

                     foreach ($detailData['list'] as $key => $value) {
                         $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                         $detailData['list'][$key]['unitname'] = '吨';
                         $detailData['list'][$key]['shipbookingdate'] = $value['shipokdate'];
                     }
                   //  print_r($detailData['list']);die;
                    echo  json_encode($detailData['list']) ;
                     break;
                 case ($businesstype==9 || $businesstype==10):
                     //明细数据
                     $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                     $search = array(
                         'bookingout_sysno' => $id,
                         'page' => false
                     );
                     $detailData =  $B->getBookoutDetailList($search);
                     foreach ($detailData['list'] as $key => $value) {
                         $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                         $detailData['list'][$key]['unitname'] = '吨';
                         $detailData['list'][$key]['shipbookingdate'] = $value['shipokdate'];
                     }
                     echo json_encode($detailData);
                     break;
                 case ($businesstype==11 || $businesstype==12):
                     $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                     $search = array(
                         'bookingout_sysno' =>	$id,
                         'page' => false
                     );
                     $detailData =  $B->getBookoutDetailList($search);
                     foreach ($detailData['list'] as $key => $value) {
                         $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                         $detailData['list'][$key]['unitname'] = '吨';
                         $detailData['list'][$key]['shipbookingdate'] = $value['shipokdate'];
                     }
                    // print_r($detailData);die;
                     echo json_encode($detailData['list']) ;
                     break;
                 case ($businesstype==13 || $businesstype==15):
                     $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                     $detailData = $B->getBookberthindetailById($id);

                     foreach ($detailData as $key => $value) {
                         $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                         $T = new RetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                         if($value['goods_quality_sysno']){
                             $data = $quality->getQualityById($value['goods_quality_sysno']);
                         }
                         if($value['storagetank_sysno']){
                             $detailData[$key]['storagetankname']  =$T->getinstoragetankById($value['storagetank_sysno']);
                         }

                         $detailData[$key]['qualityname'] = $data['qualityname']? $data['qualityname']:'';
                         $detailData[$key]['tobeqty'] = $value['bookinginqty'];
                         $detailData[$key]['unitname'] = '吨';
                         $detailData[$key]['shipbookingdate'] = $value['bookingindate'];

                     }
                    // print_r($detailData);die;
                     echo json_encode($detailData);
                     break;
                 case ($businesstype==14 || $businesstype==16):
                     $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                     $detailData = $B->getBookberthoutdetailById($id);

                     foreach ($detailData as $key => $value) {
                         $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                         $T = new RetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                         if($value['goods_quality_sysno']){
                             $data = $quality->getQualityById($value['goods_quality_sysno']);
                         }
                         if($value['storagetank_sysno']){
                             $detailData[$key]['storagetankname']  =$T->getinstoragetankById($value['storagetank_sysno']);
                         }
                         $detailData[$key]['qualityname'] = $data['qualityname']? $data['qualityname']:'';
                         $detailData[$key]['tobeqty'] = $value['bookingoutqty'];
                         $detailData[$key]['unitname'] = '吨';
                         $detailData[$key]['shipbookingdate'] = $value['shipokdate'];
                     }
                     echo json_encode($detailData);
                     break;
                 case ($businesstype==17 || $businesstype==18):
                     //明细数据
                     $R = new RetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

                     $detailData =  $R->getapplyretankdetailById($id);
                     foreach ($detailData as $key => $value) {
                         $detailData[$key]['tobeqty'] = $value['bookingretankqty'];
                         $detailData[$key]['unitname'] = '吨';
                         $detailData[$key]['shipbookingdate'] = $value['bookingretankdate'];
                         $detailData[$key]['stockretank_out_no'] = $value['stockretank_out_no'];
                         $detailData[$key]['stockretank_in_no'] = $value['stockretank_in_no'];
                     }
                     echo json_encode($detailData);

                     break;
             }
            }else{
                COMMON::result('300','数据异常');
            }

        }
    //导出靠泊卸货
    public function exportAction()
    {
        ob_end_clean();
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
//print_r($id);die;
        //靠泊卸货信息
        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $pipeInfo = $P->getPipelineMian($id);

        if ($pipeInfo) {
            return self::pipelineorderExport($pipeInfo);
        }else {
            COMMON::result(300, '数据异常');
        }
    }


    //靠泊卸货合同
    private static function pipelineorderExport($contractInfo)
    {
        extract($contractInfo);

        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/KBstockberthout.docx');

        $templateProcessor->setValue('customername', $contractInfo['customername']);

        //获取明细
        $B = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stockout_id = $contractInfo['sysno'];
        $stockoutdetail  = $B->getStockberthoutdetailById($stockout_id);
        $shipname = $stockoutdetail[0]['shipname'];

        $templateProcessor->setValue('goodsname', $stockoutdetail[0]['goodsname']);
        $beqty = 0;
        foreach($stockoutdetail as $key=>$value){
            $beqty +=$value['beqty'];
        }
        //已提数
        $templateProcessor->setValue('beqty', $beqty);
        if(!empty($shipname)){
            $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $search = array (
                'bar_shipname' => $shipname,
                'page' => false,
            );
            $list = $S->searchShipList($search);
            $shiplist = $list['list'][0];

            $templateProcessor->setValue('shipname', $shiplist['shipname']);
            $templateProcessor->setValue('shipcontact', $shiplist['shipcontact']);//联系方式
            $templateProcessor->setValue('captain', $shiplist['captain']);//船长
        }

        $booking_out_sysno = $contractInfo['booking_out_sysno'];

        if($contractInfo['ispipelineorder']==1){
            $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $search = array(
                'book_sysno' => $booking_out_sysno?$booking_out_sysno:0,
                'businesstype'=>16,
            );
            $pipedetail = $P->getDetials($search);

            $templateProcessor->cloneRow('pipeline', count($pipedetail));
//
            foreach ($pipedetail as $key => $item) {//货品信息
                $templateProcessor->setValue('pipeline#' . ($key + 1), $item['wharf_pipelineno']);
                $templateProcessor->setValue('wharf_pipelineno#' . ($key + 1), $item['wharf_pipelineno']);
                $templateProcessor->setValue('area_pipelineno#' . ($key + 1), $item['area_pipelineno']);
            }

        }

        if($contractInfo['isberthorder']){
            $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $search = array(
                'book_sysno' => $booking_out_sysno?$booking_out_sysno:0,
                'businesstype'=>16,
            );
            $berthdetail = $B->getDetails($search);
            
        }


        //   print_r($pipedetail);die;

        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '船舶卸货作业流程.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }

    }

}