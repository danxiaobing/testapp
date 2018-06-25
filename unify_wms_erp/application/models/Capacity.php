<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/22 0022
 * Time: 10:08
 */
class CapacityModel
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
     * 查询可用罐容列表
     * @author hanshutan
     */
    public function getCapacity($params, $pages = 10, $pagec = 1)
    {
        $filter = array();
        if (isset($params['storagetankname']) && $params['storagetankname'] != '') {
            $filter[] = " tank.`storagetankname` LIKE '%" . $params['storagetankname'] . "%' ";
        }
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " tank.sysno = " . $params['sysno'];
        }

        $where = ' where stock.isdel = 0 and stock.status = 1 AND stock.iscurrent = 1  ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = " ORDER BY stock.`sysno` DESC";
        $group = " Group BY tank.sysno";
        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(DISTINCT tank.sysno) FROM `".DB_PREFIX."storage_stock` stock
                LEFT JOIN ".DB_PREFIX."base_storagetank tank ON stock.storagetank_sysno = tank.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank_category tanktype  ON tank.storagetank_category_sysno = tanktype.sysno
                LEFT JOIN ".DB_PREFIX."customer customer ON stock.customer_sysno = customer.sysno
                LEFT JOIN ".DB_PREFIX."base_goods goods ON stock.goods_sysno = goods.sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute attribute ON stock.goods_sysno = attribute.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit unit ON attribute.unit_sysno = unit.sysno
                {$where} {$group} {$order} ";

        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT stock.sysno, tank.storagetankname,tanktype.storagetank_categoryname,tank.storagetanknature,tank.actualcapacity,
                tank.storagetankbg,customer.customername,goods.goodsname,unit.unitname,SUM(stock.instockqty) instockqty,tank.sysno as tankid
                FROM `".DB_PREFIX."storage_stock` stock
                LEFT JOIN ".DB_PREFIX."base_storagetank tank ON stock.storagetank_sysno = tank.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank_category tanktype  ON tank.storagetank_category_sysno = tanktype.sysno
                LEFT JOIN ".DB_PREFIX."customer customer ON stock.customer_sysno = customer.sysno
                LEFT JOIN ".DB_PREFIX."base_goods goods ON stock.goods_sysno = goods.sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute attribute ON stock.goods_sysno = attribute.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit unit ON attribute.unit_sysno = unit.sysno
                {$where} {$group} {$order} ";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /*
     * 查询可用罐容
     * @author wu xianneng
     * @editor hanshutan 2017/2/25
     */
    public function searchCapacity($params)
    {
        $filter = array();
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " bs.`sysno` = " . $params['storagetank_sysno'] . " ";
        }
        if (isset($params['storagetankname']) && $params['storagetankname'] != '') {
            $filter[] = " bs.`storagetankname` LIKE '%" . $params['storagetankname'] . "%' ";
        }
        if (isset($params['area_sysno']) && $params['area_sysno'] != '') {
            $filter[] = " bs.`area_sysno` = " . $params['area_sysno'] . " ";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[] = " cu.`customername` LIKE '%" . $params['customername'] . "%' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != ''){
            $filter[] = " bg.`sysno` = " . $params['goods_sysno'] . " ";
        }

        $where = 'bs.status=1 AND bs.isdel=0 and bga.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(bs.sysno) from ".DB_PREFIX."base_storagetank AS bs
            LEFT JOIN ".DB_PREFIX."base_storagetank_category as bsc on bs.storagetank_category_sysno = bsc.sysno
            LEFT JOIN ".DB_PREFIX."base_goods as bg on bg.sysno = bs.goods_sysno
            LEFT JOIN ".DB_PREFIX."base_goods_attribute as bga on bga.goods_sysno = bs.goods_sysno
            LEFT JOIN ".DB_PREFIX."base_unit as unit on unit.sysno = bga.unit_sysno
            where {$where}
            order by bs.updated_at desc ";
        
        $result['totalRow'] = $this->dbh->select_one($sql);


        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT ba.areaname,bs.sysno,bs.storagetankname,bsc.storagetank_categoryname,bs.storagetanknature,unit.unitname,
                        bs.qualityname,bg.goodsname,bs.actualcapacity,bs.tank_stockqty,bs.orderinqty,bs.orderoutqty
                        FROM ".DB_PREFIX."base_storagetank AS bs
                        LEFT JOIN ".DB_PREFIX."base_storagetank_category as bsc on bs.storagetank_category_sysno = bsc.sysno
                        LEFT JOIN ".DB_PREFIX."base_goods as bg on bg.sysno = bs.goods_sysno
                        LEFT JOIN ".DB_PREFIX."base_goods_attribute as bga on bga.goods_sysno = bs.goods_sysno
                        LEFT JOIN ".DB_PREFIX."base_unit as unit on unit.sysno = bga.unit_sysno
                        LEFT JOIN ".DB_PREFIX."base_area ba ON ba.sysno = bs.area_sysno
                        where {$where} group by bs.sysno";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT ba.areaname,bs.sysno,bs.storagetankname,bsc.storagetank_categoryname,bs.storagetanknature,unit.unitname,
                        bs.qualityname,bg.goodsname,bs.actualcapacity,bs.tank_stockqty,bs.orderinqty,bs.orderoutqty
                        FROM ".DB_PREFIX."base_storagetank AS bs
                        LEFT JOIN ".DB_PREFIX."base_storagetank_category as bsc on bs.storagetank_category_sysno = bsc.sysno
                        LEFT JOIN ".DB_PREFIX."base_goods as bg on bg.sysno = bs.goods_sysno
                        LEFT JOIN ".DB_PREFIX."base_goods_attribute as bga on bga.goods_sysno = bs.goods_sysno
                        LEFT JOIN ".DB_PREFIX."base_unit as unit on unit.sysno = bga.unit_sysno
                        LEFT JOIN ".DB_PREFIX."base_area ba ON ba.sysno = bs.area_sysno
                        where {$where} group by bs.sysno";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }

        return $result;
    }

    /**
     * @title 根据罐容编号 查询储罐历史货品表记录
     * @param $id
     * @return array
     */

    public function getstockgoodsbytankid($id)
    {
        $result = array();
        if(!$id){
            return false;
        }
        $sql = "  select bg.goodsname as goodslog from ".DB_PREFIX."base_storagetank_goods as bsg 
                LEFT JOIN  ".DB_PREFIX."base_goods as bg on bg.sysno = bsg.goods_sysno
                WHERE storagetank_sysno = {$id} 
                GROUP BY goods_sysno 
                ORDER BY bsg.created_at DESC 
                limit 3 ";
        $result = $this->dbh->select($sql);
        return $result;
    }


}