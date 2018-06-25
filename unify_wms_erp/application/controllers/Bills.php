<?php
/**
 * 用户信息
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 17:09
 */

class BillsController extends Yaf_Controller_Abstract
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
	
	/*
	 *结算管理-对帐单
	 */
	public function listAction()
	{
		$params=array();
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
		$search = array(
			'bar_status'=>1,
			'page'=>false
		);
		$customerdata = $C->searchCustomer($search);
		$params['customerlist'] = $customerdata['list'];

		$this->getView()->make('bills.billslist',$params);
	}

	/*
	 * 获取对账单数据
	 */
	public function billslistJsonAction()
    {
		$request=$this->getRequest();
		$date2 = $request->getPost('enddate',date('Y-m-d'));//
        $params=array(
				'startdate'=>$request->getPost('startdate',date('Y-m-d',strtotime('-1 months'))),
				'enddate'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),//
				'customer_sysno'=>$request->getPost('customer_sysno',''),
				'pageCurrent' => COMMON:: P(),
				'pageSize' => COMMON:: PR(),
		);

		$billsModel=new BillsModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
		$resultdata=$billsModel->getBills($params);

        echo json_encode($resultdata);
    }

	/*
	 * 对账单导出EXCEL
	 */
	public function excelAction()
	{
		$request=$this->getRequest();
		$date2 = $request->getPost('date2',date('Y-m-d'));//
		$params=array(
				'startdate'=>$request->getPost('startdate',date('Y-m-d',strtotime('-1 months'))),
				'enddate'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),//
				'customer_sysno'=>$request->getPost('customer_sysno',''),
				'page' => false,
		);
		
		$billsModel=new BillsModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
		$resultdata=$billsModel->getBills($params);

        ob_end_clean();//清除缓冲区,避免乱码
		
		$objPHPExcel=new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
	        ->setTitle("对帐单")
			->setSubject("对帐单")
			->setDescription("对帐单");
		
		$mainTitle=array(
		    array('A1:A1', 'A1', '0094CE58', '客户名称'),
            array('B1:B1', 'B1', '005E9CD3', '信用额度'),
            array('C1:C1', 'C1', '005E9CD3', '可用信用额度'),
            array('D1:D1', 'D1', '0094CE58', '信用期限(月)'),
            array('E1:E1', 'E1', '003376B3', '超期'),
            array('F1:F1', 'F1', '003376B3', '期初应收金额'),
			array('G1:G1', 'G1', '003376B3', '本期发生额'),
			array('H1:H1', 'H1', '003376B3', '折扣额'),
			array('I1:I1', 'I1', '003376B3', '本期收款额'),
			array('J1:J1', 'J1', '003376B3', '未核销收款余额'),
			array('K1:K1', 'K1', '003376B3', '期末应收金额'),
			array('L1:L1', 'L1', '003376B3', '已开票金额'),
			array('M1:M1', 'M1', '003376B3', '未开票金额')
		);
		
		$objActSheet=$objPHPExcel->getActiveSheet();
		$objActSheet->setTitle("对帐单");

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F','G','H','I','J','K','L','M');
		
		foreach($resultdata['list'] as $item)
		{
			$line++;
			for($i=0;$i<count($subtitle);$i++)
			{
				$site = $subtitle[$i] . $line;
                $value = '';
				 switch ($i) {
                    case 0:
                        $value = $item['customername'];
                        break;
                    case 1:
                        $value = $item['customercredit'];
                        break;
                    case 2:
                        $value = $item['customercredit']-($item['firstcost']+$item['nowcost']-$item['discountcost']-$item['rececost']);
                        break;
                    case 3:
                        $value = $item['customerterm'];
                        break;
					case 4:
						if($item['overflag']==1){
							$value = '是';
						}else{
							$value = '否';
						}
                        break;
					case 5:
                        $value = $item['firstcost'];
                        break;
					case 6:
						$value = $item['nowcost'];
						break;
					case 7:
						$value = $item['discountcost'];
						break;
					case 8:
						$value = $item['rececost'];
						break;
					case 9:
						$value = $item['remaincost'];
						break;
					case 10:
						$value = $item['firstcost']+$item['nowcost']-$item['discountcost']-$item['rececost'];
						break;
					case 11:
						$value = $item['haveinvocost'];
						break;
					case 12:
						$value = $item['notinvocost'];
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

}