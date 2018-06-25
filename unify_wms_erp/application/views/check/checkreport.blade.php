<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#checkreport-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">查询时间</label>
                <div class="row-input">
                    <input type="text" name="created_at" value="{{$created_at or ''}}" data-toggle="datepicker" placeholder="查询时间">
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="checkreport-table" data-toggle="datagrid" data-options="{
    tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#report_check_tb',
            addLocation: 'last',
            dataUrl: 'check/checkreportJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:20},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'created_at',align:'center',width:100}">生成时间</th>
            <th data-options="{name:'checknum',align:'center',width:100}">储罐盘点数量</th>
        </tr>
        </thead>
    </table>
</div>
<div id="report_check_tb">
    <button id="show_checkreport_btn" class="btn btn-blue"><i class="fa fa-eye" aria-hidden="true">&nbsp;&nbsp;查看</i></button>
    <button type="button" class="btn btn-green" data-icon="gavel"  id="report_check_btn">生成盘点表</button>
</div>
<script type="text/javascript">
    $("#show_checkreport_btn").click(function (){
        var data = $('#checkreport-table').data('selectedDatas');
//        console.log(data[0].sysno);
        if(data=='' || data==null) {
            BJUI.alertmsg('warn', '未选中任何行！');
        }else {
            BJUI.navtab({
                id: 'navab290',
                url: '/check/seereport/id/' + data[0].sysno,
                type: 'post',
                data: {'id': data[0].sysno},
                title: '盘点表明细'
            });
        }
    });

    $("#report_check_btn").click(function () {
            BJUI.dialog({
                id: 'navab290',
                url: '/check/report/',
                width:'800',
                height:'900',
                title: '生成盘点表'
            });
    });

</script>