<?php
/**
 * 提单
 */
class Report_StoragetankintradayController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }

	public function listAction() {
		
		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$storagetankList = $S->searchStoragetank($search);
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$params['storagetankList'] = $storagetankList['list'];
		$this->getView()->make('storagetankintraday.list',$params);
	}


	public function listJsonAction() {
		$request = $this->getRequest();
		
		$search = array (
			'bar_date' => $request->getPost('bar_date',''),
			'storagetank_sysno' => $request->getPost('storagetank_sysno',0),
			'goods_sysno' => $request->getPost('goods_sysno',0),
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
		);

		$RS = new Report_StoragetankintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RS->search($search);
		echo json_encode($list);
	}
	public function detailListAction()
	{
		$request = $this->getRequest();
		$data =json_decode($request->getPost('data',''),true);
		$bar_date = $request->getPost('bar_date','');
		if(!$bar_date){
			$data['bar_date'] = date('Y-m-d');
		}else{
			$data['bar_date'] = date('Y-m-d',strtotime($bar_date));
		}
		
        $params['data'] = $data;
		$this->getView()->make('storagetankintraday.detail',$params);
	}
/*
	public function detailJsonAction() {
		$request = $this->getRequest();
		$data = $request->getPost('data','');
		$data['page'] = false;
		$data['bar_date'] = $data['date'];
		if($data){
			$RS = new Report_StoragetankintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$result = $RS->search($data);
			foreach ($result['list'] as $key => $value) {
				$result['list'][$key]['date'] = $data['date'];
			}
			echo json_encode($result['list']);
		}else{
			echo json_encode([]);
		}
		
	}
*/
	public function inAndOutDetailJsonAction() {
		$request = $this->getRequest();

		$search = array(
			'storagetank_sysno' => $request->getParam('storagetank_sysno',''),
			'goods_sysno'       => $request->getParam('goods_sysno',''),
			'bar_date'       => $request->getParam('bar_date',''),
			);
		

		$RS = new Report_StoragetankintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RS->getDetailData($search);
		if(!empty($list)){
			echo json_encode($list);
		}else{
			echo json_encode([]);
		}
	}

	public function dbtoexcelAction()
	{

		$request = $this->getRequest();

		$search = array (
			'bar_date' => $request->getPost('bar_date',''),
			'storagetank_sysno' => $request->getPost('storagetank_sysno',0),
			'goods_sysno' => $request->getPost('goods_sysno',0),
			'page' => false,
		);

		$RS = new Report_StoragetankintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RS->search($search);


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("储罐日统计总表")
			->setSubject("储罐日统计总表")
			->setDescription("储罐日统计总表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '储罐号'),
			array('B1:B1', 'B1', '005E9CD3', '储罐性质'),
			array('C1:C1', 'C1', '005E9CD3', '品名'),
			array('D1:D1', 'D1', '0094CE58', '计量单位'),
			array('E1:E1', 'E1', '0094CE58', '结存量'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('储罐日统计总表');

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
		$subtitle = array('A', 'B', 'C', 'D', 'E');

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['storagetankname'];
						break;
					case 1:
						switch($item['storagetanknature']){
							case "1":
								$value = "内贸罐";
								break;
							case "2":
								$value = "外贸罐";
								break;
							case "3":
								$value = "保税罐";
								break;
						}
						break;
					case 2:
						$value = $item['goodsname'];
						break;
					case 3:
						$value = '吨';
						break;
					case 4:
						$value = $item['endstock'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="储罐日统计总表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}
	public function detail_dbtoexcelAction()
	{

		$request = $this->getRequest();

		
		$list = $request->getPost('data','');

		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("储罐日统计明细表")
			->setSubject("储罐日统计明细表")
			->setDescription("储罐日统计明细表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '客户名称'),
			array('B1:B1', 'B1', '005E9CD3', '入库日期'),
			array('C1:C1', 'C1', '005E9CD3', '船名'),
			array('D1:D1', 'D1', '0094CE58', '计量单位'),
			array('E1:E1', 'E1', '0094CE58', '结存量'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('储罐日统计明细表');

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
		$subtitle = array('A', 'B', 'C', 'D', 'E');

		foreach ($list as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['customername'];
						break;
					case 1:
						$value = $item['doc_time'];
						break;
					case 2:
						$value = $item['shipname'];
						break;
					case 3:
						$value = $item['unit'];
						break;
					case 4:
						$value = $item['endingstock'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="储罐日统计明细表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}
}
