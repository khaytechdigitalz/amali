<?php

namespace App\Http\Controllers\Admin;
use App\Mail\NewSubAccountMail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Terminal;
use App\Models\Loan;
use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    ######ADMIN OPERATIONS FROM ADMIN ROUTE############
    


    public function floatrequest()
    {
        $pending = Loan::whereStatus(0)->count();
        $running = Loan::whereStatus(1)->count();
        $closed = Loan::whereStatus(2)->count();
        $loan = Loan::latest()->whereStatus(0)->get();
        $general = Setting::first(); 
        $title = "Float/Loan Request";
        
        return view('admin.float',compact('title','general','loan','pending','running','closed'));
    }

    public function floatrunning()
    {
        $pending = Loan::whereStatus(0)->count();
        $running = Loan::whereStatus(1)->count();
        $closed = Loan::whereStatus(2)->count();
        $loan = Loan::latest()->whereStatus(1)->get();
        $general = Setting::first(); 
        $title = "Active Float";
        
        return view('admin.float',compact('title','general','loan','pending','running','closed'));
    }
    public function floatdue()
    {
        $now = Carbon::now();

        $pending = Loan::whereStatus(0)->count();
        $running = Loan::whereStatus(1)->count();
        $closed = Loan::whereStatus(2)->count();
        $loan = Loan::latest()->where('expire','<',$now)->whereStatus(1)->get();
        $general = Setting::first(); 
        $title = "Due Float";
        
        return view('admin.float',compact('title','general','loan','pending','running','closed'));
    }


    public function floatclose()
    {
        $pending = Loan::whereStatus(0)->count();
        $running = Loan::whereStatus(1)->count();
        $closed = Loan::whereStatus(2)->count();
        $loan = Loan::latest()->whereStatus(2)->get();
        $general = Setting::first(); 
        $title = "Settled Float";  
        return view('admin.float',compact('title','general','loan','pending','running','closed'));
    }



    public function floatview($id)
    {
        $loan = Loan::latest()->whereId($id)->first();
        $agent = User::latest()->whereId($loan->user_id)->first();
        $subagent = User::whereSubAgent(1)->whereUuid($agent->uuid)->count();
        $general = Setting::first(); 
        $title = "View Float";
        $now = Carbon::now();

        $trx = Transaction::whereUserId($loan->user_id)->latest()->get();

        return view('admin.float-view',compact('now','title','loan','trx','agent','subagent'));
    }


    public function floatapprove($id)
    {
        $loan = Loan::latest()->whereId($id)->first();
        $agent = User::latest()->whereId($loan->user_id)->first();
        if($loan->status == 0)
        {
        $loan->status = 1;
        $loan->save();
        $wallet = Wallet::where('user_id', $loan->user_id)->whereName('float')->first();
        if(!$wallet)
        {
           $wallet = new Wallet();
           $wallet->user_id =  $loan->user_id;
           $wallet->name = 'float';
           $wallet->save();
        }
      

        $wallet->balance += $loan->amount;
        $wallet->save();
        return back()->withInput()->with('success', 'Loan Approved Successfuly');
        }
        else
        {
         return back()->withInput()->with('error', 'You cant approve this loan again');
        }
        
    }


    public function  floatreject($id)
    {
        $loan = Loan::latest()->whereId($id)->first();
        $agent = User::latest()->whereId($loan->user_id)->first();
        if($loan->status != 1)
        {
        $loan->delete();
        return back()->withInput()->with('success', 'Loan Declined Successfuly');
        }
        else
        {
         return back()->withInput()->with('error', 'You cant decline this loan as it appears to be running');
        }
        
    }

    public function dashboard(Request $request)
    {
        $input = $request->all();
         if($input)
        {
            $datas['trans'] = Transaction::whereBetween('created_at',[$request->from,$request->to])->Orwhere('type',$request->type)->latest()->get();
        }
        else
        {
            $datas['trans'] = Transaction::latest()->get();
        }

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $jan = '01';
        $feb = '02';
        $mar = '03';
        $apr = '04';
        $may = '05';
        $jun = '06';
        $jul = '07';
        $aug = '08';
        $sep = '09';
        $oct = '10';
        $nov = '11';
        $dec = '12';

        $datas['cjan'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jan)->whereType('Credit')->sum('amount');
        $datas['cfeb'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $feb)->whereType('Credit')->sum('amount');
        $datas['cmar'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $mar)->whereType('Credit')->sum('amount');
        $datas['capr'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $apr)->whereType('Credit')->sum('amount');
        $datas['cmay'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $may)->whereType('Credit')->sum('amount');
        $datas['cjun'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jun)->whereType('Credit')->sum('amount');
        $datas['cjul'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jul)->whereType('Credit')->sum('amount');
        $datas['caug'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $aug)->whereType('Credit')->sum('amount');
        $datas['csep'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $sep)->whereType('Credit')->sum('amount');
        $datas['coct'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $oct)->whereType('Credit')->sum('amount');
        $datas['cnov'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $nov)->whereType('Credit')->sum('amount');
        $datas['cdec'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $dec)->whereType('Credit')->sum('amount');

        $datas['djan'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jan)->whereType('Debit')->sum('amount');
        $datas['dfeb'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $feb)->whereType('Debit')->sum('amount');
        $datas['dmar'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $mar)->whereType('Debit')->sum('amount');
        $datas['dapr'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $apr)->whereType('Debit')->sum('amount');
        $datas['dmay'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $may)->whereType('Debit')->sum('amount');
        $datas['djun'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jun)->whereType('Debit')->sum('amount');
        $datas['djul'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $jul)->whereType('Debit')->sum('amount');
        $datas['daug'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $aug)->whereType('Debit')->sum('amount');
        $datas['dsep'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $sep)->whereType('Debit')->sum('amount');
        $datas['doct'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $oct)->whereType('Debit')->sum('amount');
        $datas['dnov'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $nov)->whereType('Debit')->sum('amount');
        $datas['ddec'] = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $dec)->whereType('Debit')->sum('amount');

       

        $datas['balance'] = Wallet::sum('balance');
        $datas['agent'] = User::whereSubAgent(null)->count();
        $datas['trx'] = Transaction::count();

        return view('admin.dashboard', $datas);
    }

    public function agents()
    {
        $datas['agents'] = User::whereSubAgent(null)->whereSuperadmin(0)->get();

        return view('admin.all-agent', $datas);
    }

    public function addagent()
    {
        $datas['agents'] = User::whereSubAgent(null)->whereSuperadmin(0)->get();

        return view('admin.add-agent', $datas);
    }

    public function createAgent(Request $request)
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


        return redirect()->route('admin.createAgent')->with("success", "Agent created successfully. Login credentials has been sent to the email provided.");

    }



    public function viewagent($id)
    {
        $datas['agent'] = User::whereSubAgent(null)->whereId($id)->first();
        
        if(!$datas['agent'])
        {
        return back()->withInput()->with('error', 'Invalid Agent Account');
        }

        $datas['subagent'] = User::whereUuid($datas['agent']->uuid)->whereSubAgent(1)->count();
        $datas['subagents'] = User::whereUuid($datas['agent']->uuid)->whereSubAgent(1)->get();
        $datas['terminals'] = Terminal::whereAgentId($id)->get();
        return view('admin.view-agent', $datas);
    }

    public function addterminal(Request $request, $id)
    {
        $request->validate([
            'serialnumber'   => 'required'
        ]);

        $datas['agent'] = User::whereSubAgent(null)->whereId($id)->first();
        
        if(!$datas['agent'])
        {
        return back()->withInput()->with('error', 'Invalid Agent Account');
        }

        $terminal = Terminal::whereSerialNumber($request->serialnumber)->first();
        if(!isset($terminal))
        {
            return back()->withInput()->with('error', 'This terminal does not exist on the database. Try adding this Terminal first and try again');
        }

        if($terminal->agent_id != Null)
        {
            return back()->withInput()->with('error', 'This terminal has already been assigned to an agent');
        }
 
            $terminal->terminal_id = $terminal->terminal_id;
            $terminal->agent_id = $id;
            $terminal->sub_agent_id = null;
            $terminal->serial_number = $terminal->serial_number;
            $terminal->status = 1;
            $terminal->save();
       

        if ($terminal) {
            return back()->withInput()->with('success', 'Terminal added to Agent successfuly');
        } else {
            return back()->withInput()->with('error', 'Error while adding terminal');
        } 
    }


    public function assignterminal(Request $request)
    {
        $request->validate([
            'serialnumber'   => 'required',
            'agent'   => 'required'
        ]);

        $datas['agent'] = User::whereSubAgent(null)->whereId($request->agent)->first();
        
        if(!$datas['agent'])
        {
        return back()->withInput()->with('error', 'Invalid Agent Account');
        }

        $terminal = Terminal::whereSerialNumber($request->serialnumber)->first();
        if(!isset($terminal))
        {
            return back()->withInput()->with('error', 'This terminal does not exist on the database. Try adding this Terminal first and try again');
        }

        if($terminal->agent_id != Null)
        {
            return back()->withInput()->with('error', 'This terminal has already been assigned to an agent');
        }
 
            $terminal->terminal_id = $terminal->terminal_id;
            $terminal->agent_id = $request->agent;
            $terminal->sub_agent_id = null;
            $terminal->serial_number = $terminal->serial_number;
            $terminal->status = 1;
            $terminal->save();
       

        if ($terminal) {
            return back()->withInput()->with('success', 'Terminal added to Agent successfuly');
        } else {
            return back()->withInput()->with('error', 'Error while adding terminal');
        } 
    }

    public function subagentTransactions($id)
    {

        $datas['datas'] = Transaction::where('user_id', $id)->get();
        $datas['tran_count'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->count();
        $datas['tran_sum'] = Transaction::where([['user_id', $id], ['created_at', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->sum('amount');
        $datas['wallet'] = Wallet::where('user_id', $id)->first();

        return view('admin.transactions_per_agent', $datas);
    }

    public function subagents()
    {
        $datas['users'] = User::where('sub_agent', 1)->latest()->get();
        return view('admin.all-subagent', $datas);
    }


    public function posmanagement()
    {
        $datas['terminals'] = Terminal::get();
        $datas['title'] = "All POS Terminals";
        $datas['total'] = Terminal::count();
        $datas['assigned'] = Terminal::whereSubAgentId(!null)->count();
        $datas['unassigned'] = Terminal::whereSubAgentId(null)->count();

        return view('admin.terminals', $datas);
    }


    public function posmanagementcreate(Request $request)
    {
        $terminal = Terminal::whereSerialNumber($request->serialnumber)->first();
        if(isset($terminal))
        {
            return back()->withInput()->with('error', 'This terminal already exist on the database');
        }
        $terminal = new Terminal();
        $terminal->terminal_id = $request->terminalid;
        $terminal->serial_number = $request->serialnumber;
        $terminal->save();

        
        if ($terminal) {
            return back()->withInput()->with('success', 'Terminal created successfuly');
        } else {
            return back()->withInput()->with('error', 'Error while creating terminal');
        } 
    }



    public function posmanagementu()
    {
        $datas['terminals'] = Terminal::whereSubAgentId(null)->get();
        $datas['title'] = "Unassigned POS Terminals";
        $datas['total'] = Terminal::count();
        $datas['assigned'] = Terminal::whereSubAgentId(!null)->count();
        $datas['unassigned'] = Terminal::whereSubAgentId(null)->count();

        return view('admin.terminals', $datas);
    }



    public function posmanagementa()
    {
        $datas['terminals'] = Terminal::whereSubAgentId(!null)->get();
        $datas['title'] = "Assigned POS Terminals";
        $datas['total'] = Terminal::count();
        $datas['assigned'] = Terminal::whereSubAgentId(!null)->count();
        $datas['unassigned'] = Terminal::whereSubAgentId(null)->count();

        return view('admin.terminals', $datas);
    }



    public function posterminal($id)
    {
        $terminal= Terminal::whereId($id)->first();
         if($terminal->status == 1)
         {
             $terminal->status = 0;
         }
         else
         {
            $terminal->status = 1;
        }
        $terminal->save();
        return back()->withInput()->with('success', 'POS Terminal Status updated successfully');

    }


    public function kycs()
    {
        $datas['title'] = "KYC Verification";
        $datas['verified'] = Kyc::whereStatus(1)->count();
        $datas['unverified'] = Kyc::whereStatus(0)->count();
        $datas['all'] = Kyc::all();

        return view('admin.kyc', $datas);
    }



    public function kycsSuccessful()
    {
        $datas['title'] = "Approved KYC Verification";
        $datas['verified'] = Kyc::whereStatus(1)->count();
        $datas['unverified'] = Kyc::whereStatus(0)->count();
        $datas['all'] = Kyc::whereStatus(1)->get();

        return view('admin.kyc', $datas);
    }



    public function kycsrejected()
    {
        $datas['title'] = "Pnding KYC Verification";
        $datas['verified'] = Kyc::whereStatus(1)->count();
        $datas['unverified'] = Kyc::whereStatus(0)->count();
        $datas['all'] = Kyc::whereStatus(0)->get();

        return view('admin.kyc', $datas);
    }


    public function kyc($id)
    {
        $datas['title'] = "View KYC Verification";
        $datas['kyc'] = Kyc::whereId($id)->first();

        return view('admin.view-kyc', $datas);
    }


    public function kycapprove($id)
    {
        $datas['title'] = "View KYC Verification";
        $kyc = Kyc::whereId($id)->first();
        $kyc->status = 1;
        $kyc->save();
        return back()->withInput()->with('success', 'KYC Approved successfully');

    }
    public function kycreject($id)
    {
        $datas['title'] = "View KYC Verification";
        $kyc = Kyc::whereId($id)->first();
        $kyc->status = 2;
        $kyc->save();
        return back()->withInput()->with('success', 'KYC Rejected successfully');

    }


    public function floatsettings()
    {
        $datas['title'] = "Float Settings";
        $datas['general'] = Setting::first(); 

        return view('admin.floatsettings', $datas);
    }
    public function floatpost(Request $request)
    {
        $datas['title'] = "System Settings";
        $general = Setting::first(); 
         
        $general->float_min_trx = $request->float_min_trx;
        $general->float_min_count = $request->float_min_count;
        $general->float_min_month = $request->float_min_month;


        $general->float_min_amount = $request->float_min;
        $general->float_max_amount = $request->float_max;
        $general->float_min_tenure = $request->float_min_tenure;
        $general->float_max_tenure = $request->float_max_tenure;
        $general->float_int_flat = $request->float_int_flat;
        $general->float_int_percent = $request->float_int_percent;
        $general->float_fee = $request->float_fee; 
        $general->save();
        return back()->withInput()->with('success', 'Float Settings Updated successfully');
    }



    public function settings()
    {
        $datas['title'] = "System Settings";
        $datas['general'] = Setting::first(); 

        return view('admin.settings', $datas);
    }


    public function settingspost(Request $request)
    {
        $datas['title'] = "System Settings";
        $general = Setting::first(); 
        $general->sitename = $request->sitename;
        $general->cur_text = $request->cur_text;
         
        $general->save();
        return back()->withInput()->with('success', 'General Settings Updated successfully');
    }

    public function paymentsettings()
    {
        $datas['title'] = "Payment Settings";
        $datas['general'] = Setting::first(); 

        return view('admin.settings', $datas);
    }

   


    ######ADMIN OPERATIONS FROM ADMIN ROUTE############






    public function updateProfile(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:200',
            'lastname' => 'required|max:200',
            'email' => 'required|email|max:200',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $u = User::find(Auth::id());

        if (!$u) {
            return back()->withInput()->with('error', 'Unable to find account');
        }

        $u->firstname = $input['firstname'];
        $u->lastname = $input['lastname'];
        $u->email = $input['email'];
        $u->save();

        return back()->withInput()->with('success', 'Profile updated successfully');
    }
}
