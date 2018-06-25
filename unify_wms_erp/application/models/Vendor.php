<?php
/**
 *仓储表
 */

class VendorModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * VendorModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public  function getHengyangVendor()
    {
        $sql = "SELECT * FROM ".DB_PREFIX."vendor WHERE status = 1 LIMIT 1";

        return $this->dbh->select_row($sql);
    }
}