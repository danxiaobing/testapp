<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="capacitylist-bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#capacitylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">查询罐号:</label>
                <div class="row-input">
                    <select name="storagetank_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($tanklist as $item)
                            <option value="{{$item['sysno']}}">{{$item['storagetankname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">片区:</label>
                <div class="row-input">
                    <select name="area_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($arealist as $item)
                            <option value="{{$item['sysno']}}">{{$item['areaname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">品种</label>
                <div class="row-input">
                    <select name="goods_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
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
    <table class="table table-bordered" id="capacitylist-table" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            gridTitle : '',
            showToolbar: true,
            toolbarItem: 'export',
            dataUrl: 'capacity/datail',
            dataType: 'json',
            jsonPrefix: 'obj',
            exportOption: {type:'file', options:{url:'/capacity/dbtoexcel', form:$('#capacitylist-bar') }},
            editMode: {dialog:{width:'1000',height:'400',title:'库容管理',mask:true}},
            delPK:'sysno',
            paging: {pageSize:13},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            fullGrid:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'areaname',align:'center'}">片区</th>
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
            <th data-options="{name:'storagetank_categoryname',align:'center'}">储罐材质</th>
            <th data-options="{name:'storagetanknature',align:'center',render:function(value)
                    {if(value=='1') {return '内贸罐'} else if(value=='2') {return '外贸罐'}
                    else if(value=='3') {return '保税罐'} } }">储罐性质</th>
            <th data-options="{name:'contractdate',align:'center'}">合同到期日</th>
            <th data-options="{name:'storagetankbg',align:'center',render:function(value){return value =='1' ? '是' : '否'}}">
                是否包罐
            </th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品种</th>
            <th data-options="{name:'onegoodslog',align:'center'}">上载1品种</th>
            <th data-options="{name:'twogoodslog',align:'center'}">上载2品种</th>
            <th data-options="{name:'threegoodslog',align:'center'}">上载3品种</th>
            <th data-options="{name:'actualcapacity',align:'center'}">可存放吨数</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'orderinqty',calc:'sum',align:'center'}">待入量</th>
            <th data-options="{name:'orderoutqty',calc:'sum',align:'center'}">待出量</th>
            <th data-options="{name:'tank_stockqty',calc:'sum',align:'center'}">现存量</th>
        </tr>
        </thead>
    </table>
</div>
