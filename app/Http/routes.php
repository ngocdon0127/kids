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

	Route::get('/', function () {
		return view('welcome');
	});

	Route::get('/ajax/checkcoursetitle/{title}', [
		'as' => 'ajax.checkcoursetitle',
		'uses' => 'CoursesController@checkcoursetitle'
	]);

	// edit course {id}
	Route::get('/course/{id}/edit', [
		'as' => 'course.edit',
		'uses' => 'CoursesController@edit'
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
	});

});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});

Route::get('/auth/facebook', [
	'as' => 'login.facebook',
	'uses' => 'Auth\AuthController@redirectToProvider'
]);

Route::get('/auth/google', [
	'as' => 'login.google',
	'uses' => 'Auth\AuthController@googleRedirectToProvider'
]);