<?php

class Report_StockoutreportController extends Yaf_Controller_Abstract
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
    public function listAction()
    {
    	$this->getView()->make('stockoutreport.stockoutreportlist', array());
    }

    public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'daterange' => $request->getPost('daterange', date("Y",time())),
		);

		$R = new Report_StockoutreportModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $R->search($search);

		echo json_encode($list);
	}

	public function dbtoexcelAction()
	{
		$request = $this->getRequest();
		$search = array (
			'daterange' => $request->getPost('daterange', date("Y",time())),
		);

		$R = new Report_StockoutreportModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $R->search($search); 


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("出库统计表")
			->setSubject("出库统计表")
			->setDescription("出库统计表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '月份'),
			array('B1:B1', 'B1', '005E9CD3', '出库总量'),
			array('C1:C1', 'C1', '005E9CD3', '船出库总量'),
			array('D1:D1', 'D1', '0094CE58', '船数(内贸)'),
			array('E1:E1', 'E1', '0094CE58', '船数(外贸贸)'),
			array('F1:F1', 'F1', '0094CE58', '船出库(外贸)'),
			array('G1:G1', 'G1', '0094CE58', '船出库(内贸)'),
			array('H1:H1', 'H1', '003376B3', '槽车出库总量'),
			array('I1:I1', 'I1', '003376B3', '车数'),
			array('J1:J1', 'J1', '003376B3', '灌桶数'),
			array('K1:K1', 'K1', '003376B3', '管出库总量'),
			array('L1:L1', 'L1', '003376B3', '管出库(外贸)'),
			array('M1:M1', 'M1', '003376B3', '管出库(内贸)'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('出库统计表');

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

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['month'];		
						break;
					case 1:
						$value = $item['beqty'];
						break;
					case 2:
						$value = $item['bussinesscheckqty'];
						break;
					case 3:
						$value = $item['shipnum1'];
						break;
					case 4:
						$value = $item['shipnum2'];
						break;
					case 5:
						$value = $item['shipstockoutqty1'];
						break;
					case 6:
						$value = $item['shipstockoutqty2'];
						break;
					case 7:
						$value = $item['pountsoutqty'];
						break;
					case 8:
						$value = $item['carnum'];
						break;
					case 9:
						$value = $item['bucketnumber'];
						break;
					case 10:
						$value = $item['pipelineoutqty'];
						break;
					case 11:
						$value = $item['pipelineoutqty1'];
						break;
					case 12:
						$value = $item['pipelineoutqty2'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="出库统计表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}

}