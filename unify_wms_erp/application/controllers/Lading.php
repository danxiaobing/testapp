<?php

/**
 * Created by phpstorm
 * User: danxiaobing
 * Date: 2017 九月 04
 * Time: 15:29
 */

class LadingController extends Yaf_Controller_Abstract
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

    public function indexAction()
    {
        $params=array();
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];

        $this->getView()->make('lading.index',$params);
	}

    public function listJsonAction()
    {
        $request = $this -> getRequest();
        $enddate = $request->getPost('enddate', date('Y-m-d'));// 开始时间
        $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $shipname = $request->getPost('shipname', '');
        $search = array(
            'startdate'=>$request->getPost('startdate', date('Y-m-d', strtotime('-1 months'))),
            'enddate'=>date('Y-m-d', strtotime('+2 days', strtotime($enddate))),
            'customer_sysno'=>$request->getPost('customer_sysno', ''),
            'coststatus'=>$request->getPost('coststatus', ''),
            'shipname' => $shipname,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $result = $introduceInstance -> searchLadingIntroduce($search);
        if (isset($result['list'])){
            foreach ($result['list'] as $k=>$v){
                if($v['stockintype'] == 1){
                    $result['list'][$k]['shipname'] = $v['shipname'];
                }elseif ($v['stockintype'] == 2){
                    $result['list'][$k]['shipname'] = '槽车入库';
                }elseif ($v['stockintype'] == 3){
                    $result['list'][$k]['shipname'] = '管输';
                }
            }
        }
        echo json_encode($result);
	}

	public function detailAction(){
        $request = $this -> getRequest();
        $cost_sysno = $request -> getPost('cost_sysno', '');
        $params['cost_sysno'] = $cost_sysno;
        $this->getView()->make('lading.detail',$params);
    }

    public function detailJsonAction(){
        $request = $this -> getRequest();
        $cost_sysno = $request -> getParam('cost_sysno', '');
        $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $result = $introduceInstance -> searchLadingDetail($cost_sysno);
        foreach ($result as $key => $v){
            $result[$key]['costdate'] = date('Y-m-d',strtotime( '-1 day',strtotime($v['costdate'])));
            if($v['stockintype'] == 1){
                $result[$key]['shipname'] = $v['shipname'];
            }elseif ($v['stockintype'] == 2){
                $result[$key]['shipname'] = '槽车入库';
            }elseif ($v['stockintype'] == 3){
                $result[$key]['shipname'] = '管输';
            }
        }
        echo json_encode($result);
    }

    public function editdetailAction(){
        $request = $this -> getRequest();
        $params['sysno'] = $request -> getParam('sysno', '');
        $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $params = $introduceInstance -> searchLadingDetailBySysno($params['sysno']);
        $this->getView()->make('lading.editdetail',$params);
    }

    public function editDetailFinancecostAction(){
        $request = $this->getRequest();
        $data=$request->getPost('data', '0');
        $F=new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $res=$F->updatedetailFinancecost($data);
        if($res){
            COMMON::result('200');
        }else{
            COMMON::result('300');
        }
    }

	public function countIntroduceAction(){
        $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $res = $introduceInstance -> batchRunIntroduce();
        echo $res;die;
    }

    public function excelAction()
    {
        $request=$this->getRequest();
        $enddate = $request->getPost('enddate', date('Y-m-d'));
        $params=array(
            'startdate'=>$request->getPost('startdate',date('Y-m-d',strtotime('-1 months'))),
            'enddate'=>date('Y-m-d',strtotime('+1 days',strtotime($enddate))),
            'customer_sysno'=>$request->getPost('customer_sysno',''),
            'introductionstatus'=> [4,5],
            'page' => false,
        );

        $introduceInstance=new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $resultdata = $introduceInstance -> searchLadingIntroduce($params);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel=new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("提单费用单")
            ->setSubject("提单费用单")
            ->setDescription("提单费用单");

        $mainTitle=array(
            array('A1:A1', 'A1', '0094CE58', '开单公司'),
            array('B1:B1', 'B1', '005E9CD3', '转出方'),
            array('C1:C1', 'C1', '005E9CD3', '转入方'),
            array('D1:D1', 'D1', '005E9CD3', '费用承担方'),
            array('E1:E1', 'E1', '0094CE58', '提货开始日期'),
            array('F1:F1', 'F1', '003376B3', '提货结束日期'),
            array('G1:G1', 'G1', '003376B3', '计费日期'),
            array('H1:H1', 'H1', '003376B3', '船名'),
            array('I1:I1', 'I1', '003376B3', '计量单位'),
            array('J1:J1', 'J1', '003376B3', '提单数量'),
            array('K1:K1', 'K1', '003376B3', '已提数量'),
            array('L1:L1', 'L1', '003376B3', '结存量'),
            array('M1:M1', 'M1', '003376B3', '实际金额（元）'),
        );

        $objActSheet=$objPHPExcel->getActiveSheet();
        $objActSheet->setTitle("提单费用单");

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F','G','H','I','J','K','L','M','N', 'O');

        foreach($resultdata['list'] as $item)
        {
            $line++;
            if ($item['stockintype'] == 2){
                $item['shipname'] = '槽车入库';
            }elseif ($item['stockintype'] == 3){
                $item['shipname'] = '管输';
            }
            for($i=0;$i<count($subtitle);$i++)
            {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['first_customername'];
                        break;
                    case 1:
                        $value = $item['sale_customername'];
                        break;
                    case 2:
                        $value = $item['buy_customername'];
                        break;
                    case 3:
                        $value = $item['customer_name'];
                        break;
                    case 4:
                        $value = $item['receivestart'];
                        break;
                    case 5:
                        $value = $item['receiveend'];
                        break;
                    case 6:
                        $value = $item['created_at'];
                        break;
                    case 7:
                        $value = $item['shipname'];
                        break;
                    case 8:
                        $value = $item['unitname'];
                        break;
                    case 9:
                        $value = $item['takegoodsnum'];
                        break;
                    case 10:
                        $value = $item['takegoodsqty'];
                        break;
                    case 11:
                        $value = $item['costqty'];
                        break;
                    case 12:
                        $value = $item['sumpricre'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="对帐单列表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
	}


	public function excelDetailAction(){
        $request = $this -> getRequest();
        $cost_sysno = $request -> getParam('cost_sysno', '');
        $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $list = $introduceInstance -> searchLadingDetail($cost_sysno);
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询提单费用")
            ->setSubject("列表")
            ->setDescription("提单费用明细列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '开单公司'),
            array('B1:B1', 'B1', '005E9CD3', '转出方'),
            array('C1:C1', 'C1', '005E9CD3', '转入方'),
            array('D1:D1', 'D1', '0094CE58', '费用承担方'),
            array('E1:E1', 'E1', '0094CE58', '提货开始日期'),
            array('F1:F1', 'F1', '0094CE58', '提货结束日期'),
            array('G1:G1', 'G1', '0094CE58', '计费日期'),
            array('H1:H1', 'H1', '0094CE58', '计量单位'),
            array('I1:I1', 'I1', '0094CE58', '船名'),
            array('J1:J1', 'J1', '0094CE58', '提单数量'),
            array('K1:K1', 'K1', '003376B3', '已提数量'),
            array('L1:L1', 'L1', '003376B3', '结存量'),
            array('M1:M1', 'M1', '003376B3', '超期吨数'),
            array('N1:N1', 'N1', '003376B3', '单价（吨/天/元）'),
            array('O1:O1', 'O1', '003376B3', '实际金额'),
            array('P1:P1', 'P1', '003376B3', '预计金额（元）'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('提单费用明细列表');

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

        foreach ($list as $item) {
            $line++;
            if ($item['stockintype'] == 2){
                $item['shipname'] = '槽车入库';
            }elseif ($item['stockintype'] == 3){
                $item['shipname'] = '管输';
            }
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['first_customername'];
                        break;
                    case 1:
                        $value = $item['sale_customername'];
                        break;
                    case 2:
                        $value = $item['buy_customername'];
                        break;
                    case 3:
                        $value = $item['customer_name'];
                        break;
                    case 4:
                        $value = $item['receivestart'];
                        break;
                    case 5:
                        $value = $item['receiveend'];
                        break;
                    case 6:
                        $value = $item['costdate'];
                        break;
                    case 7:
                        $value = $item['unitname'] ? $item['unitname'] : '吨';
                        break;
                    case 8:
                        $value = $item['shipname'];
                        break;
                    case 9:
                        $value = $item['takegoodsnum'];
                        break;
                    case 10:
                        $value = $item['takegoodsqty'];
                        break;
                    case 11:
                        $value = $item['costqty'];
                        break;
                    case 12:
                        $value = $item['costqty'];
                        break;
                    case 13:
                        $value = $item['unitprice'];
                        break;
                    case 14:
                        $value = $item['totalprice'];
                        break;
                    case 15:
                        $value = $item['oldtotalprice'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="提单费用明细.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}