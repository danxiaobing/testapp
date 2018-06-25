<?php
/**
 * @Author: HR
 * @Date:   2017-7-5
 * @Last Modified by:   HR
 * @Last Modified time: 2017-7-5
 */
class PipelineController extends Yaf_Controller_Abstract
{
    public $businesstype = array(
            1=>'船入库预约',
            2=>'船入库订单',
            3=>'车入库预约',
            4=>'车入库订单',
            5=>'管入库预约',
            6=>'管入库订单',
            7=>'船出库预约',
            8=>'船出库订单',
            9=>'车出库预约',
            10=>'车出库订单',
            11=>'管出库预约',
            12=>'管出库订单',
            13=>'靠泊装卸入预约',
            14=>'靠泊装卸出预约',
            15=>'靠泊装卸入订单',
            16=>'靠泊装卸出订单',
    );
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
     * 管线管理的list页面
     * @return [type] [description]
     */
    public function listAction()
    {
    	$params = [];

    	return $this->getView()->make('pipeline.list', $params);
    }

    /**
     * 管线管理list数据
     * @return [type] [description]
     */
    public function listJsonAction()
    {
    	$request= $this->getRequest();
    	$search = array(
    		'pipelinename'	=> $request->getPost('pipelinename', ''),
    		'pipelinetype'	=> $request->getPost('pipelinetype', ''),
    		'bar_status'	=> $request->getPost('bar_status', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),   		
    		);

    	$P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$data = $P->getPipelineList($search);

    	echo json_encode($data);
    }

    /**
     * 管线管理edit界面
     * @return [type] [description]
     */
    public function pipelineeditAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('sysno', 0);

    	if(!$id)
    	{
    		$action = 'Pipeline/newJson';
    		$params['action'] = $action;
    	}else{
    		$action = 'Pipeline/editJson';
    		$P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    		$params = $P->getPipelineByid($id);
            $params['id'] = $id;
    		$params['action'] = $action;
    	}
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
                'status' => 1,
                'page' => false,
        );
        $list =  $G->getBaseGoodsAttribute($search);
        $params['goodslist'] = json_encode($list['list']);
    	return $this->getView()->make('pipeline.pipelineedit',$params);

    }

    /**
     * 管线管理添加方法
     * @return [type] [description]
     */
    public function newJsonAction()
    {
    	$request = $this->getRequest();
    	$params = array(
    			'pipelinename'      => $request->getPost('pipelinename', ''),
    			'pipelinetype'      => $request->getPost('pipelinetype', ''),
    			'pipelineflow'      => $request->getPost('pipelineflow', ''),
    			'installtime'       => $request->getPost('installtime', ''),
    			'status'	        => $request->getPost('status', 1),
                'goodsname'         => $request->getPost('goodsname', ''),   
                'goods_sysno'       => $request->getPost('goods_sysno', 0),  
                'pipelinecategory'  => $request->getPost('pipelinecategory', 0),  
                'iswarm'            => $request->getPost('iswarm', 0),  
                'caliber'           => $request->getPost('caliber', 0),	
    			'isdel'	   	        => 0,	
    			'created_at'        => '=NOW()',	
    			'updated_at'        => '=NOW()',	
    		);
    	
    	$P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $result = $P->isPipelinename($params['pipelinename']);

        if($result)
        {
            COMMON::result(300,'管线号重复!');
            exit;
        }
    	$res = $P->addPipeline($params);

    	if(!$res){
    		COMMON::result(300,'添加失败!');
    	}else{
    		COMMON::result(200,'添加成功!');
    	}
    }

    /**
     * 管线管理修改方法
     * @return [type] [description]
     */
    public function editJsonAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('pipeline_id', 0);

    	$params = array(
    			'pipelinename'      => $request->getPost('pipelinename', ''),
    			'pipelinetype'      => $request->getPost('pipelinetype', ''),
    			'pipelineflow'      => $request->getPost('pipelineflow', ''),
    			'installtime'       => $request->getPost('installtime', ''),
    			'status'	        => $request->getPost('status', 1),	
                'goodsname'         => $request->getPost('goodsname', ''),   
                'goods_sysno'       => $request->getPost('goods_sysno', 0),  
                'pipelinecategory'  => $request->getPost('pipelinecategory', 0),  
                'iswarm'            => $request->getPost('iswarm', 0),  
                'caliber'           => $request->getPost('caliber', 0), 
    			'updated_at'        => '=NOW()',	
    		);
    	
    	$P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$res = $P->editPipeline($params,$id);

    	if(!$res){
    		COMMON::result(300,'修改失败!');
    	}else{
    		COMMON::result(200,'修改成功!');
    	}
    }

    /**
     * 启用停用方法
     * @return [type] [description]
     */
    public function setstatusAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('id', 0);
    	$status = $request->getPost('status', '');
    		
    	$params = array(
    		'status' 		=> $status,
    		'updated_at'	=> '=NOW()',
    		);
    	$P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

    	$res = $P->editPipeline($params,$id);
    	if($status==1){
    		$msg = '启用';
    	}else{
			$msg = '停用';
    	}
    	if(!$res){
    		COMMON::result(300,$msg.'失败!');
    	}else{
    		COMMON::result(200,$msg.'成功!');
    	}    	
    }

    /**
     * 删除方法
     * @return [type] [description]
     */
    public function delpipelineAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getPost('id', 0);
    	
        $P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        //判断该管线是否被引用
        $res = $P->isUsepipeline($id);
        if ($res) {
            COMMON::result(300,'该管线已被引用不能被删除!');
            exit();
        }
        $params = array(
            'pipelinename' => $request->getPost('pipelinename', '').'del'.time(),
            'isdel'         => 1,
            'updated_at'    => '=NOW()',
            );

    	$res = $P->editPipeline($params,$id); 

    	if(!$res){
    		COMMON::result(300,'删除失败!');
    	}else{
    		COMMON::result(200,'删除成功!');
    	}      	   	
    }

    /**
     * 管线使用历史界面跳转
     * @return [type] [description]
     */
    public function pipelinehistoryAction()
    {
        $request = $this->getRequest();
        $params['id'] = $request->getPost('id', 0);
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodsList'] = $goods->getGoodsInfo();
        return $this->getView()->make('pipeline.pipelinehistory', $params);

    }

    /**
     * 管线使用历史数据
     * @return [type] [description]
     */
    public function historyJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $search = array(
            'begin_time'    => $request->getPost('begin_time', ''),
            'end_time'      => $request->getPost('end_time', ''),
            'pipelinetype'  => $request->getPost('pipelinetype', ''),
            'goods_sysno'   => $request->getPost('goods_sysno', ''),
            'pageCurrent'   => COMMON:: P(),
            'pageSize'      => COMMON:: PR(),            
            );      

        $P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $P->getPipelineHistory($id,$search);

        echo json_encode($params);
    }

    /**
     * 管线洗管界面
     * @return [type] [description]
     */
    public function pipelineClearAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $params['action'] = 'pipeline/pipelineClearAddJson';
        $pipelineno = $request->getPost('pipelineno', '');
        //客户列表
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['clearList'] = json_encode($this->pipelineClearListJsonAction());
        $params['id'] = $id;
        $params['pipelineno'] = $pipelineno;
        return $this->getView()->make('pipeline.pipelineclear', $params);

    }

    /**
     * 添加管线洗管记录
     * @return [type] [description]
     */
    public function pipelineClearAddJsonAction()
    {
        $request = $this->getRequest();
        $input = array(
            'pipeline_sysno'        => $request->getPost('pipeline_sysno', 0),
            'pipelineno'            => $request->getPost('pipelineno', ''),
            'created_employeename'  => $request->getPost('created_employeename', ''),
            'cleartime'             => $request->getPost('cleartime', '').' '.date('H:i:s', time()),
            'created_user_sysno'    => $request->getPost('created_user_sysno', 0),
            'memo'                  => $request->getPost('memo', ''),
            'status'                => 1,
            'isdel'                 => 0,
            'created_at'            => '=NOW()',
            'updated_at'            => '=NOW()',
            );

        $P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    
        $res = $P->addPipelineClear($input); 

        if(!$res){
            COMMON::result(300,'添加失败!');
        }else{
            COMMON::result(200,'添加成功!');
        } 


    }

    /**
     * 获取管线洗管记录
     * @return [type] [description]
     */
    public function pipelineClearListJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $P->getPipelineClear($id);  

        return $data;
    }

    /**
     * 管线使用历史Excel导出
     * @return [type] [description]
     */
    public function historyExcelAction()
    {

        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $search = array(
            'begin_time'    => $request->getPost('begin_time', ''),
            'end_time'      => $request->getPost('end_time', ''),
            'pipelinetype'  => $request->getPost('pipelinetype', ''),
            'goods_sysno'   => $request->getPost('goods_sysno', ''),
            'page'          => false,        
            );      

        $P = new PipelineModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $P->getPipelineHistory($id,$search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("管线使用历史表")
            ->setSubject("列表")
            ->setDescription("管线使用历史表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '管线号'),
            array('B1:B1', 'B1', '0094CE58', '管线类型'),
            array('C1:C1', 'C1', '0094CE58', '输送流量（吨）'),
            array('D1:D1', 'D1', '0094CE58', '业务单据类型'),
            array('E1:E1', 'E1', '005E9CD3', '业务单号/槽车进货'),
            array('F1:F1', 'F1', '0094CE58', '使用时间'),
            array('G1:G1', 'G1', '005E9CD3', '输送品种'),
            array('H1:H1', 'H1', '0094CE58', '操作人'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('管线使用历史表');

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

        $type = $this->businesstype;

        foreach ($params as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['pipelineno'];
                        break;
                    case 1:
                            $value = $item['pipelinetype'];
                        break;
                    case 2:
                            $value = $item['pipelineflow'];
                        break;
                    case 3:
                            $value = $type[$item['businesstype']];
                        break;
                    case 4:
                            $value = $item['businessno'];
                        break;
                    case 5:
                            $value = $item['usetime'];
                        break;
                    case 6:
                            $value = $item['goodsname'];
                        break;
                    case 7:
                            $value = $item['created_employeename'];
                        break;

                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="管线使用历史表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');  
    }
}