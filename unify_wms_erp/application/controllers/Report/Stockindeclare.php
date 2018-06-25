<?php

class Report_StockindeclareController extends Yaf_Controller_Abstract
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
     * 报关报表
     * @author zhaoshiyu
     */
    public function ListAction(){
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'page' => false,
            'isdel'=>0
        );
        $params['customers']=$C->searchCustomer($search);

        $this->getView()->make('stockindeclare.list',$params);
    }

    /**
     * 报关报表Json数据
     * @author zhaoshiyu
     */
    public function ListJsonAction(){
        $request = $this->getRequest();

        $startdate = $request->getPost('startdate',date('Y-m-d',strtotime('-1 months')));
        $enddate = $request->getPost('enddate',date('Y-m-d'));
        $customer_sysno = $request->getPost('sysno','');
        $search = array(
        	'customer_sysno' => $customer_sysno,
            'startdate'=>date('Y-m-d',strtotime($startdate)),
            'enddate'=>date('Y-m-d',strtotime($enddate)),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );

        $R = new Report_StockindeclareModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);
        echo json_encode($result);
    }

    public function dbtoexcelAction()
	{
		$request = $this->getRequest();
        $startdate = $request->getPost('startdate',date('Y-m-d',strtotime('-1 months')));
        $enddate = $request->getPost('enddate',date('Y-m-d'));
        $customer_sysno = $request->getPost('customer_sysno','');
        $search = array(
        	'customer_sysno' => $customer_sysno,
            'startdate'=>date('Y-m-d',strtotime($startdate)),
            'enddate'=>date('Y-m-d',strtotime($enddate)),
            'page' => false,
        );
        $R = new Report_StockindeclareModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);
		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("报关量报表")
			->setSubject("报关量报表")
			->setDescription("报关量报表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '罐号'),
			array('B1:B1', 'B1', '005E9CD3', '客户'),
			array('C1:C1', 'C1', '005E9CD3', '品名'),
			array('D1:D1', 'D1', '0094CE58', '进货时间'),
			array('E1:E1', 'E1', '0094CE58', '进货船名'),
			array('F1:F1', 'F1', '0094CE58', '提单量'),
			array('G1:G1', 'G1', '0094CE58', '商检量'),
			array('H1:H1', 'H1', '003376B3', '总报关量'),
			array('I1:I1', 'I1', '003376B3', '客户报关可发量'),
			array('J1:J1', 'J1', '003376B3', '储罐报关可发量'),
			array('K1:K1', 'K1', '003376B3', '储罐未报关量'),
			array('L1:L1', 'L1', '003376B3', '罐出库量'),
			array('M1:M1', 'M1', '005E9CD3', '罐结存量'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('报关量报表');

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
		$subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M');

		foreach ($result['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['storagetankname'];
						break;
					case 1:
						$value = $item['customername'];
						break;
					case 2:
						$value = $item['goodsname'];
						break;
					case 3:
						$value = $item['updated_at'];
						break;
					case 4:
						$value = $item['shipname'];
						break;
					case 5:
						$value = $item['tobeqty'];
						break;
					case 6:
						$value = $item['bussinesscheckqty'];
						break;
					case 7:
						$value = $item['release_num'];
						break;
					case 8:
						$value = $item['release_beqty'];
						break;
					case 9:
						$value = $item['storagetank_beqty'];
						break;
					case 10:
						$value = $item['unrelease_num'];
						break;
					case 11:
						$value = $item['storagetankoutqty'];
						break;
					case 12:
						$value = $item['storagetankqty'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="报关量报表.xlsx"');
		header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}


}