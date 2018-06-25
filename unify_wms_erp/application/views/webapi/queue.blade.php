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
        <h5>排队状态：{{$car_queue_status}}</h5>
        <div class="app-one">
            <ul>
                <li><span>前面等待车辆:</span><span><em>{{$num}}</em>辆</span></li>
                <li><span>预计等待时间:</span><span><em>{{$queuetime}}</em>分钟&nbsp;<small>(仅供参考)</small></span></li>
            </ul>
        </div>
        <h5>作业明细</h5>
        <div>
            <ul>
                <li>单据类型：{{$doc_source}}</li>
                <li>车牌号码：{{$carid}}</li>
                <li>司机姓名：{{$carname}}</li>
                <li>联系方式：{{$mobilephone}}</li>
                <li>鹤位号码：{{$queueno}}</li>
                <li>货品名称：{{$goodsname}}</li>
                <li>作业吨数：{{$estimateqty}}吨</li>
                <li>地磅编号：{{$loadometer}}</li>
            </ul>
        </div>
        <h5>{{$time}}</h5>
    </div>
</body>
</html>