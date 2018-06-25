<?php
/**
 * 提单
 */
class Report_CustomerintradayController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }

	public function listAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('customerintraday.list',$params);
	}


	public function listJsonAction() {
		$request = $this->getRequest();
		
		$search = array (
			'bar_date' => $request->getPost('bar_date',''),
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'goods_sysno'=> $request->getPost('goods_sysno',''),
			'goodsnature' => $request->getPost('goodsnature',''),
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
		);

		$RC = new Report_CustomerintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RC->search($search);
		echo json_encode($list);
	}
	public function detailListAction()
	{
		$request = $this->getRequest();
		$data =json_decode($request->getPost('data',''),true);
		$bar_date = $request->getPost('bar_date','');
		if(!$bar_date){
			$data['date'] = date('Y-m-d');
		}else{
			$data['date'] = date('Y-m-d',strtotime($bar_date));
		}

        $params['data'] = json_encode($data);
		$this->getView()->make('customerintraday.detail',$params);
	}

	public function detailJsonAction() {
		$request = $this->getRequest();
		$data = $request->getPost('data','');
		$data['page'] = false;
		$data['bar_date'] = $data['date'];
		if($data){
			$RC = new Report_CustomerintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$result = $RC->search($data);
			foreach ($result['list'] as $key => $value) {
				$result['list'][$key]['date'] = $data['date'];
			}
			echo json_encode($result['list']);
		}else{
			echo json_encode([]);
		}
		
	}

	public function inAndOutDetailJsonAction() {
		$request = $this->getRequest();
		$data = $request->getPost('data','');
		if($data){
			$RC = new Report_CustomerintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$list = $RC->getDetailData($data);
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
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'goods_sysno'=> $request->getPost('goods_sysno',''),
			'goodsnature' => $request->getPost('goodsnature',''),
			'page' => false,
		);

		$RC = new Report_CustomerintradayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RC->search($search);


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("客户日收发存统计表")
			->setSubject("客户日收发存统计表")
			->setDescription("客户日收发存统计表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '客户'),
			array('B1:B1', 'B1', '005E9CD3', '品名'),
			array('C1:C1', 'C1', '005E9CD3', '货物性质'),
			array('D1:D1', 'D1', '0094CE58', '昨日结存量'),
			array('E1:E1', 'E1', '0094CE58', '今日入库量'),
			array('F1:F1', 'F1', '0094CE58', '今日出库量'),
			array('G1:G1', 'G1', '0094CE58', '今日损耗量'),
			array('H1:H1', 'H1', '003376B3', '今日结存量'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('客户日收发存统计表');

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
		$subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['customername'];
						break;
					case 1:
						$value = $item['goodsname'];
						break;
					case 2:
						switch($item['goodsnature']){
							case "1":
								$value = "保税";
								break;
							case "2":
								$value = "外贸";
								break;
							case "3":
								$value = "内贸转出口";
								break;
							case "4":
								$value = "内贸内销";
								break;
						}
						break;
					case 3:
						$value = $item['endingstocks'];
						break;
					case 4:
						$value = $item['inqty'];
						break;
					case 5:
						$value = $item['outqty'];
						break;
					case 6:
						$value = $item['ullage'];
						break;
					case 7:
						$value = $item['endstock'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="客户日收发存统计表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}
}
