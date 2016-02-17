@extends('layouts.main')
@section('head.title')
	{{$Post['Title']}} - Evangels English
@endsection
@section('body.content')
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1657402167852948";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<h2 class="title">{{$Post['Title']}}</h2>
	<h2 class="description">{{$Post['Description']}}</h2>
	<li class="list-group-item">
		@if ($Post['ThumbnailID'] == 1)
			<img class="img-responsive" alt="{{$Post['Title'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="{{'/images/imagePost/' . $Post['Photo']}}" />
		@elseif ($Post['ThumbnailID'] == 2)
		<div class="embed-responsive embed-responsive-4by3">
			<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$Post['Video']}}" frameborder="0" allowfullscreen></iframe>
		</div>
		@endif
	</li>
	@if ((auth()->user()) && (auth()->user()->admin >= App\ConstsAndFuncs::PERM_ADMIN))
		<a class ="col-xs-12 btn btn-primary" href="{{route('post.edit', $Post['id'])}}">Sửa thông tin bài đăng</a>
		<a class="col-xs-12 btn btn-primary" data-toggle="modal" href='#modal-add-question'>Thêm câu hỏi</a>

		<a class="col-xs-12 btn btn-danger" data-toggle="modal" href='#modal-id'>Xóa bài đăng này</a>
		<div class="modal fade" id="modal-id">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Cảnh báo:</h4>
					</div>
					<div class="modal-body">
						<h6>Xác nhận xóa?</h6>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<a class ="btn btn-primary" href="{{route('admin.destroypost',$Post['id'])}}">Xóa</a>
					</div>
				</div>
			</div>
		</div>

	@endif


	<div class="modal fade" id="modal-add-question">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Chọn dạng câu hỏi</h4>
				</div>
				<div class="modal-body">
					@foreach (App\ConstsAndFuncs::$FORMATS as $k => $v)
					<a class ="btn btn-primary" href="{{route('admin.addquestion', $Post['id'] . '?FormatID=' . $k)}}">{{$v}}</a>
					@endforeach
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript" charset="UTF-8">
		var score = 0;
		var fill = 0;
		var maxScore = {{$MaxScore}};
		function ob(x){
			return document.getElementById(x);
		}
		var numQuestion = {!! count($Questions) !!};
		function check(questionID, answerID, trueAnswerID, nextQuestionID){
			console.log('start');
			var date = new Date();
			var id = 'radio_answer_' + questionID + '_' + answerID;
			ob(id).checked = true;
			var id = 'answer_' + questionID + '_' + answerID;

//                ob(id).disabled = true;
			var setOfRadio = document.getElementsByName('question_' + questionID);
			for(i = 0; i < setOfRadio.length; i++){
				setOfRadio[i].disabled = true;
			}

			var setLi = document.getElementById('ul_question_' + questionID).children;
			for(i = 0; i < setLi.length; i++){
				var li = setLi[i];
				li.setAttribute('onclick', '');
				li.style.cursor = 'no-drop';
			}

			console.log('receive');
			var date1 = new Date();
			console.log(date1.getTime() - date.getTime())
//                        ob('answer_' + questionID + '_' + answerID).innerHTML = obj.responseText;

//                var xml = jQuery.parseXML(obj.responseText);
//                        console.log(xml.getElementsByTagName('logical')[0].innerHTML);
			if (trueAnswerID == answerID) {
				ob(id).style.background = '#66ff66';
				score++;
			}
			else {
				ob(id).style.background = '#ff5050';
			}
			var idTrue = 'answer_' + questionID + '_' + trueAnswerID;
			console.log(idTrue);
			ob(idTrue).style.background = '#66ff66';
			fill++;
			if (fill >= maxScore){

				var resultText = 'Đúng ' + score + '/' + maxScore + ' câu.\n';
				var x = {!! $Comments !!};
				for(var i = x.length - 1; i >= 0; i--) {
					if (Math.floor(score / maxScore * 100) >= x[i]['min']){
						resultText += x[i]['comment'];
						break;
					}
				}
				ob('writeResult').innerHTML = resultText;
				ob('resultText').style.display = 'block';
				$('html, body').animate({
					scrollTop: $("#resultText").offset().top
				}, 1000);

				// console.log('diem: ' + score);
				// save result using AJAX
				$.ajax({
					url: "/finishexam",
					type: "POST",
					beforeSend: function (xhr) {
						var token = $('meta[name="_token"]').attr('content');

						if (token) {
							return xhr.setRequestHeader('X-CSRF-TOKEN', token);
						}
					},
					data: {
						Score:  score,
						MaxScore: maxScore,
						token: ob('token').value
					},
					success: function (data) {
						console.log(data);
					}, error: function (data) {
						console.log(data);
					}
				}); //end of ajax

			}
			else{
				var delayToNextQuestion = 500;      // Time for user review current question.
				var timeScrollToNextQuestion = 300;
				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $("#title_question_" + nextQuestionID).offset().top
					}, timeScrollToNextQuestion);
				}, delayToNextQuestion);
			}

		}

		var t = '';
		function gText(e) {
			t = (document.all) ? document.selection.createRange().text : document.getSelection();
			// console.log(t.length);
			// if (t.length > 0)
				// alert(t);
				ob('inputDictionary').value = t;
				console.log(ob('inputDictionary').value);
				t = ob('inputDictionary').value;
				if (t.length <= 0){
					return;
				}
				ob('divDictionary').innerHTML = 'Searching...';
				$('#modal-id-dictionary').modal();
				$.ajax({
					type: 'GET',
					url: "{{route('ajax.dic')}}",
					beforeSend: function(xhr){
						var token = $('meta[name="_token"]').attr('content');

						if (token) {
							return xhr.setRequestHeader('X-CSRF-TOKEN', token);
						}
					},
					data: {word: ob('inputDictionary').value.toLowerCase().trim()},
					success: function (data) {
						var d = JSON.parse(data);
						// console.log(d.meanings);
						var ulMeanings = document.createElement('ul');
						for(var i = 0; i < d.meanings.length; i++){
							var liMeanings = document.createElement('li');
							liMeanings.innerHTML = d.meanings[i];
							ulMeanings.appendChild(liMeanings);
						}
						var ulExamples = document.createElement('ul');
						for(var i = 0; i < d.examples.length; i++){
							var liExamples = document.createElement('li');
							liExamples.innerHTML = d.examples[i];
							ulExamples.appendChild(liExamples);
						}
						var divDictionary = ob('divDictionary');
						divDictionary.innerHTML = '';
						var pMeanings = document.createElement('p');
						pMeanings.innerHTML = 'Meaning of "' + t.trim() + '" : ';
						divDictionary.appendChild(pMeanings);
						divDictionary.appendChild(ulMeanings);
						var divtmp = document.createElement('div');
						divtmp.setAttribute('class', 'clear');
						divDictionary.appendChild(divtmp);
						var pExamples = document.createElement('p');
						pExamples.innerHTML = 'Examples for "' + t.trim() + '" : ';
						divDictionary.appendChild(pExamples);
						divDictionary.appendChild(ulExamples);
						if (window.getSelection) {
							if (window.getSelection().empty) {  // Chrome
							window.getSelection().empty();
						}
						else if (window.getSelection().removeAllRanges) {  // Firefox
							window.getSelection().removeAllRanges();
						}
						}
						else if (document.selection) {  // IE?
							document.selection.empty();
						}
					}, error: function () {
						console.log("error!!!!");
					}
				});
		}

		document.onmouseup = gText;
		if (!document.all) document.captureEvents(Event.MOUSEUP);

	</script>
	<ul id="form_test" class="list-group">
		<input id='token' type="text" value="{{$Token}}" style="display: none;" readonly />
		<?php $count_answer=1;?>
		@foreach($Questions as $key => $q)
			@if ((auth()->user()) && (auth()->user()->admin >= App\ConstsAndFuncs::PERM_ADMIN))
				<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><h3 onmouseover="this.style.color = '#f06'" onmouseout="this.style.color = '#60c'" class="title" id="title_question_{!! $key + 1 !!}">Câu hỏi số <?php echo $count_answer++; ?>:</h3></a>
			@else
			<h3 class="title" id="title_question_{!! $key + 1 !!}">Câu hỏi số <?php echo $count_answer++; ?>:</h3>
			@endif

			<!-- Trắc nghiệm -->
			@if ($q['FormatID'] == 1)
				<h4 class="title">{!! nl2br($q['Question']) . ((strlen($q['Description']) > 0) ? (" :<br /><br /> " . nl2br($q['Description'])) : "") !!}</h4>
					@if ($q['ThumbnailID'] == 1)
						@if ($q['Photo'] != null)
							<li class="list-group-item list-group-item-info">
								@if ((auth()->user()) && (auth()->user()->admin >= App\ConstsAndFuncs::PERM_ADMIN))
									<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
								@else
									<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
								@endif
							</li>
						@endif
					@elseif ($q['ThumbnailID'] == 2)
						@if ($q['Video'] != null)
							<div class="embed-responsive embed-responsive-4by3">
							<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$q['Video']}}" frameborder="0" allowfullscreen></iframe>
							</div>
						@endif
					@endif
				
				<ul class="list-group" id="ul_question_{{$q['id']}}">
					@foreach($AnswersFor1[$q['id']] as $k => $a)
						<li id="answer_{{$q['id']}}_{{$a['id']}}" class="list_answer"  onclick="check({{$q['id']}}, {{$a['id']}}, {{ $TrueAnswersFor1[$q['id']]}}, {!! $key + 2 !!})" style="cursor: pointer">
							<input type="checkbox" id="radio_answer_{{$q['id']}}_{{$a['id']}}" name="question_{{$q['id']}}"/>
							<span class="answer_content">{!! \App\Http\Controllers\AnswersController::underline($a['Detail']) !!}</span>
						</li>

						<div class="clear"></div>
					@endforeach
				</ul>
			<!-- End of Trắc nghiệm -->
			@elseif ($q['FormatID'] == 2)
			<!-- Điền từ -->
				@if ($q['ThumbnailID'] == 1)
					@if ($q['Photo'] != null)
						<li class="list-group-item list-group-item-info">
							@if ((auth()->user()) && (auth()->user()->admin == 1))
								<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
							@else
								<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
							@endif
						</li>
					@endif
				@elseif ($q['ThumbnailID'] == 2)
					@if ($q['Video'] != null)
						<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$q['Video']}}" frameborder="0" allowfullscreen></iframe>
						</div>
					@endif
				@endif
				<?php
					$subP = \App\Questions::getFilledQuestion($q['Question']);
					reset($Spaces);  // don't know what's different between this view & viewfilledquestion
				?>
				<div style="color:#cc0066; font-weight:bold;">
				@if (strlen($q['Description']) > 0)
					{!! nl2br($q['Description']) . ":" !!}
				@endif
				</div>
				<div>
					@foreach ($subP as $value)
						{!! nl2br($value) !!}
						@if (count($Spaces[$q['id']]) > 0)
						<select style="color:#cc0066" id="select_space_{{current($Spaces[$q['id']])['id']}}" data-show-icon="true">
							<?php 
								$this_answers = $AnswersFor2[current($Spaces[$q['id']])['id']];
							?>
							@foreach ($this_answers as $a)
							<option class="option_space_{{$a['Logical']}}" value="{{$a['Logical']}}">{!! $a['Detail'] !!}</option>
							@endforeach
						</select>

						<!-- change normal select into BS3 select manually-->
						<script type="text/javascript">
							bsselect("select_space_{{current($Spaces[$q['id']])['id']}}");
						</script>
						<?php array_shift($Spaces[$q['id']]) ?>
						@endif
					@endforeach
				</div>
			<!-- End of Điền từ -->
			@elseif ($q['FormatID'] == 5)
			<!-- Nối -->
				@if ($q['ThumbnailID'] == 1)
					@if ($q['Photo'] != null)
						<li class="list-group-item list-group-item-info">
							@if ((auth()->user()) && (auth()->user()->admin == 1))
								<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
							@else
								<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
							@endif
						</li>
					@endif
				@elseif ($q['ThumbnailID'] == 2)
					@if ($q['Video'] != null)
						<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$q['Video']}}" frameborder="0" allowfullscreen></iframe>
						</div>
					@endif
				@endif
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-bottom: 15px;">
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<ul id="ul_subquestions_{{$q['id']}}" class="sortable">
								@foreach($subquestions as $s)
									<li id="li_subquestion_{{$s['id']}}" class="ui-state-default li-connected form-control">{{$s['Question']}}</li>
								@endforeach
							</ul>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<ul class="sortable">
							<?php shuffle($AnswersFor5) ?>
							@foreach($AnswersFor5 as $s)
								<li class="ui-state-default li-connected form-control" id="li_subquestion_answer_{{$s['SubQuestionID']}}">{{$s['Detail']}}</li>
							@endforeach
						</ul>
					</div>
					</div>
				</div>
				<!--<script type="text/javascript" src="/js/jquery/jquery.mobile-1.4.5.min.js"></script>-->
				<script>
					$(document).bind('pageinit', function() {
						$( ".sortable" ).sortable();
						$( ".sortable" ).disableSelection();
						//<!-- Refresh list to the end of sort to have a correct display -->
						$( ".sortable" ).bind( "sortstop", function(event, ui) {
							$('.sortable').listview('refresh');
						});
					});
				</script>
			<!-- End of Nối -->
			@elseif ($q['FormatID'] == 6)
			<!-- Kéo thả -->
				@if ($q['ThumbnailID'] == 1)
					@if ($q['Photo'] != null)
						<li class="list-group-item list-group-item-info">
							@if ((auth()->user()) && (auth()->user()->admin == 1))
								<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
							@else
								<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
							@endif
						</li>
					@endif
				@elseif ($q['ThumbnailID'] == 2)
					@if ($q['Video'] != null)
						<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$q['Video']}}" frameborder="0" allowfullscreen></iframe>
						</div>
					@endif
				@endif
				<ul id="ul_subquestions_{{$q['id']}}" class="sortable" style="margin-top: 20px">
					<?php shuffle($AnswersFor6[$q['id']]) ?>
					@foreach($AnswersFor6[$q['id']] as $a)
						<li id="li_dragdrop_{{$a['id']}}" class="ui-state-default li-dragdrop form-control">{{$a['Detail']}}</li>
					@endforeach
				</ul>
			<!-- End of Kéo thả -->
			@endif

			@if($q['FormatID'] == 3)
				<h3>{{$q['Question']}}</h3>
				@if ($q['Photo'] != null)
						<li class="list-group-item list-group-item-info">
							@if ((auth()->user()) && (auth()->user()->admin == 1))
								<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
							@else
								<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
							@endif
						</li>
					@endif
					<h4>Nhập câu trả lời:</h4>
				<input type="text" name="" id="{{$q['id']}}" class="form-control" value="" placeholder="Input here..." required="required" pattern="" title="Nhập câu trả lời">
			@endif
			@if($q['FormatID'] == 4)
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border: #ecf0f1 solid 1px;">
						<h2 class="title">{{$q['Question']}}</h2>
						<input type="text" name="" id="{{$q['id']}}" class="form-control" value="" placeholder="Input here..." required="required" pattern="" title="Nhập câu trả lời">				
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						@if ($q['Photo'] != null)
								@if ((auth()->user()) && (auth()->user()->admin == 1))
									<a style="text-decoration: none;" href="{{route('user.viewquestion', $q['id'])}}"><img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" /></a>
								@else
									<img class="img-responsive" alt="{{$q['Question'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imageQuestion/{{$q['Photo']}}" />
								@endif
						@endif
					</div>							
				</div>
			@endif
		@endforeach
	</ul>
	<button class="btn btn-primary" onclick="nopBai()">Nộp bài</button>
	<script>
		function nopBai(){
			checkFilledQuestions();
			checkConnectedQuestions();
			checkDragDropQuestion();
		}
	</script>
	@if (($DisplayedQuestions >= 0) && ($DisplayedQuestions < $NumOfQuestions))
		<p>Bạn đang xem {{$DisplayedQuestions . "/" . $NumOfQuestions}} câu hỏi của bài này.</p>
		<a href="{{route('user.buy')}}" class="btn btn-info">Purchase to see full post</a>
	@endif
	<script type="text/javascript">
		$('div[class="btn-group bootstrap-select"').css("width","auto");
		function checkFilledQuestions(){
			var setOfSpaces = {!! json_encode($SetOfSpaceIDs) !!};
			for (var i = 0; i < setOfSpaces.length; i++) {
				var selectObj = $('#select_space_' + setOfSpaces[i]);

				// bootstrap-select will be hided; a button with data-id attribute equals to id of old bootstrap-select will be added and shown.
				var btn = $('button[data-id="select_space_' + setOfSpaces[i] + '"]');
				if (selectObj.val() == 1){
					score++;
					btn.css('background', "#66ff66");
				}
				else{
					btn.css('background', "#ff5050");
				}
			};
			var resultText = 'Đúng ' + score + '/' + maxScore + ' câu.\n';
			var x = {!! $Comments !!};
			for(var i = x.length - 1; i >= 0; i--) {
				if (Math.floor(score / maxScore * 100) >= x[i]['min']){
					resultText += x[i]['comment'];
					break;
				}
			}
			ob('writeResult').innerHTML = resultText;
			ob('resultText').style.display = 'block';
			$('html, body').animate({
				scrollTop: $("#resultText").offset().top
			}, 1000);
			var setOfOptions = document.getElementsByClassName('option_space_1');
			for (var i = 0; i < setOfOptions.length; i++) {
				setOfOptions[i].innerHTML += ' <span class="glyphicon glyphicon-ok">';
			}

			$.ajax({
				url: "/finishexam",
				type: "POST",
				beforeSend: function (xhr) {
					var token = $('meta[name="_token"]').attr('content');

					if (token) {
						return xhr.setRequestHeader('X-CSRF-TOKEN', token);
					}
				},
				data: {
					Score:  score,
					MaxScore: maxScore,
					token: ob('token').value
				},
				success: function (data) {
					console.log(data);
				}, error: function (data) {
					console.log(data);
				}
			}); //end of ajax
		}

		function checkConnectedQuestions() {
			
		}
		function checkDragDropQuestion(){
			
		}
	</script>
	<div class="form-control" id="resultText" style="display: none; height: 200px;">
		<b class="title" id="writeResult"></b> <br />
	</div>
	<ul class="pager">
		@if ($PreviousPost != null)
			<li class="previous"><a href="{{route('user.viewpost', $PreviousPost)}}">Previous post</a></li>
		@endif
			<a id="toTop" href="#" style="float:right"></a>
		@if ($NextPost != null)
			<li class="next"><a href="{{route('user.viewpost', $NextPost)}}">Next post</span></a></li>
		@endif
	</ul>
	<div class="fb-comments" data-href="{!! 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']!!}" data-width="500" data-numposts="5"></div>
	<div class="fb-like" data-href="{!! 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']!!}" data-width="450" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
	<input type="hidden" id="inputDictionary" value="tmp">
	<div class="modal fade" id="modal-id-dictionary">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Quick Translation</h4>
				</div>
				<div class="modal-body" id="divDictionary">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('body.navright')
	<div class="panel panel-default xxx">
		<div class="panel-heading">
			Bài đăng cùng khóa
		</div>
		<div class="panel-body" id="div_right_bar">
		@foreach($newpost as $np)
			<a id="a_smallLink_{{$np['id']}}" style="text-decoration: none;" href="{{route('user.viewpost', $np['id'])}}">
				<blockquote>
					@if($np['ThumbnailID'] == '1')
						<img class="img-responsive" alt="{{$np['Title'] . ' - Evangels English - '}}{{$_SERVER['HTTP_HOST']}}" src="/images/imagePost/{{$np['Photo']}}" />
					@elseif($np['ThumbnailID'] == '2')
					<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$np['Video']}}" frameborder="0" allowfullscreen></iframe>
					</div>
					@endif
					<h4>{{$np['Title']}}</h4>
					<h6>{{$np['Description']}}</h6>
				</blockquote>
			</a>
		@endforeach
		</div>
	</div>
@endsection
@section('head.css')
	<link rel="stylesheet" href="/js/jquery/jquery-ui.css">
	<script src="/js/jquery/jquery.js"></script>
	<script src="/js/jquery/jquery-ui.min.js"></script>
	<script src="/js/jquery/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
	<script>$('.sortable').draggable();</script>
	<style>
		.sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
		.li-connected {
			height: 75px;
			cursor: pointer;
		}
		.li-dragdrop{
			list-style-type: none;
			margin: 20;
			padding: 20;
			width: auto;
			display: inline;
			cursor: pointer;
		}
	</style>
	<script>
		jQuery(function() {
			jQuery( ".sortable" ).sortable();
			jQuery( ".sortable" ).disableSelection();
		});
		function bsselect(x){
			$("#" + x).selectpicker();
		}
		$.noConflict();
	</script>
@endsection