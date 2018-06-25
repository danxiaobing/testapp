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
        <h3>{{$vendor_name}}</h3>
        <!-- <div class="error text-center">
            <p>暂无数据，请联系管理员！</p>
        </div> -->
        <h5>出库信息</h5>
        <div class="app-one">
            <ul>
                <li><span>通知数量:</span><span><em>{{$tobeqty}}</em>吨</span></li>
                <li><span>已出库数量:</span><span><em>{{$beqty}}</em>吨</span></li>
                <li><span>待出库数量:</span><span><em>{{$daiti_beqty}}</em>吨</span></li>
            </ul>
        </div>
        <h5>车辆信息[{{$count}}]</h5>
        <div class="app-list">
            @foreach($carList as $value)
            <a href="/webapi/carOutPoundDetail/sysno/{{$value['sysno']}}">
                <dl>
                    <dt><span>{{$value['carid']}}</span><span class="text-center"><i>已出库</i></span><span class="text-right">{{$value['beqty']}} <i>kg</i></span></dt>
                    <dd><i>{{$value['carname'] ? $value['carname'] :  '--'}} {{$value['mobilephone'] ? $value['mobilephone'] :  '--'}}</i></dd>
                </dl>
                <p style="text-align: right;margin-top: -5px;margin-bottom: 15px; color: #0088cc;">查看详情</p>
            </a>
            @endforeach
        </div>
        <h5>{{$time}}</h5>
    </div>
</body>
</html>