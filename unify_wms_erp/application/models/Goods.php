<?php

/**
 * 货品管理model
 *
 * @author  Alan
 * @date    2016-11-17 15:25:02
 *
 */
class GoodsModel
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

    /**
     * 获取货品的基本属性列表
     * @author Alan
     * @time 2016-11-17 15:27:18
     **/
    public function getBaseGoods($params = array())
    {
        $filter = array();

        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " bs.`goodsname` LIKE '%" . $params['goodsname'] . "%' ";
        }
        if (isset($params['goodsno']) && $params['goodsno'] != '') {
            $filter[] = " bs.`goodsno` LIKE '%" . $params['goodsno'] . "%' ";
        }
        if (isset($params['status']) && $params['status'] != ''&& $params['status'] != '0') {
            $filter[] = " bs.`status` = " . $params['status'] . " ";
        }
        /**/
        $where = 'where bs.isdel = 0 ';

        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = " ORDER BY bs.`updated_at` DESC ";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_goods` bs {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select bs.*,(select goodsname from ".DB_PREFIX."base_goods b where b.sysno = bs.parent_sysno) as goods_goodsno 
                        from ".DB_PREFIX."base_goods bs " . $where;

                $result['list'] = $this->dbh->select($sql);

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);


                $sql = "select bs.*,(select goodsname from ".DB_PREFIX."base_goods b where b.sysno = bs.parent_sysno) as goods_goodsno 
                        from ".DB_PREFIX."base_goods bs " . $where;

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 获取货品的基本属性列表
     * @author wu xianneng
     * @time
     **/
    public function getBaseGoodslist($params)
    {
        $filter = array();
        if (isset($params['goodsno']) && $params['goodsno'] != '') {
            $filter[] = " `goodsno` LIKE '%" . $params['goodsno'] . "%' ";
        }
        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " `goodsname` LIKE '%" . $params['goodsname'] . "%' ";
        }

        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " `status` = {$params['status']} ";
        }

        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = " `isdel` = {$params['isdel']} ";
        }

        if (1 <= count($filter)) {

            $where = 'WHERE '. implode(' AND ', $filter);
        }

        $order = " ORDER BY bs.`goodsno`, bs.`updated_at` DESC ";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_goods` {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select bg.*,(select goodsname from ".DB_PREFIX."base_goods g where g.sysno = bg.parent_sysno) as parent_goodsname
                        from ".DB_PREFIX."base_goods bg " . $where;

                $result['list'] = $this->dbh->select($sql);

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);


                $sql = "select bg.*,(select goodsname from ".DB_PREFIX."base_goods g where g.sysno = bg.parent_sysno) as parent_goodsname
                        from ".DB_PREFIX."base_goods bg " . $where;

                $result['list'] = $this->dbh->select_page($sql);
            }
        }

        //如果在所有列的子菜单中找不到父菜单，则将它赋值为空
        for($i=0;$i<count($result['list']);$i++){
            $flag='no';
            for($j=0;$j<count($result['list']);$j++){
                if($result['list'][$i]['parent_sysno']==$result['list'][$j]['sysno']){
                    $flag='yes';
                    break;
                }
            }
            if($result['list'][$i]['parent_sysno']==0||$flag=='no'){
                $result['list'][$i]['parent_sysno']=null;
            }
        }
        return $result;
    }

    /**
     * 获取 GOODS 父类树状图
     */
    public function getGoodsInfo()
    {
        $sql = "SELECT sysno,parent_sysno,goodsno,goodsname FROM ".DB_PREFIX."base_goods WHERE `status` = 1 AND isdel = 0 ";
        return $this->dbh->select($sql);
    }

    /**
     * 获取 GOODS 详细信息
     * @author wu xianneng
     */
    public function getGoodsInfos()
    {
        $sql = "SELECT bg.sysno,bg.parent_sysno,bg.goodsname,ga.unit_sysno,bu.unitname
			FROM ".DB_PREFIX."base_goods bg
			LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
			LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
			WHERE bg.`status` = 1 AND bg.isdel = 0 GROUP BY bg.goodsname ";
        return $this->dbh->select($sql);
    }
    public function getGoodsName($id){
        $sql = "SELECT bg.sysno,bg.parent_sysno,bg.goodsname,ga.unit_sysno,bu.unitname
			FROM ".DB_PREFIX."base_goods bg
			LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
			LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
			WHERE bg.sysno=".$id;
        return $this->dbh->select($sql);
    }

    /**
     * 根据ID 货品信息
     */
    public function getGoodsById($id)
    {
        $sql = " SELECT bg.*,bgp.goodsname as bgp_name
                FROM ".DB_PREFIX."base_goods bg
                LEFT JOIN ".DB_PREFIX."base_goods bgp ON bgp.sysno = bg.parent_sysno
                WHERE bg.isdel = 0 AND  bg.sysno = " . $id;
        return $this->dbh->select_row($sql);
    }

    /*
     * 判断当前货品是否存在
     * @author wu xianneng
     */
    public function searchGoodsisexist($params)
    {
        $filter = array();
        if (isset($params['goodsno']) && $params['goodsno'] != '') {
            $filter[] = "  `goodsno` = '{$params['goodsno']}'";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = "  `status` = {$params['status']} ";
        }

        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = "  `isdel` = {$params['isdel']} ";
        }
        if (1 <= count($filter)) {

            $where = 'WHERE '. implode(' AND ', $filter);
        }
        $sql = " SELECT * FROM ".DB_PREFIX."base_goods ".$where;

        return $this->dbh->select($sql);
    }

    /**
     * 插入货品基本属性信息
     */
    public function addGoods($input)
    {
        return $this->dbh->insert(DB_PREFIX.'base_goods', $input);
    }

    /**
     * 更新 货品基本属性数据
     */
    public function updateGoods($params, $id)
    {
        return $this->dbh->update(DB_PREFIX.'base_goods', $params, 'sysno=' . intval($id));
    }

    /**
     * 删除货品基本属性
     */
    public function delGoods($id)
    {
        $params = array();
        $params['isdel'] = 1;
        $params['status'] = 2;

        return $this->dbh->update(DB_PREFIX.'base_goods', $params, 'sysno=' . intval($id));
    }

    /**
     * 获取列表页
     * @author Alan
     * @time 2016年11月18日15:30:14
     */
    public function getBaseGoodsAttribute($params)
    {
        $filter = array();

        if (isset($params['goodsno']) && $params['goodsno'] != '') {
            $filter[] = " bg.`goodsno` LIKE '%" . $params['goodsno'] . "%' ";
        }
        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " bg.goodsname LIKE '%" . $params['goodsname'] . "%' ";
        }
        if (isset($params['id']) && $params['id'] != '') {
            $filter[] = " ga.sysno = " . $params['id'] . " ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " ga.goods_sysno = " . $params['goods_sysno'] . " ";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " ga.`status` = {$params['status']} ";
        }
        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = " ga.isdel = {$params['isdel']} ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_goods_attribute` ga
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = ga.goods_sysno {$where} ";
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT ga.*,bg.goodsno,bg.goodsname,bu.unitname
                         FROM ".DB_PREFIX."base_goods_attribute ga
                         LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = ga.goods_sysno
                         LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = ga.unit_sysno  " . $where;

                $result['list'] = $this->dbh->select($sql);

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);


                $sql = "SELECT ga.*,bg.goodsno,bg.goodsname,bu.unitname
                         FROM ".DB_PREFIX."base_goods_attribute ga
                         LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = ga.goods_sysno
                         LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = ga.unit_sysno  " . $where;

                $result['list'] = $this->dbh->select_page($sql);
            }

            $S = new StoragetankModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
            for($i=0;$i<count($result['list']);$i++){
                $storagetank_sysno = $result['list'][$i]['storagetank_sysno'];
                $storagetank_sysnoarr = explode(',',$storagetank_sysno);
                $storagetank_categoryname = array();
                foreach($storagetank_sysnoarr as $item2){
                    $storagetank_categorynamedata = $S->getStoragetankcategoryById($item2);
                    $storagetank_categoryname[] = $storagetank_categorynamedata['storagetank_categoryname'];
                }
                $result['list'][$i]['storagetank_categoryname']=implode(',',$storagetank_categoryname);
            }
        }
        return $result;
    }

    /*
     * 搜索其他属性
     */
    public function searchAttibute($params){
        $filter = array();
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = "  `goods_sysno` = {$params['goods_sysno']}";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = "  `status` = {$params['status']} ";
        }

        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = "  `isdel` = {$params['isdel']} ";
        }
        if (1 <= count($filter)) {

            $where = 'WHERE '. implode(' AND ', $filter);
        }
        $sql = " SELECT * FROM ".DB_PREFIX."base_goods_attribute ".$where;

        return $this->dbh->select($sql);
    }

    /*
     * 通过货品id查询其他属性
     * @author wu xianneng
     */
    public function getAttributeByGoodsId($id){
        $sql = "SELECT ga.*,bg.goodsno,bg.goodsname,bu.unitname
                 FROM ".DB_PREFIX."base_goods_attribute ga
                 LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = ga.goods_sysno
                 LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = ga.unit_sysno
                 WHERE ga.status = 1 AND ga.isdel = 0 AND ga.goods_sysno = ".$id;
        return $this->dbh->select($sql);
    }

    /*
     * 根据id查询货品其他属性
     */
    public function getGoodsAttributeById($id){
        $sql = "select * from ".DB_PREFIX."base_goods_attribute where sysno = $id";
        return $this->dbh->select_row($sql);
    }

    /**
     * 添加其他属性
     */

    public function addGoodsAttribute($params,$attachment)
    {
        $this->dbh->begin();


        $id = $this->dbh->insert(DB_PREFIX.'base_goods_attribute', $params);
    //    print_r($id);
        if(!$id){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加货品属性失败'];
        }
        //回写附件对应转移单的id
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(count($attachment) > 0){
            $res = 	$A->addAttachModelSysno($id,$attachment);
            if(!$res){
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加附件失败'];
            }
        }


        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>$id];
    }

    /**
     * 更新货品 其他属性
     */
    public function updateGoodsAttribute($params, $id)
    {
        return $this->dbh->update(DB_PREFIX.'base_goods_attribute', $params, 'sysno=' . intval($id));
    }

    /**
     * 更新货品 其他属性
     */
    public function updateGoodsAttributeOnly($params, $id,$attachment)
    {
        $this->dbh->begin();

        $res = $this->dbh->update(DB_PREFIX.'base_goods_attribute', $params, 'sysno=' . intval($id));
        if(!$res){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加货品属性失败'];
        }

        //回写附件对应转移单的id
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(count($attachment) > 0){
            $res = 	$A->addAttachModelSysno($id,$attachment);
            if(!$res){
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加附件失败'];
            }
        }


        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>$id];
    }

    /**
     * 删除 货品其他属性
     */
    public function delGoodsAttribute($id)
    {
        $params = array();
        $params['isdel'] = 1;
        $params['status'] = 2;

        return $this->dbh->update(DB_PREFIX.'base_goods_attribute', $params, 'sysno=' . intval($id));
    }

    public function getGoodsandprice(){
        $sql = "select bg.sysno as goods_sysno,bg.goodsname,bga.islongterm,bga.rate_waste,bga.controlproportion,bga.controlprice,unit.sysno as unitsysno, unit.unitname,sc.storagetank_categoryname
	            from ".DB_PREFIX."base_goods bg
                LEFT JOIN ".DB_PREFIX."base_goods_attribute bga on bg.sysno = bga.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit unit on bga.unit_sysno = unit.sysno 
				LEFT JOIN ".DB_PREFIX."base_storagetank_category sc ON sc.sysno = bga.storagetank_sysno  
                where bga.status = 1 and bga.isdel = 0
                group by bg.goodsname ";

        $data = $this->dbh->select($sql);

        for ($i=0;$i<count($data);$i++){
            if($data[$i]['islongterm']==1){
                $data[$i]['islongterm'] = '是';
            }else{
                $data[$i]['islongterm'] = '否';
            }
        }
        return $data;
    }

    /*
     * 根据id判断是不是叶子节点
     */
    public function chirldnode($id){
        $sql = "select * from ".DB_PREFIX."base_goods where parent_sysno = $id AND status = 1 AND isdel = 0 ";
        return $this->dbh->select_row($sql);
    }

    /**
     * title 获取商品和价格相关信息
     */
    public function getGoodsandpriceByid($id)
    {
        $sql = "select dcg.*,bg.goodsname,bgq.qualityname,unit.unitname,com.companyname
	            from ".DB_PREFIX."doc_contract_goods dcg
                LEFT JOIN ".DB_PREFIX."base_goods bg on dcg.goods_sysno =bg.sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute bga on bg.sysno = bga.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_quality bgq on dcg.goods_quality_sysno =bgq.sysno
                LEFT JOIN ".DB_PREFIX."base_unit unit on bga.unit_sysno = unit.sysno
                LEFT JOIN ".DB_PREFIX."base_company com on dcg.invoice_company_sysno = com.sysno
                where dcg.contract_sysno = {$id} group by dcg.sysno";
        return $this->dbh->select($sql);
    }

    /*
     * 通过货品名称查询其他属性
     * @author wu xianneng
     */
    public function getAttributeByGoodsName($goodsname){
        $sql = "SELECT ga.*,bg.goodsno,bg.goodsname,bu.unitname
                 FROM ".DB_PREFIX."base_goods_attribute ga
                 LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = ga.goods_sysno
                 LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = ga.unit_sysno
                 WHERE ga.status = 1 AND ga.isdel = 0 AND bg.goodsname = '{$goodsname}' ";
        return $this->dbh->select_row($sql);
    }

}