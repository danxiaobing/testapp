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
        <h5>提货公司</h5>
        <div class="app-list">
            @foreach($poundsDetail as $value)
            <a href="/webapi/carOutDetail/sysno/{{$value['stockout_sysno']}}">
                <dl>
                    <dt><span>{{$value['customername']}}</span></dt>
                    <dd><span>提货公司：</span><span>{{$value['takegoodscompany']}}</span></dd>
                    <dd><span>提货单号：</span><span>{{$value['takegoodsno']}}</span></dd>

                </dl>
                <p style="text-align: right;margin-top: -5px;margin-bottom: 15px; color: #0088cc;">查看详情</p>
            </a>
            @endforeach
        </div>
        <h5>{{$time}}</h5>
    </div>
</body>
</html>