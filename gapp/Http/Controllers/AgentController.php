<?php

namespace App\Http\Controllers;

use App\Mail\NewSubAccountMail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{

    public function subAgents()
    {
        $datas['users'] = User::where([["uuid", Auth::user()->uuid], ['sub_agent', 1]])->latest()->get();
        return view('agents', $datas);
    }



    public function floatotp()
    {
        $trxcount = Transaction::where('uuid', Auth::id())->count();
        $trxsum = Transaction::where('uuid', Auth::id())->latest()->sum('amount');
        $general = Setting::first(); 
        $user = User::find(Auth::id());
        $now = Carbon::now();

        $months = Carbon::parse($user->created_at)->addMonths(3);
        if($now < $months)
        {
            return redirect()->route('dashboard')->with("error", 'You are not eligible for float at moment. Please try again later');
        }


        $pending = Loan::whereUserId($user->id)->whereStatus(0)->first();
        if($pending)
        {
            return redirect()->route('dashboard')->with("error", 'You have a pending loan float. Please try again later');

        }

        $exist = Loan::whereUserId($user->id)->where('paid','!=',1)->first();
        if($exist)
        {
            return redirect()->route('dashboard')->with("error", 'You have an unpaid loan float. Please pay and again later');

        }

        
        $now = Carbon::now();
        if($user->code_timer < $now)
        {
        
        $expire = Carbon::parse($now)->addMinutes(3);
        $code = rand(12345,90000);
        $user->code = $code;
        $user->code_timer = $expire;
        $user->save(); 
        $text = "Dear Amali Agent (".$user->firstname." ".$user->lastname."), Please enter the code below to verify your float request. ".$code;
        $message = urlencode(utf8_encode($text));
        //return urlencode(utf8_encode($message));

        $url = "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=$general->sms_api&from=$general->sitename&to=$user->phone&body=$message&dnd=2";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        //var_dump($resp);
        $message = "Please enter the OTP sent to your phone number and click on the verify button to start your loan float request";
        }
        else
        {
        $message = "Please re-enter the OTP we sent to your phone number and click on the verify button to continue with loan float process";
        }
        
        return view('request-float-otp',compact('general','message'));
    }

    public function floatpostotp(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'code' => 'required',
         ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $general = Setting::first(); 
        $user = User::find(Auth::id());     
        if($request->code != $user->code)
        {
            return back()->withInput()->with('error', 'Invalid OTP Code');
        }

        $exist = Loan::whereUserId($user->id)->whereStatus(0)->first();
        if($exist)
        {
            return redirect()->route('dashboard')->with("error", 'You have a pending loan float. Please try again later');

        }

        $trxcount = Transaction::where('uuid', Auth::id())->count();
        $trxsum = Transaction::where('uuid', Auth::id())->latest()->sum('amount');
        $general = Setting::first(); 
        //return view('request-float',compact('general'));
       
      if($trxsum < $general->float_min_trx )
        {
           return redirect()->route('dashboard')->with("error", 'You are not eligible for this float at the moment. Please increase your transaction performance');
           //return redirect()->route('dashboard')->with("error", 'You need to have transacted '.$general->cur_sym.$general->float_min_trx.' before you can be eligible for loan. <br> You have transacted a total sum of '.$general->cur_sym.$trxsum);

        }
        if($trxcount < $general->float_min_count )
        {
            return redirect()->route('dashboard')->with("error", 'You are not eligible for this float at the moment. Please increase your transaction count');

            //return redirect()->route('dashboard')->with("error", 'You need to have a minimum '.$general->float_min_count.' transactions before you can be eligible for loan<br> You currenctly have a total count of '.$trxcount.' transactions');

        }
        
        return view('request-float',compact('general'));
 
    }
 
 
    public function floatpost(Request $request)
    {
        $input = $request->all();
 
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'duration' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $general = Setting::first(); 
        if($request->amount < $general->float_min_amount )
        {
                                return view('request-float',compact('general'));

            return back()->withInput()->with('error', 'Invalid minimum amount entered');

        }
        
        if($request->amount > $general->float_max_amount )
        {
                    return view('request-float',compact('general'));

            return back()->withInput()->with('error', 'Invalid maximum amount entered');

        }
        //return 6;

        $interest = $general->float_int_flat + ($request->amount * $general->float_int_percent / 100);
        $total = $request->amount + $interest;
        $now = Carbon::now();
        $expire = Carbon::parse($now)->addDays($request->duration);

        $u = User::find(Auth::id());

        $loan = new Loan();
        $loan->user_id = Auth::id();
        $loan->agent = $u->uuid;
        $loan->amount = $request->amount;
        $loan->interest = $interest;
        $loan->total = $total;
        $loan->status = 0;
        $loan->reference = rand();
        $loan->expire = $expire;
        $loan->duration = $request->duration;
        $loan->save();
        
         return redirect()->route('dashboard')->with("success", "Loan Float created successfully.");


        return back()->withInput()->with('success', 'Loan requested successfuly');
       
    }

    public function myfloat()
    {
        $pending = Loan::where('user_id', Auth::id())->whereStatus(0)->count();
        $running = Loan::where('user_id', Auth::id())->whereStatus(1)->count();
        $closed = Loan::where('user_id', Auth::id())->whereStatus(2)->count();
        $loan = Loan::where('user_id', Auth::id())->latest()->get();
        $wallet = Wallet::where('user_id', Auth::id())->whereName('float')->first();
        if(!$wallet)
        {
           $wallet = new Wallet();
           $wallet->user_id =  Auth::id();
           $wallet->name = 'float';
           $wallet->save();
        }
        $general = Setting::first(); 
        
        return view('myfloat',compact('general','loan','pending','running','closed','wallet'));
    }



    public function searchSubAgents(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $search = $input['search'];

        $datas['users'] = User::where(function ($query) use ($search) {
            $query->orwhere('lastname', 'like', '%' . $search . '%')
                ->orWhere('firstname', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->Where('uuid', Auth::user()->uuid)
                ->Where('sub_agent', 1);
        })->get();

        return view('agents', $datas);
    }

    public function createSubAgent(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|max:200',
            'lastName' => 'required|max:200',
            'email' => 'required|max:200|email|unique:users',
            'dob' => 'required|max:200',
            'gender' => 'required|max:200',
            'phone' => 'required|max:11',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $exist = User::whereEmail($input['email'])->first();
        if($exist)
        {
            return back()->withInput()->with('error', 'An agent already exist with this email');
        }

        $password = uniqid();

        $u = User::create([
            'firstname' => $input['firstName'],
            'lastname' => $input['lastName'],
            'dob' => $input['dob'],
            'gender' => $input['gender'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'transaction_limit' => $input['limit'],
            'password' => Hash::make($password),
            'uuid' => Auth::user()->uuid,
            'sub_agent' => 1,
        ]);


        Wallet::create([
            'user_id' => $u->id,
            'name' => 'deposit'
        ]);


        $datas['businessName'] = Auth::user()->name;
        $datas['name'] = $input['lastName'];
        $datas['email'] = $input['email'];
        $datas['phone'] = $input['phone'];
        $datas['password'] = $password;

        try {
            Mail::to($input['email'])->send(new NewSubAccountMail($datas));
        } catch (\Exception $e) {
            echo "Mail error";
        }


        return redirect()->route('addSubAgent')->with("success", "Subagent created successfully. Login credentials has been sent to the email provided.");

    }


    public function performance()
    {
        $datas['date'] = Carbon::now()->format('y-m');
        $datas['users'] = User::where([["uuid", Auth::user()->uuid], ['sub_agent', 1]])->latest()->get();
        return view('settings.performance', $datas);
    }

    public function performanceSearch()
    {
        $datas['users'] = User::where([["uuid", Auth::user()->uuid], ['sub_agent', 1]])->latest()->get();
        return view('settings.performance', $datas);
    }

    public function agentTransactions($id)
    {

        $datas['datas'] = Transaction::where('user_id', $id)->get();
        $datas['tran_count'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->count();
        $datas['tran_sum'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->sum('amount');
        $datas['wallet'] = Wallet::where('user_id', $id)->first();

        return view('transactions_per_agent', $datas);
    }

    public function agentFloat($id)
    {
        $datas['datas'] = Transaction::where('user_id', $id)->get();
        $datas['tran_count'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->count();
        $datas['tran_sum'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->sum('amount');
        $datas['pending'] = Loan::where('user_id', $id)->whereStatus(0)->sum('amount');
        $datas['active'] = Loan::where('user_id', $id)->whereStatus(1)->sum('amount');
        $datas['completed'] = Loan::where('user_id', $id)->whereStatus(2)->sum('amount');
        $datas['float'] = Loan::where('user_id', $id)->get();
        $datas['wallet'] = Wallet::where('user_id', $id)->first();

        return view('float_per_agent', $datas);
    }

    public function transactions(Request $request)
    {
        $input = $request->all();
        if($input)
       {
           $datas['datas'] = Transaction::where('uuid', Auth::user()->uuid)->whereBetween('created_at',[$request->from,$request->to])->Orwhere('type',$request->type)->latest()->get();
       }
       else
       {
           $datas['datas'] = Transaction::where('uuid', Auth::user()->uuid)->latest()->get();
       }

 
        return view('transactions', $datas);
    }
    
      public function floatcron()
    {
        
        $now = Carbon::now();
        $months = Carbon::parse($user->created_at)->addMonths(3);
        $loan = Loan::wherePaid(0)->whereStatus(1)->where('expire', '<', $now)->get();
        $general = Setting::first(); 

        foreach($loan as $data)
        {
            try 
            {
            $balance = $data->total - $data->total_paid;
            $user = User::find($data->user_id);
            $wallet = Wallet::whereUserId($user->id)->whereName('deposit')->first();
                if($wallet->balance >= $balance )
                {
                    $wallet->balance -= $balance;
                    $wallet->save();
                    $data->paid = 1;
                    $data->save();

                    $text = "Dear Amali Agent (".$user->firstname." ".$user->lastname."), we have received payment for your running float";
                    $message = urlencode(utf8_encode($text));
                    //return urlencode(utf8_encode($message));

                    $url = "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=$general->sms_api&from=$general->sitename&to=$user->phone&body=$message&dnd=2";

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    //for debug only!
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    //var_dump($resp);
                } 
            } 
            catch (\Exception $e) 
            {
 
            }

        } 
    }



}
