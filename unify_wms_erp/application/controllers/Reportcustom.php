<?php
use Gregwar\Captcha\CaptchaBuilder;

class ReportcustomController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
   // public function __construct($dbh, $mch)
  //  {
       // $this->dbh = $dbh;

      //  $this->mch = $mch;
   // }
    public function init() {
        # parent::init();


    }

    /**
     * 显示客户收发存汇总表
     *
     * @return string
     */
    public function listAction() {
        $params = array( );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] = $list['list'];
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goods'] = $goods->getGoodsInfo();
        $this->getView()->make('Reportcustom.list',$params);
    }

    public function listJsonAction(){
        $request=$this->getRequest();
        $search = array(
            'startTime' => $request->getPost('startTime',date('Y-m-d')),
            'endTime' =>  $request->getPost('endTime',date('Y-m-d')),
            'customer_sysno' => $request->getPost('customername',''),
            'goods_sysno' =>  $request->getPost('goodsname',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $C = new ReportcustomModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
       // $list = $C->getCustomercollerlist($search);
          $list = $C->getList($search);
         echo json_encode($list);
    }

    public function detailAction() {
        $request=$this->getRequest();
            $params = [
                'startTime'=>$request->getPost('startTime',''),
                'endTime'=>$request->getPost('endTime',''),
                'customer_sysno'=> $request->getPost('customer_sysno',''),
                'goods_sysno'=>$request->getPost('goods_sysno',''),
                'ghoststockqty'=> $request->getPost('ghoststockqty',''),
                'endmath'=>$request->getPost('endmath',''),
                'pageCurrent' => COMMON::P(),
                'pageSize' => COMMON::PR(),
            ];

        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] = $list['list'];
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goods'] = $goods->getGoodsInfo();
//        print_r($params);die;
        $this->getView()->make('Reportcustom.detail',$params);

    }

    public function detailJsonAction() {
          $request=$this->getRequest();
          $goods_sysno = $request->getPost('goodsname','');
          $customer_sysno = $request->getPost('customername','');
          $startTime = $request->getPost('startdate','');
          $endTime = $request->getPost('enddate','');
        if(!$startTime || !$endTime){
            $startTime = $request->getParam('startTime','');
            $endTime = $request->getParam('endTime','');
        }
        if(!$goods_sysno){
            $goods_sysno = $request->getParam('goods_sysno','');
        }
        if(!$customer_sysno){
            $customer_sysno = $request->getParam('customer_sysno','');
        }
          $params = [
                'startTime'=>$startTime,
                'endTime'=>$endTime,
                'customer_sysno'=> $customer_sysno,
                'goods_sysno'=>$goods_sysno,
                'pageCurrent' => COMMON::P(),
                'pageSize' => COMMON::PR(),
           ];
       // print_r($params);die;
        $C = new ReportcustomModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail  = $C->getDateil($params);
      //  print_r($detail);die;
        if(!empty($detail['list'])) {
            foreach ($detail['list'] as $key => $value) {
                if ($value['type'] == 1) {
                    if ($value['stockintype'] == 1) {
                        $detail['list'][$key]['transport'] = $value['shipname'];
                    } elseif ($value['stockintype'] == 2) {
                        $detail['list'][$key]['transport'] = '槽车';
                    } elseif ($value['stockintype'] == 3) {
                        $detail['list'][$key]['transport'] = '管输';
                    } else {
                        $detail['list'][$key]['transport'] = '';
                    }
                }
                if ($value['type'] == 2) {
                    if ($value['stockouttype'] == 1) {
                        $detail['list'][$key]['transport'] = $value['shipname'];
                    } elseif ($value['stockouttype'] == 2) {
                        $detail['list'][$key]['transport'] = '槽车';
                    } elseif ($value['stockouttype'] == 3) {
                        $detail['list'][$key]['transport'] = '管出';
                    } else {
                        $detail['list'][$key]['transport'] = '';
                    }
                }
                if ($value['type'] == 3 || $value['type'] == 4) {
                    if ($value['stockintype'] == 1) {
                        $detail['list'][$key]['transport'] = $value['shipname'];
                    } elseif ($value['stockintype'] == 2) {
                        $detail['list'][$key]['transport'] = '槽车';
                    } elseif ($value['stockintype'] == 3) {
                        $detail['list'][$key]['transport'] = '管输';
                    } else {
                        $detail['list'][$key]['transport'] = '';
                    }
                }
            }
        }
      //  print_r($detail);die;
        echo  json_encode($detail);

    }
    //搜索
    public function ajaxgetqtyAction()
    {
        $request = $this->getRequest();
        $goods_sysno = $request->getPost('goods_sysno','');
        $customer_sysno = $request->getPost('customer_sysno','');
        $startTime = $request->getPost('startTime','');
        $endTime = $request->getPost('endTime','');
        $search = array(
            'goods_sysno'=>$goods_sysno,
            'customer_sysno'=>$customer_sysno,
            'startTime'=>$startTime,
            'endTime'=>$endTime,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $R = new ReportcustomModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );
        $params = $R->getList($search);
        $endmath = $params['list'][0]['endmath'];
        $ghoststockqty = $params['list'][0]['ghoststockqty'];
        $arr = array(
            'endmath' => $endmath,
            'ghoststockqty' => $ghoststockqty
        );
        echo json_encode($arr);
    }

    /**
     * 导出
     */
    public function export1Action(){
        $request = $this->getRequest();
        $search = array (
            'startTime'=>$request->getPost('startTime',''),
            'endTime' => $request->getPost('endTime',''),
            'customer_sysno' => $request->getPost('customername',''),
            'goods_sysno' => $request->getPost('goodsname',''),
            'pageSize'=>false,
            'page'=>false,
        );
        $R = new ReportcustomModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc"));
        $list =  $R->getList($search);

         // print_r($list);die;

        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=客户收发汇总表.xls");

        echo
            iconv("UTF-8","GBK//IGNORE", "客户")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货品")."\t".
            iconv("UTF-8", "GBK//IGNORE","计量单位")."\t".
            iconv("UTF-8", "GBK//IGNORE", "期初数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "入库数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货权转移入库数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "出库数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货权转移出库数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "损耗量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "期末余量")."\t";

        foreach ((array)$list as $key=>$item) {
            if($item['stocktransstatus']==1) $status = '新建';
            else if($item['stocktransstatus']==2) $status = '暂存';
            else if($item['stocktransstatus']==3) $status = '待审核';
            else if($item['stocktransstatus']==4) $status = '已审核';
            else if($item['stocktransstatus']==5) $status = '已完成';
            else if($item['stocktransstatus']==6) $status = '退回';
            if($item['ghoststockqty']==''){
                 $list[$key]['ghoststockqty'] =0;
            }
            if($item['unitname']=='' || !isset($item['unitname'])){
                $list[$key]['unitname'] ='吨';
            }
            if($item['instockqty']=='' || !isset($item['instockqty'])){
                $list[$key]['instockqty'] =0;
            }
            if($item['intransqty']=='' || !isset($item['intransqty'])){
                $list[$key]['intransqty'] =0;
            }
            if($item['outstockqty']=='' || !isset($item['outstockqty'])){
                $list[$key]['outstockqty'] =0;
            }
            if($item['outtransqty']=='' || !isset($item['outtransqty'])){
                $list[$key]['outtransqty'] =0;
            }
            if($item['lossqty']=='' || !isset($item['lossqty'])){
                $list[$key]['lossqty'] =0;
            }

            echo "\n";
            echo iconv("UTF-8", "GBK//IGNORE", $item['customername'])           //客户
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['goodsname'])             //货品
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['unitname'])          //计量单位
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['ghoststockqty'])     //期初数量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['instockqty'])             //入库数量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['intransqty'])         //货权转移入库数量
                ."\t".$list[$key]['outstockqty']                                       //出库数量
                ."\t".$list[$key]['outtransqty']                                   //货权转移出库数量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['lossqty'])         //清库数量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['endmath']);         //期末余量
        }
    }

    /**
     * 导出
     */
    public function export2Action(){
        $request=$this->getRequest();
        $goods_sysno = $request->getPost('goodsname','');
        $customer_sysno = $request->getPost('customername','');
        $startTime = $request->getPost('startdate','');
        $endTime = $request->getPost('enddate','');
        $search = [
            'startTime'=>$startTime,
            'endTime'=>$endTime,
            'customer_sysno'=> $customer_sysno,
            'goods_sysno'=>$goods_sysno,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
            'page'=>false,
        ];
        $R = new ReportcustomModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );
        $list =  $R->getDateil($search);
      //  print_r($list);die;
        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=客户收发明细表.xls");

        echo
            iconv("UTF-8","GBK//IGNORE", "单据日期")."\t".
            iconv("UTF-8", "GBK//IGNORE", "单据编号")."\t".
            iconv("UTF-8", "GBK//IGNORE","单据类型")."\t".
            iconv("UTF-8", "GBK//IGNORE","槽车/船名")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货物性质")."\t".
            iconv("UTF-8", "GBK//IGNORE", "计量单位")."\t".
            iconv("UTF-8", "GBK//IGNORE", "商检量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货权转入量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "出货量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货权转出量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "清库量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "结存量")."\t";

        foreach ((array)$list['list'] as $key=>$item) {
            if($item['goodsnature']==1){
                $list[$key]['goodsnature'] ='保税';
            }elseif($item['goodsnature']==2){
                $list[$key]['goodsnature'] ='外贸';
            }elseif($item['goodsnature']==3){
                $list[$key]['goodsnature'] ='内贸转出口';
            }else{
                $list[$key]['goodsnature'] ='内贸内销';
            }

            if($item['type']==1){
                $list[$key]['type'] ='入库单';
            }elseif($item['type']==2){
                $list[$key]['type'] ='出库单';
            }elseif($item['type']==3){
                $list[$key]['type'] ='货权转入单';
            }elseif($item['type']==4){
                $list[$key]['type'] ='货权转出单';
            }elseif($item['type']==5){
                $list[$key]['type'] ='清库单';
            }

            if ($item['type'] == 1) {
                if ($item['stockintype'] == 1) {
                    $list[$key]['transport'] = $item['shipname'];
                } elseif ($item['stockintype'] == 2) {
                    $list[$key]['transport'] = '槽车';
                } elseif ($item['stockintype'] == 3) {
                    $list[$key]['transport'] = '管输';
                } else {
                    $list[$key]['transport'] = '';
                }
            }
            if ($item['type'] == 2) {
                if ($item['stockouttype'] == 1) {
                    $list[$key]['transport'] = $item['shipname'];
                } elseif ($item['stockouttype'] == 2) {
                    $list[$key]['transport'] = '槽车';
                } elseif ($item['stockouttype'] == 3) {
                    $list[$key]['transport'] = '管出';
                } else {
                    $list[$key]['transport'] = '';
                }
            }
            if ($item['type'] == 3 || $item['type'] == 4) {
                if ($item['stockintype'] == 1) {
                    $list[$key]['transport'] = $item['shipname'];
                } elseif ($item['stockintype'] == 2) {
                    $list[$key]['transport'] = '槽车';
                } elseif ($item['stockintype'] == 3) {
                    $list[$key]['transport'] = '管输';
                } else {
                    $list[$key]['transport'] = '';
                }
            }

            echo "\n";
            echo iconv("UTF-8", "GBK//IGNORE", $item['dateTime'])           //单据日期
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['dateno'])             //单据编号
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['type'])          //单据类型
                ."\t".iconv("UTF-8", "GBK//IGNORE",  $list[$key]['transport'])          //运输
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['goodsnature'])     //货物性质
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['unitname']?$item['unitname']:'吨')             //计量单位
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty1']?$item['qty1']:0 )             //商检量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty3']?$item['qty3']:0)             //货转入
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty2']?$item['qty2']:0)             //出货量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty4']?$item['qty4']:0)             //货权转出
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty5']?$item['qty5']:0)             //清库量
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['balanceqty']);         //结存量
        }
    }

}
