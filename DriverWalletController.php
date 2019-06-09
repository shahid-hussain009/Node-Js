<?php

namespace App\Http\Controllers;

use App\driverWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Driver;
use App\TimeLog;
class DriverWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
        $DvWallet = DB::table('tms_driver')
            ->leftJoin('tms_wallet', 'tms_driver.dv_id', '=', 'tms_wallet.dv_id')
            ->select('tms_driver.*','tms_wallet.*', 'tms_driver.dv_id as driver_id')
            ->where('tms_driver.dv_is_verified','!=' , 0)
            ->where('tms_driver.dv_is_verified','!=' , 2)
            ->get();
        $data = array(
            'dvWallet' => $DvWallet,
        );
        return view('pages.dvWallet')->with($data);
    }
    public function securityWithdraw()
    {
        return view('pages.dvWalletSecurityWithdrawl')->with([
            'requested' => DB::table('tms_dv_return_deposit')
                             ->join('tms_driver', 'tms_driver.dv_id', '=', 'tms_dv_return_deposit.dv_id')
                             ->leftjoin('tms_dv_deposit_detail', 'tms_dv_deposit_detail.deposit_id', '=', 'tms_dv_return_deposit.deposit_id')
                             ->where('tms_dv_return_deposit.is_approved', '=', false)
                             ->get(),
            'approved'  => DB::table('tms_dv_return_deposit')
                             ->join('tms_driver', 'tms_driver.dv_id', '=', 'tms_dv_return_deposit.dv_id')
                             ->leftjoin('tms_dv_deposit_detail', 'tms_dv_deposit_detail.deposit_id', '=', 'tms_dv_return_deposit.deposit_id')
                             ->where('tms_dv_return_deposit.is_approved', '=', true)
                             ->get()
        ]);
    }

    public function approveSecurityWithdraw($id)
    {
        DB::table('tms_dv_return_deposit')->where('return_deposit_id', '=', $id)->update(['is_approved' => true]);
        return redirect('/securityWithdraw')->with('flash_message', 'Amount Approved Successfully');
    }

    public function downloadSecurityWithdrawCSV()
    {
        $approved = DB::table('tms_dv_return_deposit')
                      ->join('tms_driver', 'tms_driver.dv_id', '=', 'tms_dv_return_deposit.dv_id')
                      ->leftjoin('tms_dv_deposit_detail', 'tms_dv_deposit_detail.deposit_id', '=', 'tms_dv_return_deposit.deposit_id')
                      ->where('tms_dv_return_deposit.is_approved', '=', true)
                      ->get();
        
        if ( sizeof( $approved ) > 0 )
        {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="approved_security_drivers.csv";');
            $columns = ['S.No.', 'Unique Number', 'Name', 'ID Card', 'Mobile Number'];
    
            // $callback = function() use ($approved, $columns)
            // {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
    
                foreach($approved as $key => $approve) {
                    fputcsv($file, [
                        $key + 1,
                        $approve->dv_unique_number,
                        "$approve->dv_first_name $approve->dv_last_name",
                        $approve->dv_cnic_id,
                        $approve->dv_mobile_number
                    ], ',');
                }
                fclose($file);
            // };
            // return Response::stream($callback, 200, $headers);
        }
        // return redirect('/securityWithdraw#approved')->with('flash_message', 'Amount Approved Successfully');
    }

    public function walletWithdraw()
    {
        return view('pages.dvWalletWithdrawl')->with([
            'requested' => DB::table('dv_withdraw_wallet')
                             ->leftjoin('tms_driver', 'tms_driver.dv_id', '=', 'dv_withdraw_wallet.dv_id')
                             ->where('dv_withdraw_wallet.withdraw_status', '=', '0')
                             ->get(),
            'approved'  => DB::table('dv_withdraw_wallet')
                             ->leftjoin('tms_driver', 'tms_driver.dv_id', '=', 'dv_withdraw_wallet.dv_id')
                             ->where('dv_withdraw_wallet.withdraw_status', '=', '1')
                             ->get()
        ]);
    }

    public function approveWalletWithdraw($id)
    {
        DB::table('dv_withdraw_wallet')->where('id', '=', $id)->update(['withdraw_status' => true]);
        return redirect('/walletWithdraw')->with('flash_message', 'Amount Approved Successfully');
    }

    public function downloadWalletWithdrawCSV()
    {
        $approved = DB::table('dv_withdraw_wallet')
                      ->leftjoin('tms_driver', 'tms_driver.dv_id', '=', 'dv_withdraw_wallet.dv_id')
                      ->where('dv_withdraw_wallet.withdraw_status', '=', true)
                      ->get();
        
        if ( sizeof( $approved ) > 0 )
        {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="approved_wallet_drivers.csv";');
            $columns = ['S.No.', 'Name', 'ID Card', 'Mobile Number', 'JazzCash Number', 'Amount'];
    
            // $callback = function() use ($approved, $columns)
            // {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
    
                foreach($approved as $key => $approve) {
                    fputcsv($file, [
                        $key + 1,
                        "$approve->dv_first_name $approve->dv_last_name",
                        $approve->dv_cnic_id,
                        $approve->dv_mobile_number,
                        $approve->jazzcash_account_number,
                        $approve->amount
                    ], ',');
                }
                fclose($file);
            // };
            // return Response::stream($callback, 200, $headers);
        }
        // return redirect('/securityWithdraw#approved')->with('flash_message', 'Amount Approved Successfully');
    }

    public function dvWalletDetailShow(Request $request)
    {
        $id = $request->input('dvId');
        $dvWalletData = DB::table('tms_wallet')
            ->leftJoin('tms_dv_deposit_detail', 'tms_wallet.deposit_id', '=', 'tms_dv_deposit_detail.deposit_id')
            ->select('tms_wallet.*','tms_dv_deposit_detail.*','tms_dv_deposit_detail.created_at as deposit_date')
            ->where('tms_wallet.dv_id',$id)
            ->first();

        $dvWalletHistory = DB::table('tms_wallet_history')
            ->select('tms_wallet_history.*')
            ->where('tms_wallet_history.dv_id',$id)
            ->get();

        $DvWallet = DB::table('tms_driver')
            ->select('tms_driver.*')
            ->where('tms_driver.dv_id',$id)
            ->first();

        $data = array(
            'dvWalletData'      => $dvWalletData,
            'dvWalletHistory'   => $dvWalletHistory,
            'dvWallet'          => $DvWallet,
        );
        return view('partialViews.dvWalletDetailView')->with($data);
    }

    public function dvEqAmtHistory(Request $request)
    {
        $dvId = $request->input('dvId');
        $dvEqHistory = DB::table('dv_equipment_details')
            ->leftJoin('tms_store', 'dv_equipment_details.product_id', '=', 'tms_store.product_id')
            ->select('dv_equipment_details.*','tms_store.*')
            ->where('dv_equipment_details.dv_id',$dvId)
            ->get();

        $data = array(
            'dvEqHistory'      => $dvEqHistory,
        );
        return view('partialViews.dvEqAmtDetailView')->with($data);
    }

    public function dvIncomeDetailShow(Request $request)
    {
        $dvId = $request->input('dvId');

        $dvIncomeHistory = DB::table('tms_booking')
            ->join('tms_goods', 'tms_booking.good_id', '=', 'tms_goods.good_id')
            ->select('tms_booking.*', 'tms_goods.*')
            ->where('tms_booking.dv_id',$dvId)
            ->where('tms_booking.tms_booking_status',1)
            ->get();

        $passenger = DB::table('tms_passenger')
            ->select('tms_passenger.*')
            ->where('tms_passenger.dv_id',$dvId)
            ->where('tms_passenger.tms_passenger_status',1)
            ->get();

        $data = array(
            'dvIncomeHistory' => $dvIncomeHistory,
            'passengerIncomeHistory' => $passenger,
        );
        //dd($data);
        return view('partialViews.dvIncomeHistoryData')->with($data);
    }

    public function dvIncomeHistoryShow(Request $request)
    {
        $dvId = $request->input('dvId');

        $dvIncomeHistory = DB::table('tms_booking')
            ->join('tms_goods', 'tms_booking.good_id', '=', 'tms_goods.good_id')
            ->select('tms_booking.*', 'tms_goods.*')
            ->where('tms_booking.dv_id',$dvId)
            ->where('tms_booking.tms_booking_status',1)
            ->get();

        $passenger = DB::table('tms_passenger')
            ->select('tms_passenger.*')
            ->where('tms_passenger.dv_id',$dvId)
            ->where('tms_passenger.tms_passenger_status',1)
            ->get();

        $data = array(
            'dvIncomeHistory' => $dvIncomeHistory,
            'passengerIncomeHistory' => $passenger,
        );
        //dd($data);
        return view('partialViews.dvsettlementHistoryData')->with($data);
    }
    public function custIncomeHistoryShow(Request $request)
    {
        $dvId = $request->input('custId');

        $dvIncomeHistory = DB::table('tms_booking')
            ->join('tms_goods', 'tms_booking.good_id', '=', 'tms_goods.good_id')
            ->select('tms_booking.*', 'tms_goods.*')
            ->where('tms_booking.cust_id',$dvId)
            ->where('tms_booking.tms_booking_status',1)
            ->get();

        $passenger = DB::table('tms_passenger')
            ->select('tms_passenger.*')
            ->where('tms_passenger.dv_id',$dvId)
            ->where('tms_passenger.tms_passenger_status',1)
            ->get();

        $data = array(
            'dvIncomeHistory' => $dvIncomeHistory,
            'passengerIncomeHistory' => $passenger,
        );
        //dd($data);
        return view('partialViews.custsettlementHistoryData')->with($data);
    }
    public function dvTaxiIncomeDetailShow(Request $request)
    {
        $dvId = $request->input('dvId');

        $passenger = DB::table('tms_passenger')
            ->select('tms_passenger.*')
            ->where('tms_passenger.dv_id',$dvId)
            ->where('tms_passenger.tms_passenger_status',1)
            ->get();

        $data = array(
            'passengerIncomeHistory' => $passenger,
        );
        //dd($data);
        return view('partialViews.dvTaxiIncomeHistory')->with($data);
    }

    public function filterTransactions(Request $request)
    {
        $dvId = $request->input('dvId');
        $searchStatus = $request->input('searchStatus');

        $startDate = str_replace('/', '-', $request->input('startDate'));
        $endDate = str_replace('/', '-', $request->input('endDate'));
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if(!$startDate || !$endDate)
        {
            if($searchStatus == '' || $searchStatus == null)
            {
                $dvWalletHistory = DB::table('tms_wallet_history')
                    ->select('tms_wallet_history.*')
                    ->where('tms_wallet_history.dv_id',$dvId)
                    ->get();
            }
            else
            {
                $dvWalletHistory = DB::table('tms_wallet_history')
                    ->select('tms_wallet_history.*')
                    ->where('tms_wallet_history.dv_id',$dvId)
                    ->where('tms_wallet_history.transaction_status',$searchStatus)
                    ->get();
            }
        }
        else
        {
            if($searchStatus == '' || $searchStatus == null)
            {
                $dvWalletHistory = DB::table('tms_wallet_history')
                    ->select('tms_wallet_history.*')
                    ->where('tms_wallet_history.dv_id',$dvId)
                    ->whereBetween('tms_wallet_history.transaction_date',[$startDate,$endDate])
                    ->get();
            }
            else
            {
                $dvWalletHistory = DB::table('tms_wallet_history')
                    ->select('tms_wallet_history.*')
                    ->where('tms_wallet_history.dv_id',$dvId)
                    ->where('tms_wallet_history.transaction_status',$searchStatus)
                    ->whereBetween('tms_wallet_history.transaction_date',[$startDate,$endDate])
                    ->get();
            }

        }

        $data = array(
            'dvWalletHistory' => $dvWalletHistory,
        );
        return view('partialViews.filterTransactions')->with($data);
    }
    function getStayOnline($user_id,$date) 
    {
        $socket_ids = DB::table('tms_time_logs')->select(DB::raw('DISTINCT(socket_id)'))->where('user_id',$user_id)->get();
        $timeDurations = [];
        foreach ($socket_ids as $key => $socket) 
        {
           $timeDurations[] = TimeLog::onlineDuration($socket->socket_id,$date);
        }
        return array_sum($timeDurations);
    }

    public function driver_performance(Request $request)
    {
        $query = DB::table('tms_driver');
        $query->join('tms_booking', 'tms_driver.dv_id', '=', 'tms_booking.dv_id');
        $query->where('tms_booking.dv_id', $request->id);
        $query->where('tms_booking.tms_booking_status', '1');
        $query->orderBy('tms_booking.tms_booking_id', 'DESC');
        $query->groupBy(DB::raw('Date(tms_booking.created_at)'));
        $query->select('tms_booking.created_at as booking_date','tms_booking.total_paid', DB::raw('count(tms_booking.tms_booking_id) as total_booking'), DB::raw('sum(tms_booking.total_paid) as total_value'));
        $performances = $query->get();
        foreach ($performances as $p => $booking) 
        {
            $date = date('Y-m-d',strtotime($booking->booking_date));
            $performances[$p]->stayed_online = $this->getStayOnline($request->id,$date);
        }
      
       /* $socket_ids = DB::table('tms_time_logs')->select(DB::raw('DISTINCT(socket_id)'))->where('user_id','170')->get();
        $timeDurations = [];
        foreach ($socket_ids as $key => $socket) 
        {
           $timeDurations[] = TimeLog::onlineDuration($socket->socket_id);
        }*/
       
       /*dd($timeDurations);
        exit;*/
       /* $query = DB::table('tms_time_logs');
        $query->where('tms_time_logs.user_id', 170);
        $query->where('tms_time_logs.type', 'driver');
        $query->orderBy('tms_time_logs.id', 'DESC');
        $query->groupBy(DB::raw('Date(tms_time_logs.created_at)'));
        $query->groupBy('tms_time_logs.socket_id');
        $query->select(DB::raw('count(id) as count, TIME(created_at) as hour'), DB::raw('sum(created_at) as total_hour'));
        $performances = $query->get();
        dd($performances);*/
        
        return view('driver.performance', compact('performances'));
    }



}
