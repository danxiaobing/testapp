<?php
/**
 * 倒罐管理
 * User: Alan
 * Date: 2016-11-23 15:22:09
 * Time: 17:09
 */
class RetankController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     * @return void
     */
    public function init()
    {
        # parent::init();
    }

    /**
     * 倒罐申请单列表页面显示
     */
    public function applyAction(){
        $params = array();
        $this->getView()->make('retank.apply',$params);
    }

    /*
     * 倒罐申请单列表数据加载
     */
    public function applyjsonAction(){
        $request = $this->getRequest();
        $search = array (
            'bar_no' => $request->getPost('bar_no',''),
            'bookingretankdate' => $request->getPost('bookingretankdate',''),
            'bookingretankdate_end' => $request->getPost('bookingretankdate_end',''),
            'stockretankstatus' => $request->getPost('stockretankstatus',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->searchapplyretank($search);
        echo json_encode($list);
    }

    /*
     * 编辑倒罐申请单 显示页面
     */
    public function applyeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode','');
        if(!isset($id)) {
            $id = 0;
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(!$id){
            $action = "/retank/newapplyJson/";
            $params =  array();
        } else {
            if($mode =='edit'){
                $action = "/retank/editapplyJson/";
            }elseif($mode =='audit'){
                $action = "/retank/auditapplyJson/";
            }elseif($mode =='back'){
                $action = "/retank/backapplyJson/";
            }elseif($mode =='addattach'){
                $action = "/retank/addattachment/";
            }
            $row = $R->getapplyretank($id);
            if($row['stockretankstatus']!=2 && $row['stockretankstatus']!=7 && $mode=='edit'){
                COMMON::result(300, '只能选择暂存或退回状态的数据');
                return;
            }

            $params = $row;
            //获取附件
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('bookretank','bookretank-edit',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
        }
        //获取所有商品品名
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsInfos();
        $params['goodslist']=$goods;

        //获取客服
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] =  $id;
        $params['mode'] =  $mode;
        $params['action'] =  $action;

        $this->getView()->make('retank.applyretankedit',$params);
    }

    /*
     * 编辑倒罐申请单 显示详情页面
     */
    function retankdetaileditAction(){
        $request = $this->getRequest();
        $params = $request->getPost('selectedDatasArray',array());
        $params['handlestatus'] = $request->getParam('handlestatus','');
        $params['goods_sysno'] = $request->getParam('goods_sysno','');
        $params['stocktype'] = $request->getParam('stocktype',0);
        $goods_sysno= $params['goods_sysno'];
        $R = new RetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $R->getCustomerBygoodssyno($goods_sysno);
        $params['customerlist'] = $list;

        $this->getView()->make('retank.applyretankadddetail',$params);
    }

    /**
     * 获取倒罐申请单详情数据
     */
    public function addgendetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        if(!isset($id)) {
            $id = 0;
        }

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        // 获取 倒罐信息的详情信息单
        $list = $R->getapplyretankdetailById($id);
        echo json_encode($list);
    }

    /**
     * 添加添加倒罐预约单详情数据
     */
    public function addappdetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        if(!isset($id)) {
            $id = 0;
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        // 获取 倒罐信息的详情信息单
        $list = $R->getapplyretankdetailById($id);
        foreach ($list as $key=>$value){
            unset($list[$key]['sysno']);
            $list[$key]['sysno']=$value['out_stock_sysno'];
        }
        echo json_encode($list);
    }

    /*
     * 新建倒罐申请单
     */
    public function newapplyJsonAction()
    {
        //获取所添加的信息
        $request = $this->getRequest();
        $stockretankstatus = $request->getPost('stockretankstatus', 0);
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);

        if (count($retankdetaildata) == 0) {
            COMMON::result(300, '倒罐单明细不能为空');
            return;
        }
        $unique = array();
        foreach ($retankdetaildata as $key => $value) {
            if($value['goodsnature']>=1 && $value['goodsnature']<=2){
                  $unique[$value['storagetank_sysno']][]=$value['bookingretankqty'];
            }

            $length=count($retankdetaildata);
            for($i = 0; $i < $length; $i ++){
                for($j = $i + 1; $j < $length; $j ++){
                    if($retankdetaildata[$i]['sysno'] == $retankdetaildata[$j]['sysno'] && $retankdetaildata[$i]['stockretank_in_sysno'] == $retankdetaildata[$j]['stockretank_in_sysno'])
                    {
                        COMMON::result(300, '第'.($i+1).'条记录与第'.($j+1).'条倒罐申请记录重复！');
                        return;
                        break;
                    }
                }
            }
        }

        $newone=[];
        if(!empty($unique)){
            foreach ($unique as $key=>$value){
                $newone[$key] = array_sum($value);
            }
        }
        foreach($retankdetaildata as $key => $value){
            foreach ($newone as $k=>$v){
                if($value['storagetank_sysno']==$k){
                    if($value['release_num']<$v){
//                        COMMON::result(300,$value['storagetankname'] .'罐申请倒出数量不能大于报关数量');
//                        return;
                        break;
                    }
            }
            }
        }

        $gid = $request->getPost('goods_sysno', 0);
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsName($gid);
        $retank = array(
            'bookingretankno' => COMMON::getCodeId('Y1'),
            'bookingretankdate' => $request->getPost('bookingretankdate', ''),
            'stocktype' => $request->getPost('stocktype', 0),
            'goods_sysno' => $request->getPost('goods_sysno', 0),
            'goodsname' => $goods[0]['goodsname'],
            'ispipelineorder' => $request->getPost('ispipelineorder', 0),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', 0),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockretankstatus' => $stockretankstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => "=NOW()",
            'updated_at' => "=NOW()"
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //附件
        $attachment = $request->getPost('attachment',array());
//        var_dump($retankdetaildata);die();
        $res = $R->addapplyRetank($retank,$retankdetaildata,$attachment);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getapplyretank($params['id']);
            COMMON::result(200, '新增成功',$row);
        } else {
            COMMON::result(300, '新增失败：'.$res['msg'] );
        };
    }

    /**
     * 编辑倒罐申请单
     */
    public function  editapplyJsonAction()
    {
        //获取所添加的信息
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', 0);
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);
        if (count($retankdetaildata) == 0) {
            COMMON::result(300, '倒罐单明细不能为空');
            return;
        }

        $unique = array();
        foreach ($retankdetaildata as $key => $value) {
            $unique[$key] = $value['stockinno'];
            if($value['stockretank_out_no'] == $value['stockretank_in_no'])
            {
                COMMON::result(300, '移入,移出罐号不可相同');
                return;
                break;
            }
        }
        $gid = $request->getPost('goods_sysno',0);
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods = $G->getGoodsName($gid);
        $retank = array(
            'bookingretankdate' => $request->getPost('bookingretankdate', ''),
            'stocktype' => $request->getPost('stocktype', 0),
            'goods_sysno' => $gid,
            'goodsname' => $goods[0]['goodsname'],
            'ispipelineorder' => $request->getPost('ispipelineorder', 0),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', 0),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'updated_at' => "=NOW()"
        );
        //附件
        $attachment = $request->getPost('attachment',array());
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->updateapplyRetank($id,$retank,$retankdetaildata,$stockretankstatus,$attachment);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getapplyretank($params['id']);
            COMMON::result(200, '更新成功',$row);
        } else {
            COMMON::result(300, '更新失败：'.$res['msg'] );
        };
    }

    /**
     * 审核倒罐申请单
     */
    public function auditapplyJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', '');
        if ($stockretankstatus == 6) {
            if ($request->getPost('auditreason') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $retank = array(
            'bookingretankno' =>$request->getPost('bookingretankno',''),
            'bookingretankdate' =>$request->getPost('bookingretankdate',''),
            'ispipelineorder' =>$request->getPost('ispipelineorder',''),
            'stockretankstatus' => $stockretankstatus,
            'auditreason' =>$request->getPost('auditreason',''),
            'updated_at' => '=NOW()'
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->auditapplyRetank($id,$retank,$stockretankstatus);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getapplyretank($params['id']);
            COMMON::result(200, '审核成功',$row);
        } else {
            COMMON::result(300, '审核失败：'.$res['msg'] );
        };
    }

    /*
     * 退回倒罐申请单
     */
    public function backapplyJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', '');

        $retank = array(
            'stockretankstatus' => $stockretankstatus,
            'backreason' => $request->getPost('backreason', ''),
            'updated_at' => '=NOW()'
        );

        if ($request->getPost('backreason') == '') {
            COMMON::result(300, '退回备注不能为空');
            return;
        }

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->backRetankapply($id,$retank);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getapplyretank($params['id']);
            COMMON::result(200, '退回成功',$row);
        } else {
            COMMON::result(300, '退回失败：'.$res['msg'] );
        };
    }

    /*
     * 查看倒罐申请单
     * */
    function lookretankapplyAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode',0);
        if(!isset($id)) {
            $id = 0;
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $R->getapplyretank($id);
        // 获取 倒罐信息的详情信息单
        $list = $R->getapplyretankdetailById($id);
        foreach ($list as $key=>$value){
            $params['customername']=$value['customername'];
            $params['stockretank_out_no']=$value['stockretank_out_no'];
            $params['stockretank_in_no']=$value['stockretank_in_no'];
            $params['bookingretankqty']=$value['bookingretankqty'];
            $params['goodsname']=$value['goodsname'];
        }

        //获取附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach1 = $A->getAttachByMAS('retank','retank-edit',$id);
        if( is_array($attach1) && count($attach1)){
            $files1 = array();
            foreach ($attach1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['attach1']  =  join(',',$files1);
        }

        //获取客服
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        //获取所有商品品名
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsInfos();
        $params['goodslist']=$goods;
        $params['id'] =  $id;
        $params['mode'] =  $mode;

        $this->getView()->make('retank.applyretankedit',$params);
    }

    /**
     * 删除 倒罐申请单
     */
    public function delapplyjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);
        $oneId = explode(',',$id);
        if(count($oneId)==1){
            $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $retankdata = $R->getapplyretank($id);
            if($retankdata['stockretankstatus']>=3&&$retankdata['stockretankstatus']<=6){//
                COMMON::result(300,'只有暂存或退回的倒罐单才能删除');
                return ;
            }

            $retank = array(
                'isdel'=>1
            );

            if($R->delapplyRetank($id,$retank)){
                COMMON::result(200,'删除成功');
            }else{
                COMMON::result(300,'删除失败');
                return false;
            }
        }else{
            COMMON::result('300','请选择一条数据');
            return false;
        }
    }

    /*
     * EXCEL导出倒罐申请单
     */
    public function excelapplyAction(){
        $request = $this->getRequest();
        $search = array (
            'bar_no' => $request->getPost('retankno',''),
            'bookingretankdate' => $request->getPost('begin_time',''),
            'bookingretankdate_end' => $request->getPost('end_time',''),
            'stockretankstatus' => $request->getPost('retankstatus',''),
            'page' => false,
        );

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->searchapplyretank($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("倒罐申请单列表")
            ->setSubject("列表")
            ->setDescription("倒罐申请单列表");


        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '倒罐申请单单号'),
            array('B1:B1', 'B1', '005E9CD3', '申请日期'),
            array('C1:C1', 'C1', '005E9CD3', '品名'),
            array('D1:D1', 'D1', '005E9CD3', '创建人'),
            array('E1:E1', 'E1', '0094CE58', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('倒罐申请单列表');

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
                        $value = $item['bookingretankno'];
                        break;
                    case 1:
                        $value = $item['bookingretankdate'];
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
                        $value = $item['zj_employeename'];
                        break;
                    case 4:
                        if ($item['stockretankstatus']==2) {
                            $value = "暂存";
                        }else if ($item['stockretankstatus']==4) {
                            $value = "待审核";
                        }else if ($item['stockretankstatus']==5) {
                            $value = "已审核";
                        }else if ($item['stockretankstatus']==6) {
                            $value = "作废";
                        }else if ($item['stockretankstatus']==7) {
                            $value = "退回";
                        }else{
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $time = date('Y-m-d H:m:s',time());
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="倒罐申请单列表_'.$time.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 待倒罐单申请 页面显示
     */
    public function generateAction(){
        $params = array();
        $this->getView()->make('retank.generate',$params);
    }

    /**
     * 获取待倒罐申请单数据
     */
    public function generatejsonAction(){
        $request = $this->getRequest();
        $search = array (
            'bar_no' => $request->getPost('bar_no',''),
            'stockretankdate' => $request->getPost('stockretankdate',''),
            'stockretankdate_end' => $request->getPost('stockretankdate_end',''),
            'stockretankstatus' =>5,
            'issaveorder' =>0,
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
        );

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->searchapplyretank($search);
        echo json_encode($list);
    }

    /*
     *生成倒罐订单 页面
     */
    public function generateeditAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $action = "/retank/newJson/";
        $params = $R->getapplyretank($id);

        //获取所有商品品名
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsInfos();
        $params['goodslist']=$goods;

        //获取客服
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] =  $id;
        $params['action'] =  $action;
        $params['stockretankstatus']='1';
        $this->getView()->make('retank.retankedit',$params);
    }

    /**
     * 添加倒罐倒罐订单
     */
    public function newJsonAction()
    {

        //获取所添加的信息
        $request = $this->getRequest();
        $stockretankstatus = $request->getPost('stockretankstatus', 0);
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);
        if (count($retankdetaildata) == 0) {
            COMMON::result(300, '倒罐单明细不能为空');
            return;
        }
        $unique = array();
        foreach ($retankdetaildata as $key => $value) {
            $unique[$key] = $value['stockinno'];
            if($value['stockretank_out_no'] == $value['stockretank_in_no'])
            {
                COMMON::result(300, '移入,移出罐号不可相同');
                return;
            }
            if($value['stockretankqty'] == 0)
            {
                COMMON::result(300, '实际倒入数量不能为0');
                return;
            }
        }

        $arr = array();
        foreach ($retankdetaildata as $key => $value) {
            if($value['goodsnature']>=1 && $value['goodsnature']<=2){
                $arr[$value['storagetank_sysno']][]=$value['stockretankqty'];
            }
        }

        $newone=[];
        if(!empty($arr)){
            foreach ($arr as $key=>$value){
                $newone[$key] = array_sum($value);
            }
        }

        foreach($retankdetaildata as $key => $value){
            foreach ($newone as $k=>$v){
                if($value['stockretank_out_sysno']==$k){
                    if($value['release_num']<$v){
//                        COMMON::result(300,$value['stockretank_out_no'] .'罐申请倒出数量不能大于报关数量');
//                        return;
                        break;
                    }
                }
            }
        }

        $retank = array(
            'stockretankno' => COMMON::getCodeId('Y2'),
            'stockretankdate' => $request->getPost('stockretankdate', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'stocktype' => $request->getPost('stocktype', 0),
            'goodsname' => $retankdetaildata[0]['goodsname'],
            'ispipelineorder' =>$request->getPost('ispipelineorder',''),
            'bookingretank_sysno' => $request->getPost('bookingretank_sysno', 0),
            'bookingretankno' => $request->getPost('bookingretankno', 0),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', 0),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockretankstatus' => $stockretankstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => "=NOW()",
            'updated_at' => "=NOW()"
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //附件
        $attachment = $request->getPost('attachment',array());
        $res = $R->addRetank($retank,$retankdetaildata,$attachment);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getstockretank($params['id']);
            COMMON::result(200, '新增成功',$row);
        } else {
            COMMON::result(300, '新增失败：'.$res['msg'] );
        };
    }

    /**
     * 编辑倒罐订单 页面展示
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode','');

        if(!isset($id)) {
            $id = 0;
        }

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if(!$id){
            $action = "/retank/newJson/";
            $params =  array();
        } else {
            if($mode =='edit'){
                $action = "/retank/editJson/";
            }elseif($mode =='audit'){
                $action = "/retank/auditJson/";
            }elseif($mode =='back'){
                $action = "/retank/backJson/";
            }elseif($mode =='addattach'){
                $action = "/retank/addattachment/";
            }
            $row = $R->getstockretank($id);
            if($row['stockretankstatus']!=2 && $row['stockretankstatus']!=6 && $mode=='edit'){
                COMMON::result(300, '只能选择暂存或退回状态的数据');
                return;
            }

            $params = $row;
            //获取附件
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('retank','retank-edit',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
        }
        //获取所有商品品名
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsInfos();
        $params['goodslist']=$goods;
        //获取管线分配类型
        $params['pipelinetypelist'] = array(
            0=>array('id'=>1,'name'=>'是'),
            1=>array('id'=>2,'name'=>'否 '),
        );

        //获取客服
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] =  $id;
        $params['mode'] =  $mode;
        $params['action'] =  $action;

        $this->getView()->make('retank.retankedit',$params);
    }

    /**
     * 编辑倒罐倒罐订单
     */
    public function  editJsonAction()
    {
        //获取所添加的信息
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', 0);
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);
        if (count($retankdetaildata) == 0) {
            COMMON::result(300, '倒罐单明细不能为空');
            return;
        }

        $unique = array();
        foreach ($retankdetaildata as $key => $value) {
            $unique[$key] = $value['stockinno'];
            if($value['stockretank_out_no'] == $value['stockretank_in_no'])
            {
                COMMON::result(300, '移入,移出罐号不可相同');
                return;
                break;
            }
        }

        $retank = array(
            'stockretankdate' => $request->getPost('stockretankdate', ''),
            'stocktype' => $request->getPost('stocktype', 0),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', 0),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'updated_at' => "=NOW()"
        );

        //附件
        $attachment = $request->getPost('attachment',array());
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->updateRetank($id,$retank,$retankdetaildata,$stockretankstatus,$attachment);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getstockretank($params['id']);
            COMMON::result(200, '更新成功',$row);
        } else {
            COMMON::result(300, '更新失败：'.$res['msg'] );
        };
    }

    /**
     * 审核倒罐订单
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', '');
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);
//        var_dump($retankdetaildata);die();
        if ($stockretankstatus == 6) {
            if ($request->getPost('auditreason') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $retank = array(
            'stockretankno' =>$request->getPost('stockretankno',''),
            'stocktype' => $request->getPost('stocktype', 0),
            'stockretankstatus' => $stockretankstatus,
            'auditreason' =>$request->getPost('auditreason',''),
            'updated_at' => '=NOW()'
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->auditRetank($id,$retank,$retankdetaildata,$stockretankstatus);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getstockretank($params['id']);
            COMMON::result(200, '审核成功',$row);
        } else {
            COMMON::result(300, '审核失败：'.$res['msg'] );
        };
    }

    /*
     * 作废倒罐订单
     */
    public function backjsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockretankstatus = $request->getPost('stockretankstatus', '');
        $bookingretankno=$request->getPost('bookingretankno', '');
        $retankdetaildata = $request->getPost('retankdetaildata','');
        $retankdetaildata = json_decode($retankdetaildata, true);

        $retank = array(
            'bookingretank_sysno' => $request->getPost('bookingretank_sysno', ''),
            'stocktype' => $request->getPost('stocktype', 0),
            'stockretankstatus' => $stockretankstatus,
            'updated_at' => '=NOW()'
        );

        if ($stockretankstatus == 5) {
            if ($request->getPost('abandonreason') == '') {
                COMMON::result(300, '作废备注不能为空');
                return;
            }else{
                $retank['abandonreason'] = $request->getPost('abandonreason','');
            }
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $R->backRetank($id,$retank,$retankdetaildata,$bookingretankno);
        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $row = $R->getstockretank($params['id']);
            COMMON::result(200, '作废成功',$row);
        } else {
            COMMON::result(300, '作废失败：'.$res['msg'] );
        };
    }

    /**
     * 删除 倒罐订单
     */
    public function deljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);
        $oneId = explode(',',$id);
        if(count($oneId)==1){
            $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            $retankdata = $R->getstockretank($id);

            if($retankdata['stockretankstatus']>=3&&$retankdata['stockretankstatus']<=5){//
                COMMON::result(300,'只有暂存或退回的倒罐单才能删除');
                return ;
            }

            if($R->delRetank($id)){
                COMMON::result(200,'删除成功');
            }else{
                COMMON::result(300,'删除失败');
                return false;
            }
        }else{
            COMMON::result('300','请选择一条数据');
            return false;
        }
    }

    /*
     * 查看倒罐订单单据
     * */
    function lookretankAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode',0);
        if(!isset($id)) {
            $id = 0;
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $R->getstockretank($id);
        $data=$R->getRetankdetailById($id);
        $str = '';
        foreach ($data as $key=>$val){
            $params['shipname']=$val['shipname'];
            $params['stockretank_out_no'] = $val['stockretank_out_no'];
            $params['stockretank_in_no'] = $val['stockretank_in_no'];
            $str = $str.$val['stockretank_out_no'].'到'.$val['stockretank_in_no'].';';
            $params['stockretank_no'] = $str;
            $params['stockretankqty'] += $val['stockretankqty'];
            $params['memo']=$val['memo'];
        }

        //获取附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach1 = $A->getAttachByMAS('retank','retank-edit',$id);
        if( is_array($attach1) && count($attach1)){
            $files1 = array();
            foreach ($attach1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['attach1']  =  join(',',$files1);
        }

        //获取客服
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        //获取所有商品品名
        $G=new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods=$G->getGoodsInfos();
        $params['goodslist']=$goods;
        $params['id'] =  $id;
        $params['mode'] =  $mode;
        $this->getView()->make('retank.retankedit',$params);
    }

    /*
    * 编辑倒罐订单 添加明细列表 页面
    * */
    function generatedetaileditAction(){
        $request = $this->getRequest();
        $params = $request->getPost('selectedDatasArray',array());
        $params['handlestatus'] = $request->getParam('handlestatus','');
        $params['gsysno'] = $request->getParam('gsysno');
        $gsysno = $params['gsysno'];
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        //入库商品罐信息
        $stockretank_in_sysno = $params['stockretank_in_sysno'];
        $retank_base = $R->getBaseViceRetank( $stockretank_in_sysno,$gsysno);
        $params['sysno'] = $retank_base['sysno'];
//        $params['qualityname'] = $retank_base['qualityname'];
        $params['actualcapacity'] = $retank_base['actualcapacity'];
        $params['tank_stockqty2']=$retank_base['tank_stockqty'];
        $params['goods_quality_sysno'] = $retank_base['goods_quality_sysno'];
        $params['goods_sysno'] = $retank_base['goods_sysno'];

        //获取报关量
        $S = new StockshipinModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $release_num = $S ->getStockshipinById($params['stockin_sysno']);
        $params['release_num'] = $release_num['release_num'];

        $this->getView()->make('retank.retankadddetail',$params);
    }

    /**
     * 获取倒罐订单详情数据
     */
    public function adddetailjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        if(!isset($id)) {
            $id = 0;
        }

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        // 获取 倒罐信息的详情信息单
        $list = $R->getRetankdetailById($id);
        echo json_encode($list);
    }

    /**
     * 倒罐订单列表
     */
    public function listAction()
    {
        $params = array();
        $this->getView()->make('retank.list',$params);
    }

    /**
     * 获取倒罐订单列表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $search = array (
            'bar_no' => $request->getPost('bar_no',''),
            'stockretankdate' => $request->getPost('stockretankdate',''),
            'stockretankdate_end' => $request->getPost('stockretankdate_end',''),
            'stockretankstatus' => $request->getPost('stockretankstatus',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
        );
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->searchstockretank($search);
       echo json_encode($list);
    }

    /*
     * 添加附件
     */
    public function addattachmentAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');

        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $attach =  $request->getPost('attachment',array());

        if(count($attach) > 0){
            $res =  $A->addAttachModelSysno($id,$attach);
            if($res){
                COMMON::result(200,'添加附件成功');
            }else{
                COMMON::result(300,'添加附件失败');
                return;
            }
        }else{
            COMMON::result(300,'请添加附件再上传');
        }
    }

    /**
     *获取移出罐库存记录  根据品名和客户名
     */
    public function getRetankStockJsonAction(){
        $request = $this->getRequest();
        $goods_sysno = $request->getParam('goods_sysno');
        $customer_sysno=$request->getParam('customer_sysno');
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params=$R->getRetankStockInfo($goods_sysno,$customer_sysno);
        foreach ($params as $key=>$val){
            $params[$key]['stockin_no'] = $val['firstfrom_no'];
            $params[$key]['tank_stockqty']=$val['stockqty'];
            $params[$key]['stockin_sysno']=$val['firstfrom_sysno'];
            unset( $params[$key]['firstfrom_no']);
            unset( $params[$key]['stockqty']);
            unset($params[$key]['firstfrom_sysno']);
        }
        echo json_encode($params);
    }

    /**
     *获取移出罐库存记录  根据品名和客户名
     */
    public function getRetankintsStockJsonAction(){
        $request = $this->getRequest();
        $goods_sysno = $request->getParam('goods_sysno');
        $customer_sysno=$request->getParam('customer_sysno');
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params=$R->getRetankintsStockInfo($goods_sysno,$customer_sysno);
        echo json_encode($params);
    }

    /*
     * 获取移入储罐罐容信息
     * */
    public function getStocklistJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('goods_sysno');
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $R->getStockViceRetankInfo($id);
//        var_dump($params);die();

        echo json_encode($params);
    }

    /**
     * 获取 移入罐号查询
     */
    public function datailinAction()
    {
        $request = $this->getRequest();
        $pageSize = $request->getPost( 'pageSize','20');
        $pageCurrent = $request->getPost('pageCurrent', '1');
        $search = array(
            'storagetankname' => $request->getPost('storagetankname', ''),
            'customername' => $request->getPost('customername', ''),
            'pageSize'=>$pageSize,
            'pageCurrent'=>$pageCurrent
        );

        $capacity = new CapacityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $capacity->getCapacity($search,$pageSize,$pageCurrent);

        echo json_encode($params);
    }

    /*
     * EXCEL导出倒罐订单
     */
    public function excelAction(){
        $request = $this->getRequest();
        $search = array (
            'bar_no' => $request->getPost('retankno',''),
            'stockretankdate' => $request->getPost('begin_time',''),
            'stockretankdate_end' => $request->getPost('end_time',''),
            'stockretankstatus' => $request->getPost('retankstatus',''),
            'page' => false,
        );

        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->searchstockretank($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("倒罐列表")
            ->setSubject("列表")
            ->setDescription("倒罐列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '倒罐单号'),
            array('B1:B1', 'B1', '005E9CD3', '倒罐日期'),
            array('C1:C1', 'C1', '0094CE58', '品名'),
            array('D1:D1', 'D1', '005E9CD3', '计量单位'),
            array('E1:E1', 'E1', '0094CE58', '倒罐总量'),
            array('F1:F1', 'F1', '0094CE58', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('倒罐列表');

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
            $item['unitname']='吨';
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockretankno'];
                        break;
                    case 1:
                        $value = $item['stockretankdate'];
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
                        $value = $item['unitname'];
                        break;
                    case 4:
                        $value = $item['stockretankqty'];
                        break;
                    case 5:
                        if ($item['stockretankstatus']==2) {
                            $value = "暂存";
                        }else if ($item['stockretankstatus']==3) {
                            $value = "待审核";
                        }else if ($item['stockretankstatus']==4) {
                            $value = "已审核";
                        }else if ($item['stockretankstatus']==5) {
                            $value = "作废";
                        }else if ($item['stockretankstatus']==6) {
                            $value = "退回";
                        }else{
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $time = date('Y-m-d H:m:s',time());
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="倒罐列表_'.$time.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}