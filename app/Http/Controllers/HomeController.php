<?php

namespace App\Http\Controllers;

use App\Support\ProductQuery;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return view('home', ProductQuery::listing($request));
    }
}
