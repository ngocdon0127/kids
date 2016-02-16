<?php

namespace App\Http\Controllers;

use App\Answers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthController;

class SubQuestionsController extends Controller
{
	public function saveSubQuestion($QuestionID){
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = Request::capture();
		$count = $data['numAnswer'];
		for ($i=0; $i < $count; $i++) { 
			$subQ = $data['answer' + (i + 1)];
			$SubQuestionID = DB::table('subquestions')->insertGetId([
				'QuestionID' => $QuestionID,
				'Question'   => $subQ,
				'created_at' => new \DateTime(),
				'updated_at' => new \DateTime()
			]);
			$answer = new Answers();
			$answer->SubQuestionID = $SubQuestionID;
			$answer->Detail = $data['ta_answer' + (i + 1)];
			$answer->Logical = 1;
			$answer->save();
		}
		return redirect(route('user.viewquestion', $QuestionID));
	}
}
