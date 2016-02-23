<?php

namespace App\Http\Controllers;

use App\Answers;
use App\Subquestions;
use App\Questions;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SubquestionsController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\DB;

class SubquestionsController extends Controller
{
	public function saveSubQuestion($QuestionID, Request $request){
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = Request::capture()->all();
		// dd($data);
		$count = $data['numAnswer'];
		for ($i=0; $i < $count; $i++) {
			$subQ = $data['answer' . ($i + 1)];
			$SubQuestionID = DB::table('subquestions')->insertGetId([
				'QuestionID' => $QuestionID,
				'Question'   => $subQ,
				'created_at' => new \DateTime(),
				'updated_at' => new \DateTime()
			]);

			// dd($file);
			try {
				if ($request->hasFile('subquestion_photo_' . ($i + 1)) && $request->file('subquestion_photo_' . ($i + 1))->isValid()){
					$file = Request::capture()->file('subquestion_photo_' . ($i + 1));
					$sq = Subquestions::orderBy('id', 'desc')->first();
					
					if ($file != null){
					$sq->Photo = 'Subquestion_' . $QuestionID . '_' . $SubQuestionID . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
					$file->move(base_path() . '/public/images/imageSubquestion/', $sq->Photo);
					$sq->update();
					}
				}
				
			} catch (Exception $e) {
				
			}

			$answer = new Answers();
			$answer->SubQuestionID = $SubQuestionID;
			$answer->Detail = $data['ta_answer' . ($i + 1)];
			$answer->Logical = 1;
			$answer->save();
			try {
				if ($request->hasFile('answer_photo_' . ($i + 1)) && $request->file('answer_photo_' . ($i + 1))->isValid()){
					$a = Answers::orderBy('id', 'desc')->first();
					$file = Request::capture()->file('answer_photo_' . ($i + 1));
					if ($file != null){
						$a->Photo = 'Answer_SQ_' . $SubQuestionID . '_' . $a->id . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
						$file->move(base_path() . '/public/images/imageAnswer/', $a->Photo);
					}
					$a->update();
				}
				
			} catch (Exception $e) {
				
			}
		}
		return redirect(route('user.viewquestion', $QuestionID));
	}

	public function update($QuestionID){
		$question = Questions::find($QuestionID);
		$old_subquestions = Subquestions::where('QuestionID', '=', $QuestionID)->get()->toArray();
		foreach ($old_subquestions as $value) {
			SubquestionsController::destroy($value['id']);
		}
		$data = Request::capture()->all();
		$count = $data['numAnswer'];
		for ($i=0; $i < $count; $i++) { 
			$subQ = $data['answer' . ($i + 1)];
			$SubQuestionID = DB::table('subquestions')->insertGetId([
				'QuestionID' => $QuestionID,
				'Question'   => $subQ,
				'created_at' => new \DateTime(),
				'updated_at' => new \DateTime()
			]);
			$answer = new Answers();
			$answer->SubQuestionID = $SubQuestionID;
			$answer->Detail = $data['ta_answer' . ($i + 1)];
			$answer->Logical = 1;
			$answer->save();
		}
		return redirect(route('user.viewquestion', $QuestionID));
	}

	public static function destroy($id){
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$s = Subquestions::find($id);
		if (count($s) < 1){
			return redirect('/');
		}
		$answers = Answers::where('SubQuestionID', '=', $id)->delete();
		$s->delete();
	}
}
