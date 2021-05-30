<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;

class LedgerTransaction extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }


    function showBalance(){
        $allUser=User::all()
        return view('showbalance')->with('users',$allUser);
    }

    function getBalance(Request $request){
        $userId=
    }

    function addMoney(Request $request){

    }

    function transfer(Request $request){

    }
}
