<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Http\Request;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ],
        [
            'email.unique'=>'The :attribute value :input is already used.'
        ]
    );
        if ($validator->fails()) {
            $responseArr=array('status'=>false,'code'=>'201','message'=>$validator->errors());
            return response()->json($responseArr);
        }else{
            $data = $request->all();
            $data['account']=$this->generate_string(15);
            $data['password']=Hash::make($data['password']);
            $user = User::create($data);
            $token = $user->createToken('API Token')->accessToken;

            return response()->json([ 'user' => $user, 'token' => $token,'status'=>true,'code'=>'200']);
        }
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ],[
            'email.exists'=>'The :attribute value :input is not found.'
        ]);

        if ($validator->fails()) {
            $responseArr=array('status'=>false,'code'=>'201','message'=>$validator->errors());
            return response()->json($responseArr);
        }else{
            $user= User::where('email', $request->email)->first();
            //echo $request->password.'='.$user->password;die;
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'response'=>'failed',
                    'message' => 'Email id or password invalid'
                ], 404);
            }
            //$token = auth()->user()->createToken('API Token')->accessToken;
            $token = $user->createToken('API Token')->accessToken;
            $response = [
            	'response'=>'success',
                'user' => $user,
                'token' => $token,
                'status'=>true,'code'=>'200'
            ];

            return response($response);
        }


    }

    private function generate_string($strength = 16) {
        //$input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    function getDetailsAll(Request $request){
        $user=User::select('account')->get();
        $response = [
            'response'=>'success',
            'user' => $user,'status'=>true,'code'=>'200'
        ];

        return response()->json($response);
    }

    function getDetails(Request $request){
        $user=$request->user();
        $response = [
            'response'=>'success',
            'user' => $user,'status'=>true,'code'=>'200'
        ];

        return response()->json($response);
    }

    function addMoney(Request $request){
        //$dataArr=$request->all();
        //echo '<pre>';print_r($dataArr);die;
        $validator = \Validator::make($request->all(), [
            'from' => 'required|exists:users,account',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ],[
            'from.exists'=>'Invaid user acccont selected',
            'amount.regex'=>'Invalid amount data entered'
            ]);
        if ($validator->fails()) {
            $responseArr=array('status'=>false,'code'=>'201','message'=>$validator->errors());
            return response()->json($responseArr);
        }else{
            //echo '<pre>';print_r($request);die;
            $transType='credit';
            $txn_id=$this->generate_string(15);
            $user=User::where('account',$request->from)->first();
            $balance=$request->amount;
            $transactionData=Transaction::where('account',$request->from)->orderBy('id','desc')->first();
            if($transactionData){
                $balance=$transactionData->balance+$request->amount;
            }
            $trans=new Transaction();
            $trans->type=$transType;
            $trans->user_id=$user->id;
            $trans->account=$request->from;
            $trans->amount=$request->amount;
            $trans->txn_id=$txn_id;
            $trans->balance	=$balance;
            //echo '<pre>';print_r($trans);die;
            $trans->save();
            $latestTransId=$trans->id;
            $txn_details="Many deposited manually";
            $transDetailsArr=array('transaction_id'=>$latestTransId,'txn_id'=>$txn_id,'type'=>$transType,'txn_details'=>$txn_details);
            DB::table('transaction_details')->insert($transDetailsArr);
            return response()->json(['trans' => $trans,'message'=>'Transaction Addee Succssfuly','status'=>true,'code'=>'200']);
        }
    }



    function trasnsfer(Request $request){
        //$dataArr=$request->all();
        //echo '<pre>';print_r($dataArr);die;
        $validator = \Validator::make($request->all(), [
            'from' => 'required|exists:users,account',
            'to' => 'required|different:from|exists:users,account',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ],[
            'from.exists'=>'Invaid :input acccont selected',
            'to.exists'=>'Invaid :input acccont selected',
            'to.different'=>'"From" account should not same with "To" Account',
            'amount.regex'=>'Invalid amount data entered'
            ]);
        if ($validator->fails()) {
            $errors=$validator->errors();
            $responseArr=array('status'=>false,'code'=>'201','message'=>$validator->errors());
            return response()->json($responseArr);
        }else{
            $transactionData=Transaction::where('account',$request->from)->orderBy('id','desc')->first();
            if($transactionData){
                if($transactionData->balance<=$request->amount){
                    $msg='The amount '.$request->amount.' is less to transfer from "From" account.';
                    return response()->json(['message'=>$msg,'status'=>false,'code'=>'201']);
                }
            }else{
                return response()->json(['message'=>'"Fromo" account has no balance to trasfer any money to "To" account.','status'=>false,'code'=>'201']);
            }

            //$dataArr=$request->all();
            //echo '<pre>';print_r($dataArr);die;
            $trns_txn_id=$this->generate_string(15);

            $transType='credit';
            $txn_id=$this->generate_string(15);
            $userFrom=User::where('account',$request->from)->first();
            $userTo=User::where('account',$request->to)->first();
            $balance=$request->amount;
            $transactionData=Transaction::where('account',$request->to)->orderBy('id','desc')->first();
            if($transactionData){
                $balance=$transactionData->balance+$request->amount;
            }
            $trans=new Transaction();
            $trans->type=$transType;
            $trans->user_id=$userTo->id;
            $trans->account=$request->to;
            $trans->amount=$request->amount;
            $trans->txn_id=$txn_id;
            $trans->balance	=$balance;
            //echo '<pre>';print_r($trans);die;
            $trans->save();
            $latestTransId=$trans->id;
            $txn_details="Money credited to ".$request->to." by transfor tranasaction id ".$trns_txn_id;
            $transDetailsArr=array('transaction_id'=>$latestTransId,'txn_id'=>$txn_id,'type'=>$transType,'txn_details'=>$txn_details,
            'from_user_id'=>$userFrom->id,'from_account'=>$request->from,'is_tranfer'=>'y','trns_txn_id'=>$trns_txn_id);
            DB::table('transaction_details')->insert($transDetailsArr);

            $transType='debit';
            $debitTxn_id=$this->generate_string(15);
            $transactionDeditData=Transaction::where('account',$request->from)->orderBy('id','desc')->first();
            if($transactionDeditData){
                $balance=$transactionDeditData->balance-$request->amount;
            }
            $transDebit=new Transaction();
            $transDebit->type=$transType;
            $transDebit->user_id=$userFrom->id;
            $transDebit->account=$request->from;
            $transDebit->amount=$request->amount;
            $transDebit->txn_id=$debitTxn_id;
            $transDebit->balance	=$balance;
            //echo '<pre>';print_r($trans);die;
            $transDebit->save();
            $latestDeditTransId=$transDebit->id;
            $txn_details="Money bedited to ".$request->to." by transfor tranasaction id ".$trns_txn_id;
            $transDetailsArr=array('transaction_id'=>$latestDeditTransId,'txn_id'=>$debitTxn_id,'type'=>$transType,'txn_details'=>$txn_details,'is_tranfer'=>'y','trns_txn_id'=>$trns_txn_id);
            DB::table('transaction_details')->insert($transDetailsArr);
            return response()->json(['message'=>'Money Transfer completed Succssfuly with Transfer id '.$trns_txn_id,'status'=>true,'code'=>'200']);
        }
    }

    function getBalance(Request $request){
        //$dataArr=$request->all();
        //echo '<pre>';print_r($dataArr);die;
        $validator = \Validator::make($request->all(), [
            'account' => 'required|exists:users,account',
        ],[
            'account.exists'=>'Invaid :input acccont selected',
            ]);
        if ($validator->fails()) {
            $errors=$validator->errors();
            $responseArr=array('status'=>false,'code'=>'201','message'=>$validator->errors());
            return response()->json($responseArr);
        }else{
            $transactionDeditData=Transaction::where('account',$request->account)->orderBy('id','desc')->first();
            return response()->json(['message'=>'Balance for '.$request->account.' is '.$transactionDeditData->balance,'status'=>true,'code'=>'200']);
        }
    }
}
