<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MypageController extends Controller
{
    public function getIndex() {
      return view('mypage.index');
    }
}
