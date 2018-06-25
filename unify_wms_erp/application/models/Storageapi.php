<?php

/**
 * Created by PhpStorm.
 * User: HR
 * Date: 2017/02/14 0015
 * Time: 10:38
 */
class StorageapiModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * PoundsinModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 根据合同编号获取合同显示编号
    */
    public function getDisplaynoByContractno($contractno)
    {
        $sql = "SELECT contractnodisplay FROM `".DB_PREFIX."doc_contract` WHERE status = 1 AND  isdel = 0 AND contractno = '".$contractno."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     * 根据合同编号获取合同sysno
     */
    public function getSysnoByContractno($contractno)
    {
        $sql = "SELECT sysno FROM `".DB_PREFIX."doc_contract` WHERE status = 1 AND  isdel = 0 AND contractno = '".$contractno."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     * 根据用户编号获取用户SYSNO
     * @param $customerno
     * @return bool
     */
    public function getUserSysno($customerno){
        $sql = "SELECT sysno FROM `".DB_PREFIX."customer` WHERE status = 1 AND  isdel = 0 AND customerno = '".$customerno."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     * 根据用户编号获取用户name
     * @param $customerno_sysno
     * @return bool
     */
    public function getUserNameByCustomerSysno($customerno_sysno){
        $sql = "SELECT customername FROM `".DB_PREFIX."customer` WHERE status = 1 AND  isdel = 0 AND sysno = '".intval($customerno_sysno)."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     *根据商品编号获取商品SYSNO
     * @param string $goods_no
     * @return bool
     */
    public function getGoodsSysno($goods_no){
        $sql = "SELECT sysno FROM `".DB_PREFIX."base_goods` WHERE status = 1 AND  isdel = 0 AND goodsno = '".$goods_no."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     *根据商品SYSNO获取商品名称
     * @param int $goods_sysno
     * @return bool
     */
    public function getGoodsNameBySysno($goods_sysno){
        $sql = "SELECT goodsname FROM `".DB_PREFIX."base_goods` WHERE status = 1 AND  isdel = 0 AND sysno = '".intval($goods_sysno)."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     * 根据质量标准名称获取sysno
     * @param string $quality_no 质量标准名称
     * @return bool
     */
    public function getQualitySysno($quality_no){
        $sql = "SELECT sysno FROM `".DB_PREFIX."base_goods_quality` WHERE status = 1 AND  isdel = 0 AND qualityname = '".$quality_no."'";
        return $this->dbh -> select_one($sql);
    }

    /**
     * @param array $data
     * @return int
     */
    public function getStorageStock(array $data){
        $filter = array();
        if (isset($data['customerno_sysno']) && $data['customerno_sysno'] != '') {
            $filter[] = " customer_sysno='{$data['customerno_sysno']}'";
        }
        if (isset($data['goods_sysno']) && $data['goods_sysno'] != '') {
            $filter[] = " goods_sysno='{$data['goods_sysno']}'";
        }
        if (isset($data['quality_sysno']) && $data['quality_sysno'] != '') {
            $filter[] = " goods_quality_sysno='{$data['quality_sysno']}'";
        }

        $where ='iscurrent = 1 AND `status` = 1  AND isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT (sum(stockqty) -sum(checkqty)- sum(if(clockqty>0,clockqty,0))) num FROM `".DB_PREFIX."storage_stock`
                WHERE {$where}
                GROUP BY goods_sysno";

        return $this->dbh -> select_one($sql);
    }

    /**
     * 根据库存查询该库存所属的罐号
     * getStoragetankSysnoByStock
     * @author dxb
     * @param $sysno
     * @return int
     */
    public function getStoragetankSysnoByStock($sysno){
        $sql = "SELECT storagetank_sysno FROM `".DB_PREFIX."storage_stock` WHERE sysno =" .intval($sysno);
        $storageTankSysno = $this -> dbh -> select_one($sql);
        return $storageTankSysno ? $storageTankSysno : 0;
    }
}