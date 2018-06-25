<?php
/**
 * @Author: HR
 * @Date:   2017-7-5
 * @Last Modified by:   HR
 * @Last Modified time: 2017-7-5
 */

class PipelineModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }


    public function getPipelineList($params)
    {
        $filter = array();
        if (isset($params['pipelinename']) && $params['pipelinename'] != '') {
            $filter[] = " pipelinename like '%{$params['pipelinename']}%' ";
        }
        if ( isset($params['pipelinetype']) && $params['pipelinetype'] != '' ) {
            $filter[] = "pipelinetype = '{$params['pipelinetype']}'";
        }

        if( isset($params['bar_status']) && $params['bar_status'] != '' ){
            $filter[] = "status = '{$params['bar_status']}'";
        }

        $where =" where isdel=0 "  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = " order by  updated_at desc";



        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."base_pipeline` {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."base_pipeline` {$where}";
      
    
        if($params['page']==false && isset($params['page'])){

            $result['list'] = $this->dbh->select($sql);

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            
            $data = $this->dbh->select_page($sql);


            $result['list']=$data;
        }

        

        return $result;
    }


    public function addPipeline($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_pipeline', $data);
    }


    public function getPipelineByid($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."base_pipeline` where sysno=".intval($id);
        return $this->dbh->select_row($sql);
    }


    public function editPipeline($data,$id)
    {
        return $this->dbh->update(DB_PREFIX.'base_pipeline', $data, 'sysno='.intval($id));
    }


    public function getPipelineHistory($id,$params)
    {

        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " usetime >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " usetime <= '{$params['end_time']} 23:59:59' ";
        }        
        if ( isset($params['pipelinetype']) && $params['pipelinetype'] != '' ) {
            $filter[] = "pipelinetype = '{$params['pipelinetype']}'";
        }

        if( isset($params['goods_sysno']) && $params['goods_sysno'] != '' ){
            $filter[] = "goods_sysno = '{$params['goods_sysno']}'";
        }

        $where =" where isdel=0 AND pipeline_sysno={$id}"  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = " order by  created_at desc";



        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."base_pipeline_uselog` {$where} ";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."base_pipeline_uselog` {$where} {$order}";

        if($params['page']===false){

            $result = $this->dbh->select($sql);

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            
            $data = $this->dbh->select_page($sql);


            $result['list']=$data;
        }


        return $result;
    }


    public function getPipelineClear($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."base_pipeline_clearlog` where pipeline_sysno={$id} ";

        return $this->dbh->select($sql);
    }


    public function addPipelineClear($input)
    {
        return $this->dbh->insert(DB_PREFIX.'base_pipeline_clearlog',$input);
    }

    public function isUsepipeline($id)
    {
        $info = $this->getPipelineByid($id);
        $pipelinetype = $info['pipelinetype'];
        $type = $pipelinetype==1?'wharf_pipeline_sysno':'area_pipeline_sysno'; //判断是那种管线 where条件的时候使用

        $sql = "SELECT count(dp.sysno) FROM `".DB_PREFIX."doc_pipelineorder` dp
                LEFT JOIN `".DB_PREFIX."doc_pipelineorder_detail` dpd ON dp.sysno = dpd.pipelineorder_sysno
                WHERE dp.isdel=0 AND ". $type ." = {$id}";
        $result = $this->dbh->select_one($sql);

        return $result>0 ? true : false;
    }

    /**
     * 是否重名
     * @param  [type]  $pipelinename [description]
     * @return boolean               [description]
     */
    public function isPipelinename($pipelinename)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."base_pipeline` where isdel<>1 AND pipelinename = '$pipelinename' ";

        return $this->dbh->select_row($sql);
    }

}