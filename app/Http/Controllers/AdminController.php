<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthController;

class AdminController extends Controller
{
    public function index(){
        if (!AuthController::checkPermission()){
            return redirect('/login')->with('redirectPath', '/admin');
        }
        return CoursesController::viewAllCourses();
    }
}
