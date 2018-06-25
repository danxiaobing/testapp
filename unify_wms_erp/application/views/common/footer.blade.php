<div class="clear"></div>
<!--footer start-->
<div class="container text-center">
	<div class="login_footer">
		<a href="/about">关于我们</a><span>|</span>
		<a href="/contact">联系我们</a><span>|</span>
		<a href="/userGuideTrade">交易流程说明</a><span>|</span>
		<a href="/userGuidePublish">发布供求信息</a><span>|</span>
		<a href="/userGuideCertify">开通交易权限</a><span>|</span>
		<a href="/culture">企业文化</a><span>|</span>
		<a href="/honor">荣誉资质</a><span>|</span>
		<a href="/mediaReport">企业大事记</a>
		<h5>copyright © 2015-2018 上海国烨跨境电子商务有限公司</h5>
	</div>

</div>
<!--footer end-->
<!-- <div class="clear"></div> -->
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>

@if(isset($extra_js))
	@foreach($extra_js as $js)
		<script type="text/javascript" src="{{$js}}?v=1.1.0"></script>
	@endforeach
@endif
</html>
