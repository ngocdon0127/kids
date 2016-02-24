@extends('layouts.main')
@section('head.title')
	Kids
@endsection
@section('body.content')
	<div class="container">
		<img id="img" src="/images/imageAnswer/Answer_SQ_234_7019_-Evangels-English-www.evangelsenglish.com_.jpg" alt="" class="img-responsive" />
		<script>
			function ob (x) {
				return document.getElementById(x);
			}
			console.log(ob('img').clientHeight);
		</script>
	</div>

@endsection