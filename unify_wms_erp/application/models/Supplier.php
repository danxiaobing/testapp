<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class SupplierModel
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
 * 查询船舶列表
 * @author hanshutan
 */
    public function searchShipList($params)
    {

        $filter = array();
        if (isset($params['bar_shipcontact']) && $params['bar_shipcontact'] != '') {
            $filter[] = " p.shipcontact = {$params['bar_shipcontact']}";
        }
        if (isset($params['bar_shipno']) && $params['bar_shipno'] != '') {
            $filter[] = " p.shipno LIKE '%" . $params['bar_shipno'] . "%' ";
        }
        if (isset($params['bar_shipname']) && $params['bar_shipname'] != '') {
            $filter[] = " p.shipname LIKE '%" . $params['bar_shipname'] . "%' ";
        }
        if (isset($params['bar_company']) && $params['bar_company'] != '') {
            $filter[] = " p.company LIKE '%" . $params['bar_company'] . "%' ";
        }
        if (isset($params['bar_captain']) && $params['bar_captain'] != '') {
            $filter[] = " p.captain LIKE '%" . $params['bar_captain'] . "%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " p.`status`='{$params['bar_status']}'";
        }
        if (isset($params['shipname']) && $params['shipname'] != '') {
            $filter[] = " p.`shipname`='{$params['shipname']}'";
        }

        // $filter[] = "p.status=1";
        $filter[] = "p.isdel=0";

        $where = '1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."base_ship p where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "select p.* from ".DB_PREFIX."base_ship p where {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;
                else
                    $sql .= " order by  created_at desc ";

                $result['list'] = $this->dbh->select($sql);

            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "select p.* from ".DB_PREFIX."base_ship p where {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;
                else
                    $sql .= " order by  created_at desc ";

                $arr =  $this->dbh->select_page($sql);


                $result['list'] = $arr;
            }
        }

        return $result;
    }

    /**
     * 查询船舶是否存在
     * @author wu xianneng
     */
    public function searchShipisexist($params)
    {

        $filter = array();
        if (isset($params['bar_shipno']) && $params['bar_shipno'] != '') {
            $filter[] = " shipno = '{$params['bar_shipno']}'";
        }
        if (isset($params['bar_shipname']) && $params['bar_shipname'] != '') {
            $filter[] = " shipname LIKE '%" . $params['bar_shipname'] . "%' ";
        }
        if (isset($params['bar_company']) && $params['bar_company'] != '') {
            $filter[] = " company LIKE '%" . $params['bar_company'] . "%' ";
        }
        if (isset($params['bar_captain']) && $params['bar_captain'] != '') {
            $filter[] = " captain LIKE '%" . $params['bar_captain'] . "%' ";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '') {
            $filter[] = " isdel = {$params['bar_isdel']}";
        }

        if (isset($params['real_shipname']) && $params['real_shipname'] != '') {
            $filter[] = " shipname = '{$params['real_shipname']}'";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' OR ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."base_ship {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "select * from ".DB_PREFIX."base_ship {$where} ";

                $result['list'] = $this->dbh->select($sql);

            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num(1);
                $this->dbh->set_page_rows($params['pageSize'] );

                $sql = "select * from ".DB_PREFIX."base_ship {$where} ";

                $result['list'] =  $this->dbh->select_page($sql);

            }
        }

        return $result;
    }


    public function addShip($data) {
        if($data['shipname']){
            return $this->dbh->insert(DB_PREFIX.'base_ship', $data);
        }
        return false;
    }

    public function addShipAttach($id,$attach) {
        if(is_array($attach) && count($attach) > 0){
            $this->dbh->begin();
            try {
           //     $this->dbh->delete(DB_PREFIX.'base_ship-r-attach',  'ship_sysno=' . intval($id)); //不需要删除之前记录
                foreach ($attach as $aid){
                    $input = array(
                        'ship_sysno' => $id,
                        'attach_sysno' => $aid
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'base_ship-r-attach', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
                $this->dbh->commit();
                return true;

            } catch (Exception $e) {
                $this->dbh->rollback();
                return false;
            }

        }else{
            return false;
        }
    }

    public function getShipById($id = 0){
        $sql = "select p.* from ".DB_PREFIX."base_ship p where sysno = $id ";

        return $this->dbh->select_row($sql);
    }

    public function getShipAttachById($id = 0){
        $sql = "select a.* from `".DB_PREFIX."base_ship-r-attach` sa, ".DB_PREFIX."system_attach a  where sa.ship_sysno = $id and sa.attach_sysno = a.sysno ";

        return $this->dbh->select($sql);
    }

    public function updateShip($id = 0, $data = array()) {
        return $this->dbh->update(DB_PREFIX.'base_ship', $data, 'sysno=' . intval($id));
    }

    public function delShip($id) {
        $this->dbh->begin();
        try {
            $checkResult = $this->checkship($id);
            //此船没有被出入库预约单和出入库订单所占用才可以删除
            if ($checkResult) {
               $data = array(
                'isdel' => 1
                );
                $res = $this->dbh->update(DB_PREFIX.'base_ship', $data, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                // $res = $this->dbh->update(DB_PREFIX.'system_attach', $data, 'sysno in (select attach_sysno from `".DB_PREFIX."base_ship-r-attach` where ship_sysno=' . intval($id).')');
                // if (!$res) {
                //     $this->dbh->rollback();
                //     return false;
                // }

                $this->dbh->commit();
                return true; 
            }else{
                return false;
            }      
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /**
     * 根据id获得车辆细节
     * id: 权限id
     * @return 数组
     */
    public function getCarById($id = 0) {
        $sql = "select p.* from ".DB_PREFIX."base_car p where sysno = $id ";

        return $this->dbh->select_row($sql);
    }


    /**
     * 角色对应权限by视图
     */
    public function getCarViewPrivilege($carprivileges = array()) {
        $search = array();
        $privileges = $this->getAllPrivilege($search);
        $privilegesview = array();
        $module = array();

        foreach ($privileges as $privilege) {

            $privilege['check'] = false;
            /*            foreach ($carprivileges as $carprivilege) {
                            if ($carprivilege['privilege_sysno'] == $privilege['sysno']) {
                                $privilege['check'] = true;
                                break;
                            }
                        }*/

            $privilegesview[] = $privilege;

            if ($privilege['parent_sysno'] == 0) {
                $module[] = array('mval' => $privilege['privilegename'], 'msysno' => $privilege
                ['sysno'], 'check' => $privilege['check']);
            }
        }

        $out['privileges'] = $privilegesview;
        $out['module'] = $module;

        return $out;
    }

    /**
     * 根据条件显示角色列表
     * @return 数组
     */
    public function searchCar($params)
    {
        $filter = array();

        if (isset($params['bar_carid']) && $params['bar_carid'] != '') {
            $filter[] = " `carid` LIKE '%{$params['bar_carid']}%' ";
        }

        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " `carname` LIKE '%{$params['bar_name']}%' ";
        }

        if (isset($params['mobilephone']) && $params['mobilephone'] != '') {
            $filter[] = " `mobilephone` LIKE '%{$params['mobilephone']}%' ";
        }

        if (isset($params['idcard']) && $params['idcard'] != '') {
            $filter[] = " `idcard` LIKE '%{$params['idcard']}%' ";
        }

        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " `status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " `isdel`='{$params['bar_isdel']}'";
        }
        
        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_car` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);
        
        $result['list'] = array();

        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."base_car` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT * FROM `".DB_PREFIX."base_car` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);


                $result['list'] = $arr;
            }

        }
        return $result;
    }


    public function getcarsInfo(){
        $sql = "SELECT * FROM ".DB_PREFIX."base_car  WHERE `status` = 1 AND isdel = 0 ";

        return $this->dbh->select($sql);
    }

    //通过车牌获取车辆信息
    public function getcarInfoByCarId($carid){
        $sql = "SELECT status FROM ".DB_PREFIX."base_car  WHERE isdel = 0 and carid = '{$carid}' ";
        return $this->dbh->select_one($sql);
    }

    //检测出库预约中车辆信息是否存在 如果不存在则新增车辆信息
    public function checkCardata($params = array())
    {
        if (!is_array($params) || count($params) == 0) {
            return false;
        }
        $this->dbh->begin();
        try {
            foreach ($params as $key => $value) {
                $sql = "select * from  ".DB_PREFIX."base_car where carname = '{$value['carname']}' and carid = '{$value['carid']}'";
                $res = $this->dbh->select($sql);
                if (!$res) {
                    $input = array(
                        'carid'         =>  $value['carid'],
                        'carname'       =>  $value['carname'],
                        'mobilephone'   =>  $value['mobilephone'],
                        'idcard'        =>  $value['idcard'],
                        'carmarks'      =>  $value['carmarks'],
                        'status'        =>  1,
                        'created_at'    =>'=NOW()',
                        'updated_at'    =>'=NOW()'
                    );
                    if (isset($input['carname']) && isset($input['carid'])) {
                        $inputres = $this->dbh->insert(DB_PREFIX.'base_car', $input);
                        if (!$inputres) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                }
            }
            $this->dbh->commit();
            return true;
        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
        
        
    }
    /**
     * 所有权限
     */
    public function getAllPrivilege($params) {
        if (isset($params['bar_parentid']) && $params['bar_parentid'] != '-100') {
            $filter[] = "p.parent_sysno  = '" . $params['bar_parentid'] . "'";
        }

        $where = 'p.status = 1 and p.isdel = 0';

        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select p.*,(select privilegename from ".DB_PREFIX."system_privilege pp where pp.sysno =
p.parent_sysno) as parent_privilegename from ".DB_PREFIX."system_privilege p where {$where} ";

        return $this->dbh->select($sql);
    }

    public function addCar($data, $privileges = "") {
        $this->dbh->begin();
        try {
            //".DB_PREFIX."base_car update
            $res = $this->dbh->insert(DB_PREFIX.'base_car', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;


            if ($privileges !== "") {
                $privilegeArr = explode(",", $privileges);

                if (!empty($privilegeArr)) {
                    foreach ($privilegeArr as $value) {
                        $privilegesdata = array(
                            'role_sysno' => $id,
                            'privilege_sysno' => $value,
                        );
                    }

                }
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateCar($id = 0, $data = array(), $privileges = "") {
        $this->dbh->begin();
        try {
            $checkResult = $this->checkcar($id);
            //此船没有被出入库预约单和出入库订单所占用才可以删除

            if ($checkResult) {
                //".DB_PREFIX."base_car update
                $res = $this->dbh->update(DB_PREFIX.'base_car', $data, 'sysno=' . intval($id));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }


                if ($privileges !== "") {
                    $privilegeArr = explode(",", $privileges);

                    if (!empty($privilegeArr)) {
                        foreach ($privilegeArr as $value) {
                            $privilegesdata = array(
                                'role_sysno' => $id,
                                'privilege_sysno' => $value,
                            );

                        }

                    }
                }

                $this->dbh->commit();
                return true;        
            }else{
                return false;
            }
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateCarData($id = 0, $data = array()) {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'base_car', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return true;        
           
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //船批量启用|停用
    public function updateShipStatus($idArray = "")
    {   $this->dbh->begin();
        try{
            if(isset($idArray) &&  $idArray[0] == 'start'){
                unset($idArray[0]);
                $data = array(
                    'status' => 1
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_ship',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }
            }elseif(isset($idArray) &&  $idArray[0] == 'stop'){
                unset($idArray[0]);
                $data = array(
                    'status' => 0
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_ship',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }  
            }else{
                return false;
            }
            $this->dbh->commit();
            return true;  
        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }    
    }
    //车批量启用|停用
    public function updateCarStatus($idArray = "")
    {   $this->dbh->begin();
        try{
            if(isset($idArray) &&  $idArray[0] == 'start'){
                unset($idArray[0]);
                $data = array(
                    'status' => 1
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_car',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }
            }elseif(isset($idArray) &&  $idArray[0] == 'stop'){
                unset($idArray[0]);
                $data = array(
                    'status' => 2
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_car',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }  
            }else{
                return false;
            }
            $this->dbh->commit();
            return true;  
        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }    
    }
    /**
     * 车轴限重
     * @author zhaoshiyu
     */
    //查看车轴限重信息
     public function searchCarInfoList($params)
    {
        $where = 'ca.isdel=0';

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."base_car_axle ca where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "select ca.* from ".DB_PREFIX."base_car_axle ca where {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;
                else
                    $sql .= " order by  created_at desc ";

                $result['list'] = $this->dbh->select($sql);

            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "select ca.* from ".DB_PREFIX."base_car_axle ca where {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;
                else
                    $sql .= " order by  created_at desc ";

                $arr =  $this->dbh->select_page($sql);


                $result['list'] = $arr;
            }
        }

        return $result;
    }

    //删除车轴限重信息
    public function delCarinfo($id) {
        $this->dbh->begin();
        try {
            $data = array(
                'isdel' => 1
                );
                $res = $this->dbh->update(DB_PREFIX.'base_car_axle', $data, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                $this->dbh->commit();
                return true;     
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }
    //根据id获取车轴限重信息
    public function getCarinfoById($id = 0){
        $sql = "select ca.* from ".DB_PREFIX."base_car_axle ca where sysno = $id AND  isdel = 0";

        return $this->dbh->select_row($sql);
    }
    //添加车轴限重
    public function addCarinfo($data) {

        return $this->dbh->insert(DB_PREFIX.'base_car_axle', $data);
    }
    //编辑车轴限重
    public function updateCarinfo($id = 0, $data = array()) {
        return $this->dbh->update(DB_PREFIX.'base_car_axle', $data, 'sysno=' . intval($id));
    }
    //批量启用|停用车轴限重
    public function updateCarinfoStatus($idArray = "")
    {   $this->dbh->begin();
        try{
            if(isset($idArray) &&  $idArray[0] == 'start'){
                unset($idArray[0]);
                $data = array(
                    'status' => 1
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_car_axle',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }
            }elseif(isset($idArray) &&  $idArray[0] == 'stop'){
                unset($idArray[0]);
                $data = array(
                    'status' => 0
                    );
                $where = '('.implode(',', $idArray).')';
                $res = $this->dbh->update(DB_PREFIX.'base_car_axle',$data,"sysno in {$where}");
                if(!$res){
                    $this->$this->dbh->rollback();
                    return false;
                }  
            }else{
                return false;
            }
            $this->dbh->commit();
            return true;  
        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }    
    }

    //检测出入库预约单和出入库订单有没有使用该车辆
    private function checkcar($id='')
    {
        //入库预约单是否有此车
            $sql = "select * from ".DB_PREFIX."doc_booking_in_cars where `carid` = (select carid from ".DB_PREFIX."base_car where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result1 = $this->dbh->select($sql);

            //出库预约单是否有此车
            $sql = "select * from ".DB_PREFIX."doc_booking_out_cars where `carid` = (select carid from ".DB_PREFIX."base_car where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result2 = $this->dbh->select($sql);

            //入库定单是否有此车
            $sql = "select * from ".DB_PREFIX."doc_stock_in_cars where `carid` = (select carid from ".DB_PREFIX."base_car where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result3 = $this->dbh->select($sql);

            //出库订单是否有此车
            $sql = "select * from ".DB_PREFIX."doc_stock_out_cars where `carid` = (select carid from ".DB_PREFIX."base_car where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result4 = $this->dbh->select($sql);

            //此船没有被出入库预约单和出入库订单所占用才可以删除
            if (empty($result1) && empty($result2) && empty($result3) && empty($result4)){
                return true;
            }else{
                return false;
            }
    }
    private function checkship($id='')
    {
        //入库预约单是否有此船
            $sql = "select * from ".DB_PREFIX."doc_booking_in_detail where `shipname` = (select shipname from ".DB_PREFIX."base_ship where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result1 = $this->dbh->select($sql);

            //出库预约单是否有此船
            $sql = "select * from ".DB_PREFIX."doc_booking_out_detail where `shipname` = (select shipname from ".DB_PREFIX."base_ship where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result2 = $this->dbh->select($sql);

            //入库定单是否有此船
            $sql = "select * from ".DB_PREFIX."doc_stock_in_detail where `shipname` = (select shipname from ".DB_PREFIX."base_ship where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result3 = $this->dbh->select($sql);

            //出库订单是否有此船
            $sql = "select * from ".DB_PREFIX."doc_stock_out_detail where `shipname` = (select shipname from ".DB_PREFIX."base_ship where `sysno` = {$id} and `status`=1 and `isdel`=0) and `status`=1 and `isdel`=0";
            $result4 = $this->dbh->select($sql);

            if (empty($result1) && empty($result2) && empty($result3) && empty($result4)){
                return true;
            }else{
                return false;
            }
    }

    public function checkshipForApi($shipname)
    {
        //入库预约单是否有此船
        $sql = "SELECT shipname FROM ".DB_PREFIX."base_ship WHERE `status`=1 AND `isdel`=0 AND shipname = '".$shipname."'";
        $res = $this->dbh->select_one($sql);
        return $res ? $res : false;
    }

}