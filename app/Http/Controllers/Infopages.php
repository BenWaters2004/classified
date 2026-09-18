<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;

class InfoPages extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Infopages Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Infopages
    |
    */

    /**
     * index fallback
     *
     * 
     */

    public function index()
    {
        return redirect("/login");
        
    }

   
    public function privacyPolicy()
    {

        return view('infopages.privacyPolicy');
        
    }

    public function helpCenter()
    {

        return view('infopages.helpCenter');
        
    }

    public function helpCenterHelp()
    {

        return view('infopages.helpCenterHelp');
        
    }

}
