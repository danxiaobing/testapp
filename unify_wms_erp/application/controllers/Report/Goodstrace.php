<?php

/**
 * Class Report_GoodstraceController
 * User: danxiaobing
 * Date: 2017/10/10
 * Time: 16:22
 */
class Report_GoodstraceController extends Yaf_Controller_Abstract
{
    public function init() {
        # parent::init();
    }

    /**
     * 货物进出货追溯列表
     * @return string
     */
    public function listAction() {
        $params = array( );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] = $list['list'];
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goods'] = $goods->getGoodsInfo();
        $this->getView()->make('reportgoodstrace.list',$params);
    }

    public function listJsonAction(){
        $request=$this->getRequest();
        $search = array(
            'startTime' => $request->getPost('startTime',date('Y-m-d')),
            'endTime' =>  $request->getPost('endTime',date('Y-m-d')),
            'customer_sysno' => $request->getPost('customername',''),
            'goods_sysno' =>  $request->getPost('goodsname',''),
            'stockinno' =>  $request->getPost('stockinno',''),
            'shipname' =>  $request->getPost('shipname',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $goodstraceInstace->getList($search);
        echo json_encode($list);
    }

    public function detailAction() {
        $request=$this->getRequest();
        $params['sysno'] = $request->getParam('sysno', '');
        $this->getView()->make('reportgoodstrace.detail', $params);
    }

    public function detailJsonAction() {
        $request=$this->getRequest();
        $sysno = $request->getParam('sysno','');
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail  = $goodstraceInstace->getDetail($sysno);
//        print_r($detail);die;
        echo  json_encode($detail);
    }

    public function getOutJsonAction(){
        $request=$this->getRequest();
        $sysno = $request->getParam('sysno','');
        $takegoodsno = $request->getPost('takegoodsno','');
        $customername = $request->getPost('customername','');
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail  = $goodstraceInstace->getOutDetail($sysno);
        //类型:1船入库2车入库3船出库4车出库5货转入（正）6货转出（负）7倒罐入（正）8倒罐出（负）9盘点(储罐)10盘点(客户) 11管线入库 12 管线出库 13提单入 14提单出 15超期损耗(批量脚本) 16提单撤销入 17 提单撤销出
        $stockqty = 0;
        foreach ($detail as $key => $value){
            if(in_array($value['doc_type'], [1,2,5,6,7,8,11,13,16])){
                $detail[$key]['instockqty'] = $value['beqty'];
                $detail[$key]['tuihuo'] = '--';
                $detail[$key]['beqty'] = '--';
            }elseif (in_array($value['doc_type'], [26])){
                $detail[$key]['tuihuo'] = $value['beqty'];
                $detail[$key]['beqty'] = '--';
            }else{
                $detail[$key]['tuihuo'] = '--';
                $detail[$key]['instockqty'] = '--';
            }

            //传入库结存
            $yuanqty = isset($detail[$key -1]) ? 0 : $detail[$key -1]['beqty'];
            if($value['doc_type'] == 1){
                $stockqty += $yuanqty + $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
            }elseif ($value['doc_type'] == 2){ // 车入库结存
                $stockqty += $yuanqty + $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '槽车';
            }elseif ($value['doc_type'] == 5){ // 货转结存
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '货权转移';
            }elseif ($value['doc_type'] == 6){ // 货转结存
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '货权转移';
            }elseif($value['doc_type'] == 11){
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '管输';
            }elseif($value['doc_type'] == 12){
                $detail[$key]['stockqty'] = '--';
                $detail[$key]['shipname'] = '管输';
            }else{
                $detail[$key]['stockqty'] = '--';
            }
        }
//        echo count($detail);die;
        if(!empty($detail)) {
            //根据stock_sysno 排序
            foreach ($detail as $key => $item) {
                $detail[$key]['sort_no'] = $key + 1;
            }

            foreach ($detail as $k => $v) {
                if (in_array($v['doc_type'], [1, 2])) {
                    $detail[$k]['pid'] = 0;
                } elseif ($v['doc_type'] == 5) {
                    foreach ($detail as $m) {
                        if ($v['father_stock_sysno'] == $m['stock_sysno']) {
                            $detail[$k]['pid'] = $m['sort_no'];
                            break;
                        }
                    }
                } elseif ($v['doc_type'] == 6) {
                    foreach ($detail as $m) {
                        if ($v['stock_sysno'] == $m['stock_sysno']) {
                            $detail[$k]['pid'] = $m['sort_no'];
                            break;
                        }
                    }
                } else {
                    $detail[$k]['pid'] = $v['sort_no'];
                }
            }

            for ($j = 0; $j < count($detail); $j++) {
                if ($detail[$j]['pid'] == 0) {
                    $detail[$j]['sort'] = 10000000 - $detail[$j]['sort_no'] * 1000000;
                } else {
                    $self = $detail[$j]['pid'] - 1;
                    if ($self == 0) {
                        $detail[$j]['sort'] = $detail[$self]['sort'] - $detail[$j]['sort_no'] * 1000;
                    } else {
                        $detail[$j]['sort'] = $detail[$self]['sort'] - $detail[$j]['sort_no'];
                    }
                }
                $newarr[] = $detail[$j];
            }
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field' => 'sort',       //排序字段
            );
            $arrSort = array();
            foreach ($detail AS $uniqid => $row) {
                foreach ($row AS $key => $value) {
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            if ($sort['direction']) {
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $detail);
            }
        }
        $searachData = array();
        if($takegoodsno || $customername) {
            foreach ($detail as $val) {
                if ($takegoodsno) {
                    if (stripos($val['takegoodsno'], $takegoodsno) >= 0 && is_numeric(stripos($val['takegoodsno'], $takegoodsno))) {
                        $searachData[] = $val;
                    }
                }
                if ($customername) {
                    if (stripos($val['customername'], $customername) >= 0 && is_numeric(stripos($val['customername'], $customername))) {
                        $searachData[] = $val;
                    }
                }
            }
        }else{
            $searachData = $detail;
        }

        echo  json_encode($searachData);
    }

    /**
     * 导出
     */
    public function exportAction(){
        $request=$this->getRequest();
        $search = array(
            'startTime' => $request->getPost('startTime',date('Y-m-d')),
            'endTime' =>  $request->getPost('endTime',date('Y-m-d')),
            'customer_sysno' => $request->getPost('customername',''),
            'goods_sysno' =>  $request->getPost('goodsname',''),
            'stockinno' =>  $request->getPost('stockinno',''),
            'shipname' =>  $request->getPost('shipname',''),
            'page' => false,
            'pageSize' => false,
        );
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $goodstraceInstace->getList($search);
        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=货物进去追溯表.xls");
        echo
            iconv("UTF-8","GBK//IGNORE", "进货单号")."\t".
            iconv("UTF-8", "GBK//IGNORE", "类型")."\t".
            iconv("UTF-8", "GBK//IGNORE","入库日期")."\t".
            iconv("UTF-8", "GBK//IGNORE", "品名")."\t".
            iconv("UTF-8", "GBK//IGNORE", "客户")."\t".
            iconv("UTF-8", "GBK//IGNORE", "进货车船名")."\t".
            iconv("UTF-8", "GBK//IGNORE", "提单量（吨）")."\t".
            iconv("UTF-8", "GBK//IGNORE", "商检量（吨）")."\t";


        foreach ((array)$list['list'] as $key=>$item) {
            //入库单类型：1船入库2车入库3管线入4靠泊装卸入
            if($item['stockintype']==1){
                $status = '船入库';
            }elseif($item['stockintype']==2){
                $status = '车入库';
                $item['shipname'] = '槽车进货';
            }elseif($item['stockintype']==3){
                $status = '管线入';
                $item['shipname'] = '管输';
            }else if($item['stockintype']==4){
                $status = '靠泊装卸入';
                $item['shipname'] = '管输';
            }else{
                $status = '未知类型';
            }

            echo "\n";
            echo iconv("UTF-8", "GBK//IGNORE", $item['stockinno'])           //进货单号
                ."\t".iconv("UTF-8", "GBK//IGNORE", $status)           //类型
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['stockindate'])          //入库日期
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['goodsname'])     //品名
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['customername'])             //客户
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['shipname'])         //进货车船名
                ."\t".$item['takegoodsnum']                                       //提单量（吨）
                ."\t".$item['beqty'];                              //商检量（吨）

        }
    }

    public function exportDetailAction(){
        $request=$this->getRequest();
        $sysno = $request->getParam('sysno','');
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $inDataDetail  = $goodstraceInstace->getDetail($sysno);
        $goodstraceInstace = new Report_GoodstraceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail  = $goodstraceInstace->getOutDetail($sysno);
        //类型:1船入库2车入库3船出库4车出库5货转入（正）6货转出（负）7倒罐入（正）8倒罐出（负）9盘点(储罐)10盘点(客户) 11管线入库 12 管线出库 13提单入 14提单出 15超期损耗(批量脚本) 16提单撤销入 17 提单撤销出
        $stockqty = 0;
        foreach ($detail as $key => $value){
            if(in_array($value['doc_type'], [1,2,5,6,7,8,11,13,16])){
                $detail[$key]['instockqty'] = $value['beqty'];
                $detail[$key]['tuihuo'] = '--';
                $detail[$key]['beqty'] = '--';
            }elseif (in_array($value['doc_type'], [26])){
                $detail[$key]['tuihuo'] = $value['beqty'];
                $detail[$key]['beqty'] = '--';
            }else{
                $detail[$key]['tuihuo'] = '--';
                $detail[$key]['instockqty'] = '--';
            }

            //传入库结存
            $yuanqty = isset($detail[$key -1]) ? 0 : $detail[$key -1]['beqty'];
            if($value['doc_type'] == 1){
                $stockqty += $yuanqty + $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
            }elseif ($value['doc_type'] == 2){ // 车入库结存
                $stockqty += $yuanqty + $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '槽车';
            }elseif ($value['doc_type'] == 5){ // 货转结存
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '货权转移';
            }elseif ($value['doc_type'] == 6){ // 货转结存
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '货权转移';
            }elseif($value['doc_type'] == 11){
                $stockqty += $yuanqty - $value['beqty'] - $value['ullage'];
                $detail[$key]['stockqty'] = $stockqty;
                $detail[$key]['shipname'] = '管输';
            }elseif($value['doc_type'] == 12){
                $detail[$key]['stockqty'] = '--';
                $detail[$key]['shipname'] = '管输';
            }else{
                $detail[$key]['stockqty'] = '--';
            }
        }
        if(!empty($detail)) {
            //根据stock_sysno 排序
            foreach ($detail as $key => $item) {
                $detail[$key]['sort_no'] = $key + 1;
            }

            foreach ($detail as $k => $v) {
                if (in_array($v['doc_type'], [1, 2])) {
                    $detail[$k]['pid'] = 0;
                } elseif ($v['doc_type'] == 5) {
                    foreach ($detail as $m) {
                        if ($v['father_stock_sysno'] == $m['stock_sysno']) {
                            $detail[$k]['pid'] = $m['sort_no'];
                            break;
                        }
                    }
                } elseif ($v['doc_type'] == 6) {
                    foreach ($detail as $m) {
                        if ($v['stock_sysno'] == $m['stock_sysno']) {
                            $detail[$k]['pid'] = $m['sort_no'];
                            break;
                        }
                    }
                } else {
                    $detail[$k]['pid'] = $v['sort_no'];
                }
            }

            for ($j = 0; $j < count($detail); $j++) {
                if ($detail[$j]['pid'] == 0) {
                    $detail[$j]['sort'] = 10000000 - $detail[$j]['sort_no'] * 1000000;
                } else {
                    $self = $detail[$j]['pid'] - 1;
                    if ($self == 0) {
                        $detail[$j]['sort'] = $detail[$self]['sort'] - $detail[$j]['sort_no'] * 1000;
                    } else {
                        $detail[$j]['sort'] = $detail[$self]['sort'] - $detail[$j]['sort_no'];
                    }
                }
                $newarr[] = $detail[$j];
            }
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field' => 'sort',       //排序字段
            );
            $arrSort = array();
            foreach ($detail AS $uniqid => $row) {
                foreach ($row AS $key => $value) {
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            if ($sort['direction']) {
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $detail);
            }
        }
//        echo "<pre>";print_r($detail);die;
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("货物进出报表")
            ->setSubject("列表")
            ->setDescription("货物进出表明细");

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
        $line = 1;
        //基本信息
        $mainTitle = array(
            array('A1:A1', 'A'.$line, '0094CE58', '货主'),
            array('B1:B1', 'B'.$line, '005E9CD3', '进货船名'),
            array('C1:C1', 'C'.$line, '005E9CD3', '货物名称'),
            array('D1:D1', 'D'.$line, '0094CE58', '进货日期'),
            array('E1:E1', 'E'.$line, '0094CE58', '进货罐号'),
            array('F1:F1', 'F'.$line, '0094CE58', '商检岸罐量'),
            array('G1:G1', 'G'.$line, '0094CE58', '损耗量'),
            array('H1:H1', 'H'.$line, '0094CE58', '结存量'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('货物进出表明细');

        foreach ($mainTitle as $row) {
            $objActSheet->mergeCells($row[0]);
            $objActSheet->setCellValue($row[1], $row[3]);

            $objStyle = $objActSheet->getStyle($row[1]);
            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }


        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        foreach ($inDataDetail as $items) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $items['customername'];
                        break;
                    case 1:
                        if ($items['stockintype'] == '2') {
                            $value = '槽车进货';
                        } elseif ($items['stockintype'] == '3') {
                            $value = '管输';
                        } else {
                            $value = $items['shipname'];
                        }
                        break;
                    case 2:
                        $value = $items['goodsname'];
                        break;
                    case 3:
                        $value = $items['stockindate'];
                        break;
                    case 4:
                        $value = $items['storagetankname'];
                        break;
                    case 5:
                        $value = $items['instockqty'];
                        break;
                    case 6:
                        $value = $items['ullage'];
                        break;
                    case 7:
                        $value = $items['stockqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
                $objStyle = $objActSheet->getStyle($site);
                $objStyle->getAlignment()->setHorizontal("center");
                $objStyle->getAlignment()->setVertical("center");
            }
        }

        $count = count($subtitle);
        for($i=1;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($subtitle[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

//        货转信息
        $line = $line + 2;
        $tabindex = $line;
        $mainTitle2 = array(
            array('A1:A1', 'A'.$line, '0094CE58', '单号'),
            array('B1:B1', 'B'.$line, '005E9CD3', '类型'),
            array('C1:C1', 'C'.$line, '005E9CD3', '客户'),
            array('D1:D1', 'D'.$line, '0094CE58', '日期'),
            array('E1:E1', 'E'.$line, '0094CE58', '提单号'),
            array('F1:F1', 'F'.$line, '0094CE58', '车船类型'),
            array('G1:G1', 'G'.$line, '0094CE58', '车号'),
            array('H1:H1', 'H'.$line, '0094CE58', '入库量/货转量'),
            array('I1:I1', 'I'.$line, '0094CE58', '实提数量'),
            array('J1:J1', 'J'.$line, '0094CE58', '退货数量(吨)'),
            array('K1:K1', 'K'.$line, '0094CE58', '结存量（吨）'),
            array('L1:L1', 'L'.$line, '0094CE58', '损耗量（吨）'),
        );
        foreach ($mainTitle2 as $row2) {

            $objActSheet->setCellValue($row2[1], $row2[3]);
            $objStyle = $objActSheet->getStyle($row2[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }

        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
        foreach ($detail as $item) {
            $line ++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['docno'];
                        break;
                    case 1:
                        $value = self::getDocType($item['doc_type']);
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['created_at'];
                        break;
                    case 4:
                        $value = $item['takegoodsno'];
                        break;
                    case 5:
                        $value = $item['shipname'];
                        break;
                    case 6:
                        $value = $item['carid'];
                        break;
                    case 7:
                        $value = $item['instockqty'];
                        break;
                    case 8:
                        $value = $item['beqty'];
                        break;
                    case 9:
                        $value = $item['tuihuo'];
                        break;
                    case 10:
                        $value = $item['stockqty'];
                        break;
                    case 11:
                        $value = $item['ullage'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $count = count($subtitle);
        for($i=$tabindex;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($subtitle[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="货物进出表明细.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    private static function getDocType($key){
        $array =[
            '1' => '船入库',
            '2' => '车入库',
            '3' => '船出库',
            '4' => '车出库',
            '5' => '货转入',
            '6' => '货转出',
            '7' => '倒罐入',
            '8' => '倒罐出',
            '9' => '盘点(储罐)',
            '10' => '盘点(客户)',
            '11' => '管线入库',
            '12' => '管线出库',
            '13' => '提单入',
            '14' => '提单出',
            '15' => '超期损耗',
            '16' => '提单撤销入',
            '17' => '提单撤销出',
            '18' => '清库损耗',
            '19' => '补单入',
            '20' => '补单扣',
            '21' => '提单倒罐入',
            '22' => '提单倒罐出',
            '23' => '提单作废出',
            '24' => '提单作废入',
            '25' => '库存调整',
            '25' => '退货',
        ];
        return $array[$key];
    }
}
