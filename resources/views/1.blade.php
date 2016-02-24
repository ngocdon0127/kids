<div class="container">
	<script type="text/javascript" src="/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
	<div id="divimg"><img id="img" src="/test.jpg" alt="" class="img-responsive" /></div>
	<script>
		function ob (x) {
			return document.getElementById(x);
		}
		console.log(ob('divimg').clientHeight);
	</script>
</div>