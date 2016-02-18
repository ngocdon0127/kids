<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::auth();

    Route::get('/admin', [
		'as' => 'admin',
		'uses' => 'AdminController@index'
	]);

	Route::get('/', [
		'as' => 'user.index',
		'uses' => 'PostsController@viewnewestposts'
	]);

	Route::get('/ajax/checkcoursetitle/{title}', [
		'as' => 'ajax.checkcoursetitle',
		'uses' => 'CoursesController@checkcoursetitle'
	]);

	// edit course {id}
	Route::get('/course/{id}/edit', [
		'as' => 'course.edit',
		'uses' => 'CoursesController@edit'
	]);

	// edit post {id}
	Route::get('/post/{id}/edit', [
		'as' => 'post.edit',
		'uses' => 'PostsController@edit'
	]);

	Route::put('/admin/editpost/{id}', [
		'as' => 'post.update',
		'uses' => 'PostsController@update'
	]);

	Route::get('/post/{postid}', [
		'as' => 'user.viewpost',
		'uses' => 'PostsController@viewpost'
	]);

	Route::get ('/question/{questionid}', [
		'as' => 'user.viewquestion',
		'uses' => 'QuestionsController@viewquestion'
	]);

	Route::get('question/{id}/edit', [
		'as' => 'question.edit',
		'uses' => 'QuestionsController@edit'
	]);

	// edit question {id} (Answers) // Will merge with 2 above routes later.
	Route::get('/answer/{questionid}/edit', [
		'as' => 'answer.edit',
		'uses' => 'AnswersController@edit'
	]);

	Route::put('/admin/editanswer/{questionid}', [
		'as' => 'answer.update',
		'uses' => 'AnswersController@update'
	]);

	// delete question {id}
	Route::delete('/question/{id}/delete', [
		'as' => 'question.destroy',
		'uses' => 'QuestionsController@destroy'
	]);

	Route::post('/timeonline', [
		'as' => 'count.timeonline',
		'uses' => 'TimesController@incTimeOnline'
	]);

	Route::post('/trackip', [
		'as' => 'count.ip',
		'uses' => 'TimesController@trackip'
	]);

	Route::get('/buy', [
		'as' => 'user.buy',
		'uses' => 'PaidsController@buy'
	]);

	Route::post('/finishexam', [
		'as' => 'count.score',
		'uses' => 'DoexamsController@savescore'
	]);

	Route::group(['prefix' => '/admin'], function(){
		Route::get ('/addquestion/{postid}',[
			'as'    => 'admin.addquestion',
			'uses'  => 'QuestionsController@addquestion'
		]);
		Route::post('/addquestion/{postid}',[
			'as'    => 'admin.savequestion',
			'uses'  => 'QuestionsController@savequestion'
		]);
		Route::get ('/addpost',[
			'as'    => 'admin.addpost',
			'uses'  => 'PostsController@addpost'
		]);
		Route::post('/addpost',[
			'as'    => 'admin.savepost',
			'uses'  => 'PostsController@savepost'
		]);
		Route::get ('/addcourse',[
			'as'    => 'admin.addcourse',
			'uses'  => 'CoursesController@addcourse'
		]);
		Route::post('/addcourse',[
			'as'    => 'admin.savecourse',
			'uses'  => 'CoursesController@savecourse'
		]);
		Route::get ('/addanswer/{questionid}',[
			'as'    => 'admin.addanswer',
			'uses'  => 'AnswersController@addanswer'
		]);
		Route::post('/addspace/{questionid}', [
			'as'    => 'admin.addspace',
			'uses'  => 'SpacesController@savespace'
		]);
		Route::post('/editspace/{questionid}', [
			'as'    => "admin.editspace",
			'uses'  => "SpacesController@update"
		]);
		Route::post('/addsubquestion/{questionid}', [
			'as'    => 'admin.addsubquestion',
			'uses'  => 'SubquestionsController@savesubquestion'
		]);
		Route::post('/editsubquestion/{questionid}', [
			'as'    => "admin.editsubquestion",
			'uses'  => "SubquestionsController@update"
		]);
		Route::post('/addanswer/{questionid}',[
			'as'    => 'admin.saveanswer',
			'uses'  => 'AnswersController@saveanswer'
		]);
		Route::put('/editcourse/{id}', [
			'as' => 'course.update',
			'uses' => 'CoursesController@update'
		]);
		Route::get ('/course/{courseid}', [
			'as' => 'admin.viewcourse',
			'uses' => 'CoursesController@adminviewcourse'
		]);
		Route::get ('post/{postid}/edit',[
			'as'    => 'admin.editpost',
			'uses'  => 'PostsController@edit'
		]);
		Route::get ('/post/{postid}/delete',[
			'as'    => 'admin.destroypost',
			'uses'  => 'PostsController@destroy'
		]);
		Route::get ('/question/{id}/delete',[
			'as'    => 'admin.destroyquestion',
			'uses'  => 'QuestionsController@destroy'
		]);
		Route::get ('/course/{id}/delete',[
			'as'    => 'admin.destroycourse',
			'uses'  => 'CoursesController@destroy'
		]);
		Route::put('/editquestion/{id}', [
			'as' => 'question.update',
			'uses' => 'QuestionsController@update'
		]);

		Route::post('/editquestion/{id}', [
			'as' => 'question.update',
			'uses' => 'QuestionsController@update'
		]);
	});

});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
    Route::get('/auth/facebook', [
		'as' => 'login.facebook',
		'uses' => 'Auth\AuthController@redirectToProvider'
	]);

	Route::get('/auth/google', [
		'as' => 'login.google',
		'uses' => 'Auth\AuthController@googleRedirectToProvider'
	]);
	Route::get('/fbcallback', [
		'as' => 'callback.facebook',
		'uses' => 'Auth\AuthController@handleProviderCallback'
	]);

	Route::get('/ggcallback', [
		'as' => 'callback.google',
		'uses' => 'Auth\AuthController@googleHandleProviderCallback'
	]);
});



Route::get('/search', [
	'as' => 'search',
	'uses' => 'PostsController@searchpostsbyhashtag'
]);

Route::get('/ajax/dic', [
	'as' => 'ajax.dic',
	'uses' => 'PageController@dic'
]);