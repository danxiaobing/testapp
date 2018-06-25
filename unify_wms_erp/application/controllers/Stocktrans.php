<?php
/**
 * 货权转移
 * @author 江浩
 *
 */
class StocktransController extends Yaf_Controller_Abstract {
    public $m;
    public $request;
    public $prefix;
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
        $this->m = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $this->request = $this->getRequest();
        $this->prefix = $this->getRequest()->getControllerName().'_'.$this->getRequest()->getActionName().'_';
    }

    public function indexAction(){

    }
    /**
     * 列表
     */
    public function listAction(){

        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        $params['prefix'] = $this->prefix;

        $this->getView()->make('stocktrans.list', $params);
    }

    /**
     * 列表数据
     */
    public function listJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'stocktransdate_start'=>$request->getPost('stocktransdate_start',''),
            'stocktransdate_end' => $request->getPost('stocktransdate_end',''),
            'stocktransstatus'=>$request->getPost('stocktransstatus',''),
            'sale_customer_sysno'=>$request->getPost('sale_customer_sysno',''),
            'buy_customer_sysno'=>$request->getPost('buy_customer_sysno',''),
            'docsource'=>$request->getPost('docsource',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            //'orders'  => $request->getPost('orders',''),

        );

        $list = $this->m->getList($search);
        //$list = $this->m->searchCumstomerContract($search);
        // print_r($list);die;
        echo json_encode($list);

    }

    /**
     * 编辑
     */
    public function editAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if (!isset($id)) {
            $id = 0;
        }
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if($id){
            $params = $this->m->getTransById($id);
            if($params['cost_contract_type']==1){
                $search = ['bar_id'=>$params['sale_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            }else{
                $search = ['bar_id'=>$params['buy_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            }
            //合同列表
            $params['contractlist'] = $C->searchCustomercontractlist($search);
            //    print_r($params);die;
            $params['prefix'] = $this->prefix.'1_';
            //处理时用
            $params['handle'] = $request->getPost('handle','');
            //编辑时用
            $params['type'] = $request->getParam('type','');
            //查看时用
            $params['look'] = $request->getPost('look','');
            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('stocktrans','attach-1',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
            if($params['type']=='edit'&& ($params['stocktransstatus'] !=2 && $params['stocktransstatus']!=6)){
                COMMON::result('300','请选择暂存的数据');
                return false;
            }
            //得到原货主
            $params['parentShipper'] = $this->m->getparentshipper($id);
            $params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['sale_customer_sysno']);

        }else{
            $params = $_GET;
            if(isset($_GET['booktrans_sysno'])&&$_GET['booktrans_sysno'])
                $params['prefix'] = $this->prefix.'2_'.$_GET['booktrans_sysno'].'_';
            else
                $params['prefix'] = $this->prefix.'2_';
            //合同列表
            $search = ['bar_id'=>$_GET['buy_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            $params['contractlist'] = $C->searchCustomercontractlist($search);
            $params['sysno'] = 0;
            $params['id'] = 0;
        }
        //客户列表
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];


        //有效合同客户列表
        $search = array(
            //  'contractstatus'=>5,
            'contractenddate'=>'NOW()',
            'page' =>false,
            'bar_status'=>1,
            'bar_isdel' => 0,

        );
        $list = $C->searchCustomerContract($search);
        $params['customerlistContract'] =  $list;
//        print_r(array_keys($params));exit;

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname  = $Company->getDefault();
        $params['companyname']  = $companyname['companyname'];

       //  print_r($params);die;
        $this->getView()->make('stocktrans.edit',$params);
    }

    /**
     * 审核
     */
    public function auditJsonAction(){

        $request = $this->getRequest();
        $id = $request->getPost('sysno');
        $status = $request->getPost('status');
        $stockmarks = $request->getPost('stockmarks');
        $rejectreason = $request->getPost('rejectreason');
        $costdata = $request->getPost('costdata','');
        if($costdata['docsource']==2 && $costdata['ca_address']!='' && $costdata['ca_no']!='' ){
                sleep(1);
        }
        // echo '<pre>';
        // var_dump((strtotime($costdata['buystartdate'])-strtotime('2017-05-30'))/3600/24);exit;
        $res = $this->m->audit($id,$status,$stockmarks,$rejectreason,$costdata);

        if($res['statusCode']==200) COMMON::result(200, '审核成功：'.$res['msg']);
        else     COMMON::result(300, '审核失败：'.$res['msg'] );
    }

    /**
     * 添加和编辑数据
     */
    public function editJsonAction(){
        $request = $this->getRequest();
//          print_r($request->getPost());
        $id = $request->getPost('sysno');
        $stocktransstatus = $request->getPost('stocktransstatus');
        $booktrans_sysno = $request->getPost('booktrans_sysno','0');
        $ststatus = $request->getPost('ststatus','');
        $stocktransno  = $request->getPost('stocktransno','');
        $data = [
            'stocktransno'=>trim($stocktransno),
            'stocktransdate' => $request->getPost('stocktransdate'),
            'sale_customer_sysno' => $request->getPost('sale_customer_sysno'),
            'sale_customername' => $request->getPost('sale_customername'),
            'buy_customer_sysno' => $request->getPost('buy_customer_sysno'),
            'buy_customername' => $request->getPost('buy_customername'),
            'buystartdate' => $request->getPost('buystartdate'),
            'bookingtrans_sysno' => $request->getPost('booktrans_sysno'),//通知单编号暂时没有
            'bookingtransno' => $request->getPost('bookingtransno'),
            'transno' => $request->getPost('transno'),
            'transqty' => $request->getPost('transqty'),
            'stocktransstatus' => $request->getPost('stocktransstatus'),
            'contract_sysno' => $request->getPost('contract_sysno','0'),
            'contractno' => $request->getPost('contractno',''),
            'sale_contract_sysno'=>$request->getPost('sale_contract_sysno','0'),//转让方合同id
            'sale_contractno'=>$request->getPost('sale_contractno',''),//转让方合同no
            'freecostdate'=>$request->getPost('freecostdate'),//免仓天数
            'cost_contract_type'=>$request->getPost('cost_contract_type','1'),//合同计费
            'updated_at'=>'=NOW()',
        ];
        //int_r( $data);exit;
        $detail = $request->getPost('detaildata', "");
        $detail = json_decode($detail, true);
        if (count($detail) == 0) {
            COMMON::result(300, '货权转移单明细不能为空');
            return;
        }

        if(in_array($stocktransstatus, [4,5,6])){
            COMMON::result(300, '请按照正规流程审核');
            return;
        }
        //附件
        $attachment = $request->getPost('attachment',array());

        $qualityname =$detail[0]['qualityname'];
        $goodsname =$detail[0]['goodsname'];
        $firstfrom_sysno = $detail[0]['firstfrom_sysno'];
        $contract_sysno = $detail[0]['contract_sysno'];
        //添加货品明细限制，必须同一货品和规格
  //    echo '<pre>';  print_r($detail) ;die;
        $out_stock_sysno = array();
        $mind_array = array();
        foreach($detail as $item){
            if(count($detail)>1){
                if($item['goodsname']!=$goodsname){
                    return COMMON::result('300','货品明细：只能添加一种货品');
                    return false;
                }
                if($item['goodsname'] == $goodsname && $item['qualityname']!=$qualityname){
                    return COMMON::result('300','货品明细：同种货品规格必须相同');
                    return false;
                }

                //判断明细合同id和入库单id必须相同
              //  if($item['firstfrom_sysno']!=$firstfrom_sysno || $item['contract_sysno'] != $contract_sysno){
                if($item['contract_sysno'] != $contract_sysno){
                 //   COMMON::result('300','货品明细来源必须相同');
                    COMMON::result('300','货品明细合同必须相同');
                    return false;
                }
                //明细不能重复添加同一个入库单
                if(!in_array(trim($item['out_stock_sysno']),$out_stock_sysno)){
                    $out_stock_sysno[] = $item['out_stock_sysno'];
                    $mind_array[] = array(
                        'out_stock_sysno'=>$item['out_stock_sysno'],
                        'storagetank_sysno'=>$item['storagetank_sysno'],
                    );
                }elseif(in_array(trim($item['out_stock_sysno']),$out_stock_sysno)){
                    foreach($mind_array as $key=>$value){
                        if($value['out_stock_sysno']==$item['out_stock_sysno'] && $value['storagetank_sysno']==$item['storagetank_sysno']){
                            COMMON::result('300','货品明细不能重复');
                            return false;
                        }else{
                            $mind_array[] = array(
                                'out_stock_sysno'=>$item['out_stock_sysno'],
                                'storagetank_sysno'=>$item['storagetank_sysno'],
                            );
                        }
                    }
                }
            }

            //添加控制储罐商品必须和库存商品一致
            $storagetank_sysno = $item['storagetank_sysno'];
            if($storagetank_sysno){
               //得到储罐商品
               $storagetank_data = $this->m->getGoodsidBystoragetank($storagetank_sysno);
                if($storagetank_data){
                    $goods = intval($item['goods_sysno']);
                    $storagetank_gid = intval($storagetank_data['goods_sysno']);
                    if($goods != $storagetank_gid){
                        COMMON::result('300','储罐与库存货品不一致');
                        return false;
                    }
                }else{
                    COMMON::result('300','获取储罐信息失败');
                    return false;
                }
            }else{
                COMMON::result('300','获取储罐id失败');
                return false;
            }
            //报关数量判断
            if($item['goodsnature'] || $item['release_num']){
               if($item[goodsnature] !=4 && floatval($item['transqty'])>floatval($item['release_num'])){
                   COMMON::result('300','报关数量不足');
                   return false;
               }
            }else{
                COMMON::result('300','报关信息不全');
                return false;
            }

        }
         // print_r($detail);die;
        //判断受让方合同商品
        if((intval($data['contract_sysno']) != -100 && $data['cost_contract_type']==2) || (intval($data['sale_contract_sysno']) != -100 && $data['cost_contract_type']==1)){
            //得到合同的商品
            if($data['cost_contract_type']==2){
                $goods_Contract = $this->m->getgoodsIdBycontractId($data['contract_sysno']);
            }else{
                $goods_Contract = $this->m->getgoodsIdBycontractId($data['sale_contract_sysno']);
            }
       //     print_r($goods_Contract);
            foreach($goods_Contract as $v){
                $goods_Contracts[] = $v['goods_sysno'];
            }
         //   print_r($detail);die;
            foreach($detail as $key=>$value){
                if($id && $value['goods_sysno']==''){
                    $detail[$key]['goods_sysno'] = $this->m->getgoodssysno($value['goodsname']);
                }
                if(!in_array($detail[$key]['goods_sysno'],(array)$goods_Contracts)){
                    COMMON::result('300','合同不包括转移的货品，请选择上家合同！');
                    return false;
                }
            }
        }

        //添加顶点合同
        if($data['cost_contract_type']==1){
            if($data['sale_contract_sysno']==-100 ) {
                $data['sale_contract_sysno'] = $contract_sysno;
                $data['sale_contractno'] = $this->m->getContractName($contract_sysno);
            }
        }else{
            if( $data['contract_sysno']==-100 ) {
                $data['contract_sysno'] = $contract_sysno;
                $data['contractno'] = $this->m->getContractName($contract_sysno);
            }
        }
        //添加或编辑
        if ($id){
            //判断单号是否唯一
            $params = array(
              'stocktransno'=>$data['stocktransno'],
              'id'=>$id,
              'sale_customer_sysno'=>$data['sale_customer_sysno'],
            );
            $stocktransno_c = $this->m->isstocktransnoOnly($params);
          //  print_r($stocktransno_c);die;
            if($stocktransno_c != 0){
                COMMON::result(300, '主单号重复' );
                return false;
            }

            $text = $request->getPost('stockmarks');
            if(!trim($text)){
                if($stocktransstatus==1) $text = '新建';
                if($stocktransstatus==2) $text = '暂存';
                if($stocktransstatus==3) $text = '已提交';
                if($stocktransstatus==4) $text = '已审核';
                if($stocktransstatus==5) $text = '已完成';
                if($stocktransstatus==6) $text = '作废';
            }
            $res = $this->m->update($id,$data,$detail,$text,$attachment,$ststatus);
            if($res['statusCode']==200) COMMON::result(200, '提交成功：'.$res['msg']);
            else     COMMON::result(300, '提交失败：'.$res['msg'] );
        }else{
            //判断单号是否唯一
            $params = array(
                'stocktransno'=>$data['stocktransno'],
                'sale_customer_sysno'=>$data['sale_customer_sysno'],
            );
            $stocktransno_c = $this->m->isstocktransnoOnly($params);
           // print_r($stocktransno_c);die;
            if($stocktransno_c != 0){
                COMMON::result(300, '主单号重复' );
                return false;
            }
            $data['docsource'] = 1;//来源单号
           // $data['stocktransno'] =  COMMON ::getCodeId('T');
            $res = $this->m->add($data,$detail,'新建',$booktrans_sysno,$attachment);
            if($res['statusCode']==200) COMMON::result(200, '提交成功：'.$res['msg']);
            else     COMMON::result(300, '提交失败：'.$res['msg']);
        }
    }
    /*
     * 控货比重
     * */
    function  controlgoodsAction(){
        $request = $this->getRequest();
        $data = $request->getPost('data','');
        $detail=$request->getPost('detail',array());
        $params['goods_sysno'] =$this->m->getGoodaSysno($detail[0]['goodsname']);
        foreach($detail as $value){
            $params['num']+=$value['transqty'];
        }
        if($data['cost_contract_type']==1){
            $params['customer_sysno'] =$data['sale_customer_sysno'];
            $params['contract_sysno'] =$data['sale_contract_sysno'];
        }elseif($data['cost_contract_type']==2){
            $params['customer_sysno'] =$data['buy_customer_sysno'];
            $params['contract_sysno'] =$data['contract_sysno'];
        }
        $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $controlgoods = $S->controlgoods($params);
        if(empty($detail) || !is_array($detail)){
            $info = [
                'code'=>'400',
                'message'=>'明细不能为空！',
            ];
            echo json_encode($info);
            return;
        }
        foreach($detail as $key=>$value){
            $mag['goodsname'] = $value['goodsname'];
            $mag['stockqty'] = $value['stockqty'];
            $mag['transqty'] = $value['transqty'];
            $mag['qualityname'] = $value['qualityname'];
            $mag['goodsnature'] = $value['goodsnature'];
            $mag['stockin_no'] = $value['stockin_no'];
            $mag['storagetank_sysno'] = $value['storagetank_sysno'];
            if(count($mag)==7 && in_array('',$mag) ) {
                $info = [
                    'code' => '400',
                    'message' => '明细信息不全，请补全！',
                ];
                echo json_encode($info);
                return ;
            }
        }
        echo json_encode($controlgoods);

    }
    /*
     * 合同类型
     * */
    public function controltypeAction(){
        $request = $this->getRequest();
        $data = $request->getPost('data','');
        $detail=$request->getPost('detail',array());
        $contract_sysno = $detail[0]['contract_sysno'];
        //添加顶点合同
        if($data['cost_contract_type']==1){
            if($data['sale_contract_sysno']==-100 ) {
                $info_control_sysno = $contract_sysno;
            }else{
                $info_control_sysno = $data['sale_contract_sysno'];
            }
        }else{
            if( $data['contract_sysno']==-100 ) {
                $info_control_sysno = $contract_sysno;
            }else{
                $info_control_sysno = $data['contract_sysno'];
            }
        }
        //获取合同类型

        $contract_type = $this->m->getcontroltype($info_control_sysno);
        if($contract_type){
            echo json_encode($contract_type);
        }else{
            echo json_encode(['contracttype'=>'0']);
        }

        //  print_r($data);die;

    }

    /**
     * 删除数据
     */
    public function delJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $oneId = explode(',',$id);
        // print_r($oneId);die;
        if(count($oneId)==1){
            $res = $this->m->deleteTrans($id,['isdel' => 1]);
            if ($res) {
                COMMON::result(200, '删除成功');
            } else {
                COMMON::result(300, '删除失败');
            }
        }else{
            COMMON::result('300','请选择一条数据');
            return false;
        }
    }
    /*
     * 查看
     * */
    function lookstocktrankAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if (!isset($id)) {
            $id = 0;
        }
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if($id){
            $params = $this->m->getTransById($id);
            if($params['cost_contract_type']==1){
                $search = ['bar_id'=>$params['sale_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            }else{
                $search = ['bar_id'=>$params['buy_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            }
            //合同列表
            $params['contractlist'] = $C->searchCustomercontractlist($search);
            //    print_r($params);die;
            $params['prefix'] = $this->prefix.'1_';
            //处理时用
            $params['handle'] = $request->getPost('handle','');
            //编辑时用
            $params['type'] = $request->getParam('type','');
            //查看时用
            $params['look'] = $request->getPost('look','');
            if(!$params['look']){
                $params['look'] = $request->getParam('look','');
            }
            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('stocktrans','attach-1',$id);
            // $attach2 = $A->getAttachByMAS('stocktrans','attach-2',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
            //print_r($params);die;
            if($params['type']=='edit'&& ($params['stocktransstatus'] !=2 && $params['stocktransstatus']!=6)){
                COMMON::result('300','请选择暂存的数据');
                return false;
            }

            $params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['sale_customer_sysno']);
        }else{
            $params = $_GET;
            if(isset($_GET['booktrans_sysno'])&&$_GET['booktrans_sysno'])
                $params['prefix'] = $this->prefix.'2_'.$_GET['booktrans_sysno'].'_';
            else
                $params['prefix'] = $this->prefix.'2_';
            //合同列表
            $search = ['bar_id'=>$_GET['buy_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
            $params['contractlist'] = $C->searchCustomercontractlist($search);
            $params['sysno'] = 0;
            $params['id'] = 0;
        }
        //客户列表
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        //$params['codeid'] = COMMON ::getCodeId('S');

        //有效合同客户列表
        $search = array(
            //  'contractstatus'=>5,
            'contractenddate'=>'NOW()',
            'page' =>false,
            'bar_status'=>1,
            'bar_isdel' => 0,
        );
        $list = $C->searchCustomerContract($search);
        $params['customerlistContract'] =  $list;

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname  = $Company->getDefault();
        $params['companyname']  = $companyname['companyname'];

        $this->getView()->make('stocktrans.lookstocktrank',$params);
    }
    /*
     * 附件
     * */
    public  function lookImagelistAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $module = 'stocktrans';
        $action = 'attach-1';
        $imageData = $A->getAttachMAS($id,$module,$action);
        //  $imageData = $A->getAttachByDocsysno($id);
        $params['imageData'] = $imageData;

        $this->getView()->make('stocktrans.stocktrankimagelist',$params);

    }
    /**
     * 添加和编辑明细
     */
    public function editDetailAction(){
        $id = $this->request->getParam('id', '0');
        $prefix = $this->request->getParam('prefix', '');
        if($id){
//            $where['stocktrans_sysno'] = $id;
//            $list = $this->m->getListDetials($where);
//            $params = $list?$list[0]:null;
//            $params['prefix'] = $prefix;
//            $s = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
//            $stocks = $s->getElementById([$params['out_stock_sysno']]);
//            $params['stockqty'] = count($stocks)?$stocks[0]['stockqty']:0;
//            $params=array();

            $params['prefix'] = $prefix;
        }else{
            $params['prefix'] = $prefix;
        }
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['storagetanklist'] = $S->getStoragetank();
        $this->getView()->make('stocktrans.adddetail',$params);
    }
    /*
     * 编辑货权转移明细
     *
     * */
    function stocktrankeditAction(){
        $request = $this->getRequest();
        $params = $request->getPost('selectedDatasArray',array());
       // print_r($params);die;
        $detailtype = $request->getParams('prefix','');
        $params['prefix'] = $detailtype['prefix'];
        $params['sale_customer_sysno'] = $request->getPost('sale_customer_sysno','0');
        //储罐
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['storagetanklist'] = $S->getStoragetank();
        //获取储罐详细信息
        $search = ['sysno'=>$params['storagetank_sysno'],
                   'page'=>false,
        ];
        $storagetankData =$this->m->getstocktankList($search);
        $params['tank_stockqty'] = $params['tank_stockqty']?$params['tank_stockqty']:$storagetankData['list'][0]['tank_stockqty'];
       // echo '<pre>';   print_r($params);die;
        $this->getView()->make('stocktrans.adddetail',$params);
    }

    /**
     * 明细列表json
     */
    public function detailListJsonAction(){
        //查询主表
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        if($id){
            $where['stocktrans_sysno'] = $id;
            $list = $this->m->getListDetials($where);
            /*
            $params = $this->m->getTransById($id);
            $search = array(
                'bar_id'=>$params['sale_customer_sysno'],
                'bar_contractstatus'=>6,
                'bar_status' => 1,
                'bar_isdel' => 0,
                'page' => false
            );
            $list = $C->searchCustomercontractlist($search);
            */
        }

        $list = $list?$list:array();
        //    print_r($list);die;
        echo json_encode($list);
    }
    /**
     * 详细提交
     */
    public function detailsubmitAction(){
        $request = $this->getRequest();
        $stock_sysno = $request->getPost('obj_sysno',0);
        $transqty = $request->getPost('transqty');
        $goods_sysno = $request->getPost('obj_goods_sysno');
        $unitname = $request->getPost('obj_unitname');
        $goodsname = $request->getPost('obj_goodsname');
        $storagetank_sysno = $request->getPost('storagetank_sysno');
        $storagetankname = $request->getPost('storagetankname');
        $shipname = $request->getPost('shipname');
        $s = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        //  $list = $s->getElementById([$stock_sysno]);
        $list = $this->m->getStockdetailById([$stock_sysno]);
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
     * 库存列表
     */
    public function stockListJsonAction(){
        $cid = $this->request->getParam('cid','0');
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = [ 'page' => false, 'customer_sysno' => $cid,'iscurrent' => 1 ];
        $stockdata =  $this->m->getstockList($search);
        //   $stockdata =  $S-> getStockList($search);
       //  echo "<pre>";  print_r($stockdata);die;
        echo json_encode($stockdata['list']);
    }
    /*
     * 添加储罐信息表
     * */
    public function storagetankListAction(){
        $request = $this->getRequest();
        $goods_sysno = $request->getParam('goods_sysno','');
        $S = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = [ 'page' => false,
                    'goods_sysno' => $goods_sysno
                  ];
        $stockdata =  $S->getstocktankList($search);
    //      print_r($stockdata);die;
        echo json_encode($stockdata['list']);

    }

    /*
     *  新增合同联动
     * */

    public function customercontractJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $search = array(
            'bar_id'=>$id,
            'bar_contractstatus'=>5,
            'bar_contractenddate'=>'1',
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
        $list[] = array('value'=>'-100','label'=>'默认上家货权转移合同');

        echo json_encode($list);

    }

    /*
     * 获取提货样单
     * */

    public function customerSampleJsonAction(){
        $request = $this->getRequest();
        $cid = $request->getPost('cid','0');

        $A = new AttachmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $sample = $A->getAttachByMAS('customer','customerlading',$cid);

        echo json_encode($sample);
    }

    /**
     * 导出
     */
    public function export1Action(){

        $search = array (
            'stocktransdate_start'=>$this->request->getPost('stocktransdate_start',''),
            'stocktransdate_end' => $this->request->getPost('stocktransdate_end',''),
            'sale_customer_sysno' => $this->request->getPost('sale_customer_sysno',''),
            //'buy_customer_sysno' => $request->getPost('sale_customer_sysno',''),
            'stocktransstatus'=>$this->request->getPost('stocktransstatus',''),
            'orders'  => $this->request->getPost('orders',''),
            'pageSize'=>false,
        );
        $list = $this->m->getList($search);
        //  print_r($list);die;

        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=货权转移列表.xls");


        echo
            iconv("UTF-8","GBK//IGNORE", "单据编号")."\t".
            iconv("UTF-8", "GBK//IGNORE", "单据来源")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货物性质")."\t".
            iconv("UTF-8", "GBK//IGNORE", "货品名称")."\t".
            iconv("UTF-8", "GBK//IGNORE","转让名称")."\t".
            iconv("UTF-8", "GBK//IGNORE", "受让方名称")."\t".
            iconv("UTF-8", "GBK//IGNORE", "数量")."\t".
            iconv("UTF-8", "GBK//IGNORE", "计量单位")."\t".
            /* iconv("UTF-8", "GBK//IGNORE", "转移日期")."\t".*/
            iconv("UTF-8", "GBK//IGNORE", "受让方起始日")."\t".
            iconv("UTF-8", "GBK//IGNORE", "单据状态")."\t";
        /* iconv("UTF-8", "GBK//IGNORE", "创建时间")."\t";*/

        foreach ($list['list'] as $key=>$item) {
            if($item['stocktransstatus']==1) $status = '新建';
            else if($item['stocktransstatus']==2) $status = '暂存';
            else if($item['stocktransstatus']==3) $status = '待审核';
            else if($item['stocktransstatus']==4) $status = '已审核';
            else if($item['stocktransstatus']==5) $status = '已完成';
            else if($item['stocktransstatus']==6) $status = '退回';

            if($item['docsource']==1){
                $list[$key]['docsource'] = '手工创建';
            }elseif($item['docsource']==2){
                $list[$key]['docsource'] = '国烨云仓';
            }elseif($item['docsource']==3){
                $list[$key]['docsource'] = '初始化导入';
            }

            if($item['goodsnature']==1) $goodsnature='保税';
            else if($item['goodsnature']==2) $goodsnature='外贸';
            else if($item['goodsnature']==3) $goodsnature='内贸转出口';
            else  $goodsnature='内贸内销';
            echo "\n";
            echo '' .iconv("UTF-8", "GBK//IGNORE", $item['stocktransno'])                                    //单据编号
                ."\t".iconv("UTF-8", "GBK//IGNORE", $list[$key]['docsource'])      //单据来源
                ."\t".iconv("UTF-8", "GBK//IGNORE", $goodsnature)                //货物性质
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['goodsname'])           //货品名称
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['sale_customername'])   //转让名称
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['buy_customername'])    //受让方名称
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['qty'])             //数量
                ."\t".iconv("UTF-8", "GBK//IGNORE", '吨')         //计量单位
//            ."\t".$item['stocktransdate']                                       //转移日期
                ."\t".$item['buystartdate']                                         //受让方起始日
                ."\t".iconv("UTF-8", "GBK//IGNORE", $status) ;        //单据状态
//            ."\t".iconv("UTF-8", "GBK//IGNORE", $item['created_at']);
        }
    }

}