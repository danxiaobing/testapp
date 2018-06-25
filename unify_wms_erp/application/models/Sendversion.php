<?php 
/**
 * User:hr
 * Date: 2017/01/17 
 * Time: 13:30
 */

	class SendversionModel
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


    	public function getVersionInfo($search)
    	{

	        $filter = array();
	        if (isset($search['versionname']) && $search['versionname'] != '') {
	            $filter[] = " versionname like  '%{$search['versionname']}%' ";
	        }
	        if (isset($search['status']) && $search['status'] != '') {
	            $filter[] = " status = {$search['status']} ";
	        }

	        $where = ' where isdel=0 ';

	        if(count($filter) >=1){
	        	$where .= ' AND '.implode(' AND ', $filter);
	        }

	        $sql = " SELECT count(*) from ".DB_PREFIX."doc_version {$where} ";


	        $result['totalRow']=$this->dbh->select_one($sql);

    		$sql = "select * from ".DB_PREFIX."doc_version {$where} order by created_at desc";


    		$result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);

            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);

    		return $result;

    	}

    	/**
	     * 新增版本
	     */
	    public function add($data)
	    {
	        return $this->dbh->insert(DB_PREFIX.'doc_version', $data);
	    }


	    public function getVersionById($id)
	    {
	    	$sql = "SELECT * from `".DB_PREFIX."doc_version` where sysno=".intval($id);
	    	return $this->dbh->select_row($sql);
	    }


	    /**
	     * Title: 编辑
	     */
	    public function update($id, $data)
	    {
	        return $this->dbh->update(DB_PREFIX.'doc_version', $data, 'sysno=' . intval($id));
	    }


	    /**
	    * 发布停用版本
	    */

	    public function change($id,$state,$versiontype)
	    {
	    	if($state=='start'){
	    		$this->dbh->update(DB_PREFIX.'doc_version',array('status'=>2),"sysno!= {$id} and status!=3 and versiontype={$versiontype}");
	    		return $this->dbh->update(DB_PREFIX.'doc_version', array('status'=>1), 'sysno=' . intval($id),'versiontype='.$versiontype);
	    	}else{

	    		return $this->dbh->update(DB_PREFIX.'doc_version', array('status'=>2), 'sysno=' . intval($id),'versiontype='.$versiontype);
	    	}
	    }


	    /**
		* 获取部门信息
	    */

		public function getdepartmentinfo()
		{
			if (isset($search['status']) && $search['status'] != '') {
	            $filter[] = " `status` = ".$search['status']." ";
	        }


	        $where ='where isdel = 0';
	        if (1 <= count($filter)) {
	            $where .= " and ". implode(' AND ', $filter);
	        }


	        $sql = "SELECT * FROM `".DB_PREFIX."system_department` {$where} ";

	        $result = $this->dbh->select($sql);
	        return $result;

		}

		public function isonly($version_name)
		{
			$sql = " SELECT count(*) FROM `".DB_PREFIX."doc_version` where versionname = '{$version_name}' ";
			return $this->dbh->select_one($sql);
		}
    }