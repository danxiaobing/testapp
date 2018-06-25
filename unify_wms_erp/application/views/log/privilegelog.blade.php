<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#privilegelog-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">模块</label>
                <div class="row-input">
                    <input type="text" name="bar_controller" value="{{$bar_controller or ''}}" placeholder="模块">
                </div>

                <label class="row-label">操作</label>
                <div class="row-input">
                    <input type="text" name="bar_action" value="{{$bar_action or ''}}" placeholder="操作">
                </div>
                <label class="row-label">操作人</label>
                <div class="row-input">
                    <input type="text" name="bar_realname" value="{{$bar_realname or ''}}" placeholder="操作人">
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                        <!-- <button type="reset" class="btn-orange" data-icon="times">重置</button> -->
                    </div>
                </div>
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="privilegelog-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: false,
        dataUrl: 'log/privilegelogJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:14},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'controller',align:'center'}">模块</th>
                <th data-options="{name:'action',align:'center'}">操作</th>
                <th  data-options="{name:'url',align:'left'}">url</th>
                <th data-options="{name:'user_realname',align:'center'}">操作人</th>
                <th data-options="{name:'loginip',align:'center'}">访问IP</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">操作时间</th>
            </tr>
        </thead>
    </table>
</div>
