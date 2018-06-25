<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2016-11-11 13:41:02
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-11-11 14:13:17
 */
?>
<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#departmentlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
            	<label class="row-label">部门编号：</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no}}" placeholder="部门编号">
                </div>
                <label class="row-label">部门名称：</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name}}" placeholder="部门名称">
                </div>
                <label class="row-label">部门状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="1" >启用</option>
                        <option value="2">停用</option>
                    </select>
                </div>
                <div class="row-input text-right">
	                <div class="btn-group">
	                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
	                </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="departmentlist-table" data-selected-multi="true" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        dataUrl: '/department/listJson',
        editMode: {dialog:{width:'800',height:'300',title:'部门管理',mask:true}},
        editUrl: '/department/addandedit/id/{sysno}',
        afterSave:function(str, datas){ this.refresh();},
        afterDelete:function(str, datas){ this.refresh();},
        delUrl:'/department/deljson',
        delPK:'sysno',
        paging: false,
        filterThead:false,
        addLocation:'first',
        isTree: 'departmentname',
        treeOptions: {
            expandAll: false,
            add: false,
            simpleData: true,
            keys: {
                key:'sysno',
                parentKey: 'parent_sysno'
            }
        },
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'departmentno',align:'center',width:220}">部门编码</th>
                <th data-options="{name:'departmentname',align:'center',width:220}">部门名称</th>
                <th data-options="{name:'parent_departmentname',align:'center',width:220}">上级部门</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
                <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
                <th data-options="{name:'departmentdesc',align:'center'}">部门说明</th>
            </tr>
        </thead>
    </table>
</div>