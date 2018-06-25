<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form  id="add-version-deail"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json " >
            <div class="bjui-row col-2">

                <label class="row-label">部门名称</label>
                <div class="row-input required">
<!--                 <input type="hidden" id="departmentname" name="departmentname" value="">
                    <select id="depart" name="department_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                        @foreach($data as $item)
                            <option value="{{ $item['sysno'] }}">{{ $item['departmentname']}}</option>
                        @endforeach
                    </select> -->
                <input type="text" name="departmentname" id="departmentname"  data-toggle="selectztree"  
                       data-tree="#j_select_tree2" readonly  value="{{$departmentname}}" data-rule="required">

                <input type="hidden" name="department_sysno" id="parentId" value="{{$department_sysno}}">
                <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true"
                    data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck"
                    data-on-click="S_NodeClick">
                    @foreach($data as $info)
                        <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}"
                            @if($info['sysno'] == $department_sysno ) data-checked='true' @endif >{{$info['departmentname']}}
                        </li>
                    @endforeach
                </ul>

                </div>

                <label class="row-label">备注</label>
                <div class="row-input">
                
                    <input type="text" name="memo" value="{{ $memo }}">
               
                </div>

            </div>
           

            
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
     <li><button type="button" class="btn-green" data-icon="save" onclick="saveReceipe()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
       
    </ul>
</div>


<script type="text/javascript">
    function saveReceipe() {
        // console.log($('#parentId').val());
        // var departmentname = $('#depart :selected').html();
        // $('#departmentname').val(departmentname);

        $('#add-version-deail').isValid(function(v){
            if (v) {
                var data  = $("#add-version-deail").serializeJson();
                if(data.bookin_detail_sysno =='')
                    data.bookin_detail_sysno = data.sysno;
                var allData  = $.CurrentNavtab.find("#sendversion-detail-table").data('allData');
                //    console.log(allData);
                var state = false;
                $.each(allData,function (index,domEle){
                    if(domEle.department_sysno == data.department_sysno){
                        BJUI.alertmsg('error','<h4>请勿重复添加评审部门!</h4>');
                        state = true;
                    }
                });

                if(state){
                    return false;
                }              
                // console.log(data);

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }


                $.CurrentNavtab.find('#sendversion-detail-table').datagrid('reload',  {data:allData});
                // $.CurrentDialog.find('closeCurrent');
                BJUI.dialog('closeCurrent', 'stockcarin-cars');
            }else{
                console.log('no');
            }

        });


    }


</script>