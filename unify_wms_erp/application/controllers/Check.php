<?php

/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2016/11/23 0017
 * Time: 10:35
 */
class CheckController extends Yaf_Controller_Abstract
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

    /**
     * 显示盘点记录列表页面
     * @author Jay Xu
     */
    public function checkrecordAction()
    {
        $tank = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false
        );
        $tankdata = $tank->searchStoragetank($search);
        $params['tanklist'] = $tankdata['list'];
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('check.checkrecord', $params);
    }

    /**
     * 显示盘点报表页面
     * @author Jay Xu
     */
    public function checkreportAction()
    {
        $tank = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false
        );
        $tankdata = $tank->searchStoragetank($search);
        $params['tanklist'] = $tankdata['list'];
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('check.checkreport', $params);
    }

    /*
     * 获取盘点记录表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'checkrecorddate' => $request->getPost('checkrecorddate', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno',''),
            'storagetankname' => $request->getPost('storagetankname',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => $request->getPost('orders', ''),
        );

        $S = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchCheck($search);
        echo json_encode($list);
    }

    /*
     * 显示盘点编辑页面
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode', '');

        if (!isset($id)) {
            $id = 0;
        }

        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/check/newJson/";
            $params = array();
            $val = 3;
        } else {
            if($mode =='edit'){
                $action = "/check/editJson/";
            }elseif($mode =='audit'){
                $action = "/check/auditjson/";
            }elseif($mode =='attach'){
                $action = "/check/addattachjson/";
            }elseif($mode =='abolish'){
                $action = "/check/abolishjson/";
            }
            $params = $S->getCheckById($id);

            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach1 = $A->getAttachByMAS('check', 'receipt', $id);
            if (is_array($attach1) && count($attach1)) {
                $files1 = array();
                foreach ($attach1 as $file) {
                    $files1[] = $file['sysno'];
                }
                $params['attach1'] = join(',', $files1);
            }
        }

        $array = $S->getCheckById($id);
        if($id !='' && $mode != 'audit' && $mode != 'abolish'){
            if($array['stockcheckstatus'] != 2 && $array['stockcheckstatus'] != 6){
                COMMON::result(300, '非暂存或退回状态盘点单不允许编辑');
                return false;
            }
        }

        if ($array['storagetanknature'] == 1) {
            $array['storagetanknature'] = '内贸罐';
        } elseif ($array['storagetanknature'] == 2) {
            $array['storagetanknature'] ='外贸罐';
        } elseif ($array['storagetanknature'] == 3) {
            $array['storagetanknature'] = '保税罐';
        }

        $storagetanknature = $array['storagetanknature'];

        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'page' => false,
        );
        $list = $C->searchcustomer($search);
        $params['customerlist'] = $list['list'];

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] = $id;
        $params['mode'] = $mode;
        $params['action'] = $action;
        $params['storagetanknature'] = $storagetanknature;
        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['unitlist'] = $unit->getUnit();

        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] = $list['list'];


        $this->getView()->make('check.edit', $params);
    }

    //盘点添加
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'checkrecordno' => COMMON::getCodeId('CH'),
            'checkrecorddate' => $request->getPost('checkrecorddate', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'storagetankname' => $request->getPost('storagetankname', ''),
            'storagetanknature' => $request->getPost('storagetanknature', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'created_employee_sysno' => $request->getPost('created_employee_sysno', ''),
            'created_employeename' => $request->getPost('created_employeename', ''),
            'temperature' => $request->getPost('temperature', ''),
            'liquid' => $request->getPost('liquid', ''),
            'rulerqty' => $request->getPost('rulerqty', ''),
            'ischecked' => $request->getPost('ischecked', ''),
            'rulerdate' => $request->getPost('rulerdate', ''),
            'memo' => $request->getPost('memo', ''),
            'auditreason' => $request->getPost('auditreason', ''),
            'stockcheckstatus' => $request->getPost('stockcheckstatus',''),
            'status' => $request->getPost('status',1),
            'isdel' => $request->getPost('isdel', '0'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        if ($input['storagetanknature'] == '内贸罐') {
            $input['storagetanknature'] = 1;
        } elseif ($input['storagetanknature'] == '外贸罐') {
            $input['storagetanknature'] = 2;
        } elseif ($input['storagetanknature'] == '保税罐') {
            $input['storagetanknature'] = 3;
        }

        //附件
        $attachment = $request->getPost('attachment', array());
        $id = $S->addCheck($input,$attachment);

        if ($id) {
            $row = $S->getCheckById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    //添加附件
    public function addattachjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $request->getPost('attachment', array());

        if (count($attach) > 0) {
            $res = $A->addAttachModelSysno($id, $attach);
            if (!$res) {
                COMMON::result(300, '添加附件失败');
                return;
            } else {
                COMMON::result(200, '添加附件成功');
                return;
            }
        }else{
            COMMON::result(300, '请先上传附件');
            return;
        }
    }

    //盘点更新
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'checkrecorddate' => $request->getPost('checkrecorddate', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'storagetankname' => $request->getPost('storagetankname', ''),
            'storagetanknature' => $request->getPost('storagetanknature', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'created_employee_sysno' => $request->getPost('created_employee_sysno', ''),
            'created_employeename' => $request->getPost('created_employeename', ''),
            'temperature' => $request->getPost('temperature', ''),
            'liquid' => $request->getPost('liquid', ''),
            'rulerqty' => $request->getPost('rulerqty', ''),
            'ischecked' => $request->getPost('ischecked', ''),
            'rulerdate' => $request->getPost('rulerdate', ''),
            'memo' => $request->getPost('memo',''),
            'auditreason' => $request->getPost('auditreason', ''),
            'stockcheckstatus' => $request->getPost('stockcheckstatus',''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'version' => $request->getPost('version', '1'),
            'updated_at' => '=NOW()'
        );

        if ($input['storagetanknature'] == '内贸罐') {
            $input['storagetanknature'] = 1;
        } elseif ($input['storagetanknature'] == '外贸罐') {
            $input['storagetanknature'] = 2;
        } elseif ($input['storagetanknature'] == '保税罐') {
            $input['storagetanknature'] = 3;
        }
        //附件
        $attachment = $request->getPost('attachment', array());
        $res = $S->updateCheck($id, $input,$request->getPost('stockcheckstatus', ''), $attachment);
        if ($res['statusCode'] == 200){
            COMMON::result(200, '更新成功！');
        }else{
            COMMON::result(300, '更新失败!');
        }
    }

    //盘点删除
    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $S = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $input = array(
            'isdel' => 1
        );

        $array = $S->getCheckById($id);

        if ($array['stockcheckstatus'] != 2 && $array['stockcheckstatus'] != 6) {
            COMMON::result(300, '非暂存或退回状态盘点单不允许删除');
            return false;
        }

        if ($S->delCheck($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /**
     * 审核通过
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockcheckstatus = $request->getPost('stockcheckstatus', '');
        $attachment = $request->getPost('attachment',array());

        $input = array(
            'checkrecordno'=>$request->getPost('checkrecordno',''),
            'storagetank_sysno'=>$request->getPost('storagetank_sysno',''),
            'stockcheckstatus' => $stockcheckstatus,
            'auditreason'=> $request->getPost('auditreason', ''),
            'updated_at' => '=NOW()'
        );

        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $S->auditcheck($id, $input,$stockcheckstatus);
        if ($res['statusCode'] == 200){
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            if(count($attachment)>0){
                $res = $A->addAttachModelSysno($id,$attachment);
                if(!$res){
                    COMMON::result(300, '添加附件失败!' );
                    return ;
                }
            }
            COMMON::result(200, '审核成功!');
        }else{
            COMMON::result(300, '审核失败!');
        }
    }

    /*
     * 盘点作废
     */
    public function abolishjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id','0');
        $stockcheckstatus = $request->getPost('stockcheckstatus', '');
        $attachment = $request->getPost('attachment',array());
        $input = array(
            'checkrecordno'=>$request->getPost('checkrecordno',''),
            'storagetank_sysno' =>$request->getPost('storagetank_sysno',''),
            'stockcheckstatus' => $stockcheckstatus,
            'auditreason'=> $request->getPost('auditreason', ''),
            'updated_at' => '=NOW()'
        );

        $C = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $checkinfo = $C->getCheckById($id);

        if ($checkinfo['stockcheckstatus'] != 4) {
            COMMON::result(300, '已审核的单据才能作废');
            return false;
        }

        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $S->abolishcheck($id, $input);
        if ($res['statusCode'] == 200){
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            if(count($attachment)>0){
                $res = $A->addAttachModelSysno($id,$attachment);
                if(!$res){
                    COMMON::result(300, '添加附件失败!' );
                    return ;
                }
            }
            COMMON::result(200, '作废成功!');
        }else{
            COMMON::result(300, '作废失败!');
        }

    }

    /*
     * 盘点查看
     */
    public function checkSeeAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $val = $request->getParam('val', '');
        $mode = $request->getParam('mode', '');

        if (!isset($id)) {
            $id = 0;
        }

        $S = new CheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/check/newJson/";
            $params = array();
            $val = 3;
        } else {
            $action = "/check/editJson/";
            $params = $S->getCheckById($id);

            //添加附件的显示
                        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                        $attach1 = $A->getAttachByMAS('check','receipt',$id);
                        if( is_array($attach1) && count($attach1)){
                            $files1 = array();
                            foreach ($attach1 as $file){
                                $files1[] = $file['sysno'];
                            }
                            $params['attach1']  =  join(',',$files1);
                        }
            #var_dump($params['attach1']);exit();

        }

        $array = $S->getCheckById($id);
        if ($array['storagetanknature'] == 1) {
            $array['storagetanknature'] = '内贸罐';
        } elseif ($array['storagetanknature'] == 2) {
            $array['storagetanknature'] ='外贸罐';
        } elseif ($array['storagetanknature'] == 3) {
            $array['storagetanknature'] = '保税罐';
        }

        $storagetanknature = $array['storagetanknature'];

        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'page' => false,
        );
        $list = $C->searchcustomer($search);
        $params['customerlist'] = $list['list'];
        $params['checkstatusnamelist'] = array(
            0 => array('id' => "", 'name' => '新建'),
            1 => array('id' => 1, 'name' => '新建'),
            2 => array('id' => 2, 'name' => '暂存'),
            3 => array('id' => 3, 'name' => '已提交'),
            4 => array('id' => 4, 'name' => '已审核'),
            5 => array('id' => 5, 'name' => '已完成'),
            6 => array('id' => 6, 'name' => '作废'),
        );

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] = $id;
        $params['val'] = $val;
        $params['mode'] = $mode;
        $params['action'] = $action;
        $params['storagetanknature'] = $storagetanknature;

        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['unitlist'] = $unit->getUnit();

        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] = $list['list'];


        //附件
        $attachment = $request->getPost('attachment',array());

        $this->getView()->make('check.recordsee', $params);
    }

    /*
    * 获取盘点报表数据
    */
    public function checkreportJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'created_at' => $request->getPost('created_at', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $S = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchCheckreport($search);
        echo json_encode($list);
    }

    public function recordgoodsJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        if($id){
            $C = new CheckModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $data = $C->getStoragetankInfo($id);
                if ($data['storagetanknature'] == 1) {
                    $data['storagetanknature'] = '内贸罐';
                } elseif ($data['storagetanknature'] == 2) {
                    $data['storagetanknature'] ='外贸罐';
                } elseif ($data['storagetanknature'] == 3) {
                    $data['storagetanknature']  = '保税罐';
                }
            echo json_encode($data);
        }else{
            $data = array();
            echo json_encode($data);
        }

    }

    /*
     * 盘点报表查看
     */
    public function seereportAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $params['id'] = $id;
        $this->getView()->make('check.reportsee', $params);
    }

    /*
     * 盘点报表查看数据
     */
    public function seereportJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $C = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->getReportdetail($id);
        echo json_encode($list);
    }

    /*
     * 盘点表
     */
    public function reportAction()
    {
        $params = array();
        $this->getView()->make('check.reportlist',$params);
    }

    /*
     * 盘点表数据
     */
    public function reportJsonAction()
    {
        $request = $this->getRequest();
        $C = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->getcheckdata();
        echo json_encode($list);
    }

    /*
     * 生成盘点表
     */
    public function generate_checkAction(){
        $request = $this->getRequest();
        $data = $request->getPost('data',array());
        foreach ($data as $datum){
            $stro[] = $datum['sysno'];
        }
        $C = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $ids = implode(',',$stro);
        $detaildata = $C->getcheckdata2($ids);
        $checkqty = 0;
        foreach ($detaildata as $item){
            $checkqty += $item['beqty'] - $item['tank_stockqty'];
        }
        $input = array(
            'checkreportno' => COMMON::getCodeId('PD'),
            'checknum' => $checkqty,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        $id = $C->generate_check($input,$detaildata);
        if ($id) {
            $row = $C->getCheckreportById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    //盘点单EXCEL导出
    public function checktoexcelAction()
    {
        $request = $this->getRequest();
        $params = array(
            'created_at' => $request->getPost('created_at', ''),
            'updated_at' => $request->getPost('updated_at', ''),
            'bar_stockcheckstatus' => $request->getPost('bar_stockcheckstatus', '-100'),
            'page' => false,
        );

        $C = new CheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchCheck($params);

        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("盘点单列表")
            ->setSubject("盘点单列表")
            ->setDescription("盘点单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '盘点单号'),
            array('B1:B1', 'B1', '005E9CD3', '盘点日期'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '0094CE58', '品名'),
            array('E1:E1', 'E1', '0094CE58', '计量单位'),
            array('F1:F1', 'F1', '0094CE58', '剩余数量'),
            array('G1:G1', 'G1', '0094CE58', '盘点数量'),
            array('H1:H1', 'H1', '003376B3', '损耗量'),
            array('I1:I1', 'I1', '003376B3', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('盘点单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockcheckno'];
                        break;
                    case 1:
                        $value = $item['stockcheckdate'];
                        break;
                    case 2:
                        $value= $item['customername'];
                        break;
                    case 3:
                        $value = $item['goodsname'];
                        break;
                    case 4:
                        $value = $item['unitname'];
                        break;
                    case 5:
                        $value = $item['tank_stockqty'];
                        break;
                    case 6:
                        $value = $item['stockcheckqty'];
                        break;
                    case 7:
                        $value = $item['checkqty'];
                        break;
                    case 8:
                        switch ($item['stockcheckstatus']) {
                            case "1":
                                $value = "新建";
                                break;
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "待审核";
                                break;
                            case "4":
                                $value = "已审核";
                                break;
                            case "5":
                                $value = "作废";
                                break;
                            case "6":
                                $value = "退回";
                                break;
                        };
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="盘点单列表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}