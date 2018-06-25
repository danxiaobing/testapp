<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="/static/web_css/app.css">
</head>
<body>
<div class="app-listbox">
    <!-- <div class="error text-center">
        <p>暂无数据，请联系管理员！</p>
    </div> -->
    <h3>{{$vendor_name}}</h3>
    <div class="app-detail">
        <span>{{$carid}}</span>
        <span class="text-center">已出库</span>
        <span class="text-right">{{round($beqty)}} kg</span>
    </div>
    <h5>基本信息</h5>
    <div>
        <ul>
            <li>单据类型：{{$poundsoutno}}</li>
            <li>装货日期：@if($fullcartime) {{date('Y-m-d', strtotime($fullcartime))}} @endif</li>
            <li>货品名称：{{$goodsname}}</li>
        </ul>
    </div>
    <h5>作业明细</h5>
    <div>
        <ul>
            <li>空车重量：{{round($emptycarqty)}} kg</li>
            <li>重车重量：{{round($fullcarqty)}} kg</li>
            <li>空车时间：{{$emptycartime}}</li>
            <li>重车时间：{{$fullcartime}}</li>
            <li>实际重量：{{round($beqty)}} kg</li>
            <li>鹤位编号：{{$cranename}}</li>
        </ul>
    </div>
    @if($pounds_detail)
    <h5>提货公司信息</h5>
    <div>
        @foreach($pounds_detail as $value)
        <ul>
            <li>货主名称：{{$value['customername']}}</li>
            <li>入库船名：{{$value['shipname']}}</li>
            <li>货物性质：{{$value['goodsnature']}}</li>
            <li>储罐区域：{{$value['guanqu']}}</li>
            <li>提货公司：{{$value['takegoodscompany']}}</li>
            <li>提货单号：{{$value['takegoodsno']}}</li>
            <li>提货数量：{{$value['detail_beqty']}} kg</li>
        </ul>
        @endforeach
    </div>
    @endif
    <h5>{{$time}}</h5>
</div>
</body>
</html>