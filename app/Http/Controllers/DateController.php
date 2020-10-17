<?php

namespace App\Http\Controllers;

use App\Services\HolidayChecker;
use Illuminate\Http\Request;

class DateController extends Controller
{
    public function submit(Request $req){
         $valid = $req->validate([
             'date' => 'required|date',
         ]);
         $result = HolidayChecker::check($valid);
        return view('result', compact('result'));
    }
}
