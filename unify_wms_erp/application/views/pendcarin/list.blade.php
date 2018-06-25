<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#pendcarinlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间：</label>
                <div class="row-input datawidth">
                    <input type="text" name="startDate" value="" data-toggle="datepicker" data-rule="date" placeholder="开始时间" />
                </div>
                 <div class="row-input datawidth">
                    <input type="text" name="endDate" value="" data-toggle="datepicker" data-rule="date" placeholder="结束时间" />
                 </div>
                <label class="row-label">客户：</label>
                <div class="row-input">
                    <input type="hidden" id="pendcustomername" name="customername" value="">
                    <select id="pendcusid" name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" name="carid" placeholder="车牌号">
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
    <table class="table table-bordered" id="pendcarinlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        toolbarCustom:'#pendcarin_list_tb',
        showToolbar: true,
        addLocation: 'last',
        dataUrl: 'pendcarin/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        editMode:false,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'stockinno',align:'center'}">入库订单号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(value=='请选择'){return '--'}else{return value}}}">客服专员</th>
            
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'detail_sysno',align:'center',hide:true}">入库详情id</th>
        </tr>
        </thead>
    </table>
</div>
<div id="pendcarin_list_tb">
    <button type="button" id="pendcarin" class="btn btn-green" data-icon="plus">生成入库磅码单</button>
</div>
<script type="text/javascript">
    $("#pendcusid").change(function (){
        $("#pendcustomername").val($("#pendcusid option:selected").text())
    });

    $('#pendcarin').click(function(){
    var checkdata=$('#pendcarinlist-table').data('selectedDatas');
    if(checkdata && checkdata.length>0){
        if(checkdata.length>1){
            BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
            return;
        }
        var id = checkdata[0].sysno;
        var carid = checkdata[0].carid;
        BJUI.navtab({
            id:'pendcarin_edit',
            url:'/pendcarin/edit/'+Math.floor(Math.random()*100+1),
            type:'POST',
            data:{id:id,carid:encodeURI(carid)},
            title:'车入库核单'
        });
    }else{
        BJUI.alertmsg('warn','<h4>未选择数据</h4>');
    }
});


    // console.log('               ii.                                         ;9ABH, ');
    // console.log('              SA391,                                    .r9GG35&G ');
    // console.log('              &#ii13Gh;                               i3X31i;:,rB1 ');
    // console.log('              iMs,:,i5895,                         .5G91:,:;:s1:8A  ');
    // console.log('               33::::,,;5G5,                     ,58Si,,:::,sHX;iH1  ');
    // console.log('                Sr.,:;rs13BBX35hh11511h5Shhh5S3GAXS:.,,::,,1AG3i,GG   ');
    // console.log('                .G51S511sr;;iiiishS8G89Shsrrsh59S;.,,,,,..5A85Si,h8   ');
    // console.log('               :SB9s:,............................,,,.,,,SASh53h,1G.  ');
    // console.log('            .r18S;..,,,,,,,,,,,,,,,,,,,,,,,,,,,,,....,,.1H315199,rX,  ');
    // console.log('          ;S89s,..,,,,,,,,,,,,,,,,,,,,,,,....,,.......,,,;r1ShS8,;Xi  ');
    // console.log('        i55s:.........,,,,,,,,,,,,,,,,.,,,......,.....,,....r9&5.:X1  ');
    // console.log('       59;.....,.     .,,,,,,,,,,,...        .............,..:1;.:&s  ');
    // console.log('      s8,..;53S5S3s.   .,,,,,,,.,..      i15S5h1:.........,,,..,,:99   ');
    // console.log('      93.:39s:rSGB@A;  ..,,,,.....    .SG3hhh9G&BGi..,,,,,,,,,,,,.,83   ');
    // console.log('      G5.G8  9#@@@@@X. .,,,,,,.....  iA9,.S&B###@@Mr...,,,,,,,,..,.;Xh  ');
    // console.log('      Gs.X8 S@@@@@@@B:..,,,,,,,,,,. rA1 ,A@@@@@@@@@H:........,,,,,,.iX: ');
    // console.log('     ;9. ,8A#@@@@@@#5,.,,,,,,,,,... 9A. 8@@@@@@@@@@M;    ....,,,,,,,,S8  ');
    // console.log('     X3    iS8XAHH8s.,,,,,,,,,,...,..58hH@@@@@@@@@Hs       ...,,,,,,,:Gs  ');
    // console.log('    r8,        ,,,...,,,,,,,,,,.....  ,h8XABMMHX3r.          .,,,,,,,.rX: ');
    // console.log('   :9, .    .:,..,:;;;::,.,,,,,..          .,,.               ..,,,,,,.59 ');
    // console.log('  .Si      ,:.i8HBMMMMMB&5,....                    .            .,,,,,.sMr ');
    // console.log('  SS       :: h@@@@@@@@@@#; .                     ...  .         ..,,,,iM5 ');
    // console.log('  91  .    ;:.,1&@@@@@@MXs.                            .          .,,:,:&S ');
    // console.log('  hS ....  .:;,,,i3MMS1;..,..... .  .     ...                     ..,:,.99 ');
    // console.log('  ,8; ..... .,:,..,8Ms:;,,,...                                     .,::.83 ');
    // console.log('   s&: ....  .sS553B@@HX3s;,.    .,;13h.                            .:::&1 ');
    // console.log('    SXr  .  ...;s3G99XA&X88Shss11155hi.                             ,;:h&, ');
    // console.log('     iH8:  . ..   ,;iiii;,::,,,,,.                                 .;irHA  ');
    // console.log('      ,8X5;   .     .......                                       ,;iihS8Gi');
    // console.log('         1831,                                                 .,;irrrrrs&@');
    // console.log('           ;5A8r.                                            .:;iiiiirrss1H');
    // console.log('             :X@H3s.......                                .,:;iii;iiiiirsrh');
    // console.log('              r#h:;,...,,.. .,,:;;;;;:::,...              .:;;;;;;iiiirrss1');
    // console.log('             ,M8 ..,....,.....,,::::::,,...         .     .,;;;iiiiiirss11h');
    // console.log('            8B;.,,,,,,,.,.....          .           ..   .:;;;;iirrsss111h');
    // console.log('            i@5,:::,,,,,,,,.... .                   . .:::;;;;;irrrss111111');
    // console.log('            9Bi,:,,,,......                        ..r91;;;;;iirrsss1ss1111');
    
</script>