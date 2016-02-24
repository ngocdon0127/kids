@extends('layouts.main')
@section('head.title')
	Kids
@endsection
@section('body.content')
	<div class="container">
		<div id="divimg"><img id="img" src="/test.jpg" alt="" class="img-responsive" /></div>
		<script>
			function ob (x) {
				return document.getElementById(x);
			}
			console.log(ob('divimg').clientHeight);
		</script>
	</div>

@endsection