<?php

namespace App\Http\Controllers;

use App\Answers;
use App\Http\Controllers\Auth\AuthController;
use App\Questions;
use App\Posts;
use App\Spaces;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PostsController;

class QuestionsController extends Controller
{
    protected static $imageQuestionPath = '/public/images/imageQuestion/';

	public function viewQuestion($QuestionID){
		$Question = Questions::find($QuestionID);
		if (count($Question) < 1){
			return view('errors.404');
		}
		$Question = $Question->toArray();
		$format = $Question['FormatID'];
		if ($format == 1){ // Multiple-choice Question
			$Answers = Answers::where('QuestionID', '=', $QuestionID)->get()->toArray();
			return view('viewquestion')->with(compact('Question', 'Answers'));
		}
		else if ($format == 2){ // Filled Question
			$Answers = array();
			$Spaces = Spaces::where('QuestionID', '=', $QuestionID)->get()->toArray();
			foreach ($Spaces as $value) {
				$Answers += array($value['id'] => Answers::where('SpaceID', '=', $value['id'])->get()->toArray());
			}
			// dd($Answers);
			return view('admin.viewfilledquestion')->with(compact('Question', 'Spaces', 'Answers'));
			}
			else if ($format == 3){ // Aranged Question
				$Answers = Answers::where('QuestionID', '=', $QuestionID)->get()->toArray();
				return view('admin.viewarangedquestion')->with(compact('Question', 'Answers'));
			}
	}

	public function addQuestion($PostID){
		if (!AuthController::checkPermission()){
			return redirect('/login');
		};
		if (array_key_exists('FormatID', $_GET)){
			switch ($_GET['FormatID']) {
				case 1:
					$view = 'admin.addquestion';
					break;
				case 2:
					$view = 'admin.addfilledquestion';
					break;
				case 3:
					$view = 'admin.addarangedquestion';
					break;
				default:
					$view = 'admin.addquestion';
					break;
			}
		}
		else {
			$view = 'admin.addquestion';
		}
		return view($view)->with(['PostID' => $PostID]);
	}
	
	public function saveQuestion($PostID){
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = Request::capture();
		$question = new Questions();
		$question->PostID = $PostID;
		$question->ThumbnailID = $data['ThumbnailID'];
		$question->Question = $data['Question'];
		$question->Description = $data['Description'];
		$question->FormatID = $data['FormatID'];
		$a = $data['Answer'];
		switch ($data['ThumbnailID']){
			case '1': // Photo
				$question->save();
				$question = Questions::orderBy('id', 'desc')->first();

				//Photo
				$file = Request::capture()->file('Photo');
				if ($file != null){
					$question->Photo = 'Question_' . $PostID . '_' . $question->id . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
					$file->move(base_path() . '/public/images/imageQuestion/', $question->Photo);
				}

				$question->update();
				break;
			case '2': // Video
				$linkVideo = $data['Video'];
				$question->Video = PostsController::getYoutubeVideoID($linkVideo);
				$question->save();
				break;
		}
		// var_dump($data);


		if ($question->FormatID == '3') {

			$answer = new Answers();
			$answer->Detail = $a;
			$answer->QuestionID = $question->id;
			$answer->save();
		}
		echo $question->id;
		return;
	}

	public function edit($id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$Question = Questions::find($id);
		$format = $Question['FormatID'];
		// dd($format);
		switch ($format) {
			case 1:			// Multiple-choices Question
				return view('admin.editquestion', compact('Question'));
				break;
			case 2:			// Filled Question
				$Spaces = Spaces::where('QuestionID', '=', $Question['id'])->get()->toArray();
				$rawAnswers = array();
				foreach ($Spaces as $key => $value) {
					$ra = '';
					$Answers = Answers::where('SpaceID', '=', $value['id'])->get()->toArray();
					foreach ($Answers as $key => $v) {
						$ra .= $v['Detail'] . "; ";
					}
					$rawAnswers = array_merge($rawAnswers, [$ra]);
				}
				return view('admin.editfilledquestion', compact('Question', 'rawAnswers'));
				break;
				case 3:
					return view('admin.editarangedquestion', compact('Question'));
					break;
		}
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = $request->all();
		$question = Questions::find($id);
		$question->Question = $data['Question'];
		$question->ThumbnailID = $data['ThumbnailID'];
		$question->Description = $data['Description'];
		$question->update();

		switch ($data['ThumbnailID']){
			case '1': // Photo
				// if admin upload new photo
				if ($request->file('Photo') != null) {
					$question = Questions::find($id);

					$file = $request->file('Photo');
					$question->Photo = 'Question_' . $question['PostID'] . '_' . $question->id . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
					$file->move(base_path() . '/public/images/imageQuestion/', $question->Photo);

					$question->update();
				}
				break;
			case '2':
				$question->Video = PostsController::getYoutubeVideoID($data['Video']);
				$question->update();
		}
		return redirect(route('user.viewquestion', $question->id));
	}

	public static function destroy($id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$question = Questions::find($id);
		@unlink(public_path('images/imageQuestion/' . $question['Photo']));
		$postid = $question['PostID'];
		$format = $question['FormatID'];
		if ($format == 1 || $format == 3){
			$answers = Answers::where('QuestionID', '=', $id)->get()->toArray();
			foreach ($answers as $answer) {
				Answers::destroy($answer['id']);
			}
		}
		else if ($format == 2){
			$spaces = Spaces::where('QuestionID', '=', $id)->get()->toArray();
			foreach ($spaces as $value) {
				SpacesController::destroy($value['id']);
			}
		}
		$question->delete();
		return redirect(route('user.viewpost', $postid));
	}
}
