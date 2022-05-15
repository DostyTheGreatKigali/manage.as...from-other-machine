@extends('master.layout')

@php
$url = url('/');
@endphp
<?php
$user = \Auth::user();


// $url_to_admin = url('/') . '/admin';

// $financierUrl = url('/') . '/admin/dashboard/subscriptions';
// dd($url);
// if ($user->hasRole('executive')) {
//     header('Location: ' . $financierUrl, true);

//     exit();
// }

if ($user == null) {
    // header('Location: ' . $url_to_admin, true);
    header('Location: ' . $url, true);

    exit();
}

// if ($user->hasRole('republic_bank_admin')) {
//     header('Location: ' . $url, true);

//     exit();
// }

?>

@section('content')
<div class="content">
<?php 
// echo \Carbon\Carbon::createFromFormat('Y-m-d H', '1975-05-21 22')->toDateTimeString(); // 1975-05-21 22:00:00
// echo \Carbon\Carbon::createFromFormat('Y-m-d H', '2022-03-22 23')->toDateTimeString(); // 1975-05-21 22:00:00
// $dateForResettingMoneyCounters = \Carbon\Carbon::createFromFormat('Y-m-d H', '2022-03-22 23')->startOfDay()->toDateTimeString();
// echo $dateForResettingMoneyCounters;
?>
    @php

    $realTotalRegisteredSupporters = count(App\Supporter::all());
    // dd($realTotalRegisteredSupporters);

    // $dateForResettingMoneyCounters = \Carbon\Carbon::createFromFormat('Y-m-d H', '2022-03-22 23')->toDateTimeString();
    $dateForResettingMoneyCounters = \Carbon\Carbon::createFromFormat('Y-m-d H', '2022-03-22 23')->startOfDay()->toDateTimeString();

    $ghanaPaidSupporters = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id')
    ;
   // dd( $ghanaPaidSupporters);


    //$outsidePaidSupporters = App\Transaction::whereNotIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    $outsidePaidSupporters = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    //->orWhere('query_response', 'like', '%"responseCode":"01"%');//  UNIWALLET , DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id')
    ;
    //dd($outsidePaidSupporters);

    // General
    //$allSupporters = App\Supporter::count();
    //$allSupporters = 725;
    $allSupporters =  $ghanaPaidSupporters + $outsidePaidSupporters;

    $unpaidRegisteredSupporters = $realTotalRegisteredSupporters  - $allSupporters;
    // dd($unpaidRegisteredSupporters);


    $start = Carbon\Carbon::now()->startOfMonth();
    $end = Carbon\Carbon::now()->endOfMonth();
    // $registeredThisMonth = App\Supporter::whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    // ->count();
    $registeredThisMonth = App\Transaction::whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id')
    ;
  /*  $revenueThisMonth = App\Transaction::whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    ->sum('amount');
    $totalRevenue = App\Transaction::where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    ->sum('amount');*/

    // Ghana
    //$ghanaSupporters = App\Supporter::where('country', 'Ghana')->count();
    //$ghanaSupporters = 714;
    $ghanaSupporters =  $ghanaPaidSupporters;

    $start = Carbon\Carbon::now()->startOfMonth();
    $end = Carbon\Carbon::now()->endOfMonth();
    // $ghanaRegisteredThisMonth = App\Supporter::where('country', 'Ghana')->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    // ->count();

    $ghanaRegisteredThisMonth = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id')
    ;
    $ghanaRevenueThisMonth = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('created_at', '>=', $dateForResettingMoneyCounters)
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
	->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
                    })
    ->sum('amount');
    
    $ghanaTotalRevenue = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('created_at', '>=', $dateForResettingMoneyCounters)
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
        ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
                        })
->sum('amount');

    // GH PILOT MONEY
    $ghanaTotalPilotRevenue = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->where('created_at', '<', $dateForResettingMoneyCounters)
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
        ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
                        })
->sum('amount');

if(url('/') == 'https://manage.asantekotokosupporters.com'){
$ghanaTotalPilotRevenue = $ghanaTotalPilotRevenue - 11482;
}
// $ghanaTotalRevenue = $ghanaTotalRevenue - 16617;
//$ghanaTotalRevenue = 20126;

    // Outside Ghana
   // $outsideSupporters = App\Supporter::where('country', '!=', 'Ghana')->count();
    //$outsideSupporters = 11;
    $outsideSupporters =   $outsidePaidSupporters;

    $start = Carbon\Carbon::now()->startOfMonth();
    $end = Carbon\Carbon::now()->endOfMonth();
    // $outsideRegisteredThisMonth = App\Supporter::where('country', '!=', 'Ghana')->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    // ->count();
    // 
    $outsideRegisteredThisMonth = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
    ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    //->orWhere('status', "Completed");// DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id')
    ;
    //$outsideRevenueThisMonth = App\Transaction::whereNotIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    $outsideRevenueThisMonth = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])
    ->where('created_at', '>=', $dateForResettingMoneyCounters)
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
        ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    //->orWhere('query_response', 'like', '%"responseCode":"01"%');//  UNIWALLET , DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
})
    ->sum('usd_to_ghs');
    //$outsideTotalRevenue = App\Transaction::whereNotIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    $outsideTotalRevenue = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('created_at', '>=', $dateForResettingMoneyCounters)
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
//->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
        ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    //->orWhere('query_response', 'like', '%"responseCode":"01"%');//  UNIWALLET , DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
                    })
    ->sum('usd_to_ghs');

    // OUTSIDE PILOT MONEY
    $outsideTotalPilotRevenue = App\Transaction::whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
    ->where('created_at', '<', $dateForResettingMoneyCounters)
    ->where('type', '!=' , 'card')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
//->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
        ->where(function($query) {
    //$query->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    //->orWhere('query_response', 'like', '%"responseCode":"01"%');//  UNIWALLET , DIRECT DEBIT
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
                    })
    ->sum('usd_to_ghs');




// GH Total Card Revenue
    $ghanaCardBaseTransactions  = App\Transaction::join('supporters', 'supporters.id', '=', 'transactions.supporter_id')
    ->whereIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->where('type', '=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
            ->where(function($query) {

                    // $query->orWhere('query_response', 'like', '%"responseCode":"01"%')//  UNIWALLET
                    // ->orWhere('status', "Completed");// DIRECT DEBIT
                    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
                    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
                    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
                    ->orWhere('status', "Completed");// DIRECT DEBIT
                });
    
    $ghanaCardTotalRevenue =     $ghanaCardBaseTransactions->sum('amount');
    // dd($ghanaCardTotalRevenue);

    //$successTotal = count($allSupporters->get());

    $ghanaCardTotalPaidSupporters = count($ghanaCardBaseTransactions->get());

     // dd($ghanaCardTotalPaidSupporters);

    // Outside Total Revenue
    $outsideCardBaseTransactions = App\Transaction::join('supporters', 'supporters.id', '=', 'transactions.supporter_id')
    // ->whereNotIn('supporter_id', App\Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->whereIn('supporter_id', App\Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
    ->where('type', '=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')   
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
            ->where(function($query) {
                    // $query->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
                    //       ->orWhere('query_response', 'like', '%"responseCode":"01"%');//  UNIWALLET , DIRECT DEBIT
                    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
                    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
                    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
                    ->orWhere('status', "Completed");// DIRECT DEBIT
                });
    
    $outsideCardTotalRevenue = $outsideCardBaseTransactions->sum('usd_to_ghs');
   // $outsideCardTotalRevenue = $outsideCardBaseTransactions->sum('amount');

    //dd($outsideCardTotalRevenue); 

    $outsideCardTotalPaidSupporters = count($outsideCardBaseTransactions->get());

     // dd($outsideCardTotalPaidSupporters);



        // Totals
    $revenueThisMonth = $ghanaRevenueThisMonth + $outsideRevenueThisMonth;
    //$totalRevenue = 24142;// $ghanaTotalRevenue + $outsideTotalRevenue;
    $totalRevenue = sprintf('%.2f', $ghanaTotalRevenue + $outsideTotalRevenue);
    $totalKotokoVISACardRevenue = sprintf('%.2f', $ghanaCardTotalRevenue + $outsideCardTotalRevenue);
         //dd($totalKotokoVISACardRevenue);
    $kotokoVISACardTotalPaidSupporters = $ghanaCardTotalPaidSupporters + $outsideCardTotalPaidSupporters;
    // dd($kotokoVISACardTotalPaidSupporters);
    $totalPilotRevenue = sprintf('%.2f', $ghanaTotalPilotRevenue + $outsideTotalPilotRevenue);

    @endphp
    <!-- Welcome card -->
    <div class="row">


    </div>
    <!-- END Welcome -->

    <!-- Graph Starts -->
    <div class="row">
    <div class="col-12">
    <h1 class="text-center">Revenue Generated</h1>
    <!-- <h2></h2> -->
    <canvas id="myChart" width="1000" height="400"></canvas>
    </div>
    </div>
   <!-- Graph Ends -->

    <!-- Welcome card -->
    <div class="row mb-5">


    </div>
    <!-- END Welcome -->

    <!-- Stats -->
    
    <div class="row">
            
    <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                <div class="font-size-sm font-w600 text-uppercase text-muted"> Total Registrations<br> &nbsp;</div>
                <div class="font-size-h2 font-w400 text-dark"> {{ number_format(round($realTotalRegisteredSupporters, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL PAID REGISTRATIONS<br> &nbsp;</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($allSupporters, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>


        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                <div class="font-size-sm font-w600 text-uppercase text-muted"> Total Unpaid<br>Registrations</div>
                <div class="font-size-h2 font-w400 text-dark"> {{ number_format(round($unpaidRegisteredSupporters, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL REVENUE<br> &nbsp;</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($totalRevenue, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">

    <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Total supporters <br>paid for Visa Card</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($kotokoVISACardTotalPaidSupporters, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>

        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted"> Total Kotoko <br>Visa Card Payment</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($totalKotokoVISACardRevenue, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>

        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3 ">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REGISTERED THIS MONTH<br> &nbsp;</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($registeredThisMonth, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3 ">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REVENUE GENERATED <br>THIS MONTH</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($revenueThisMonth, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL PAID REGISTRATIONS <br> IN GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($ghanaSupporters, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>


        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REGISTERED THIS MONTH <br>IN GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($ghanaRegisteredThisMonth, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REVENUE THIS MONTH <br>GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($ghanaRevenueThisMonth, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL REVENUE FROM <br>GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($ghanaTotalRevenue, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>

    </div>

    <div class="row">
    <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL PAID REGISTRATIONS <br>OUTSIDE GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($outsideSupporters, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REGISTERED THIS MONTH <br>OUTSIDE GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">{{ number_format($outsideRegisteredThisMonth, 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">REVENUE THIS MONTH <br>OUTSIDE GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($outsideRevenueThisMonth, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                    <div class="font-size-sm font-w600 text-uppercase text-muted">TOTAL REVENUE FROM <br>OUTSIDE GHANA</div>
                    <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($outsideTotalRevenue, 0), 0, ".", ", ") }} </div>
                </div>
            </a>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border">
                <div class="font-size-sm font-w600 text-uppercase text-muted"> Total <br> Pilot Revenue</div>
                <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($totalPilotRevenue, 0), 0, ".", ", ") }}</div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border d-none">
                <div class="font-size-sm font-w600 text-uppercase text-muted"> Total <br> Pilot Revenue</div>
                <div class="font-size-h2 font-w400 text-dark">GH₵ {{ number_format(round($totalPilotRevenue, 0), 0, ".", ", ") }}</div>
                  
                </div>
            </a>
        </div>

        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border d-none">
                    <div class="font-size-sm font-w600 text-uppercase text-muted"> <br></div>
                    <div class="font-size-h2 font-w400 text-dark"> </div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-6 col-md-3 col-lg-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full totals-border d-none">
                    <div class="font-size-sm font-w600 text-uppercase text-muted"> <br></div>
                    <div class="font-size-h2 font-w400 text-dark"> </div>
                </div>
            </a>
        </div>
    </div>
    <!-- END Stats -->
</div>



{{-- GRAB GRAPTH INFO WITH CURL --}}
@php
                $curl = curl_init();
        
        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://api.dev.asantekotokosupporters.com/api/general",
            CURLOPT_URL => $url . '/admin/dashboard/charts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
       // dd($response);
        if ($err) {
            // echo "cURL Error #:" . $err;
        } else {
            $phpObj = (object) json_decode($response, TRUE); 
            //dd($phpObj);
          
            // We need to keep this to set it after flushing out after logout
            // $session_anonymous_token = $general_info->token;
        }

@endphp
@stop


<!-- 
 OneUI JS Core

            Vital libraries and plugins used in all pages. You can choose to not include this file if you would like
            to handle those dependencies through webpack. Please check out {{$url}}/assets/_es6/main/bootstrap.js for more info.

If you like, you could also include them separately directly from the {{$url}}/assets/js/core folder in the following
order. That can come in handy if you would like to include a few of them (eg jQuery) from a CDN.

{{$url}}/assets/js/core/jquery.min.js
{{$url}}/assets/js/core/bootstrap.bundle.min.js
{{$url}}/assets/js/core/simplebar.min.js
{{$url}}/assets/js/core/jquery-scrollLock.min.js
{{$url}}/assets/js/core/jquery.appear.min.js
{{$url}}/assets/js/core/js.cookie.min.js -->


@section('javascript')
<script src="{{$url}}/assets/js/oneui.core.min.js"></script>

<!--
            OneUI JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at {{$url}}/assets/_es6/main/app.js
        -->

<script src="{{$url}}/assets/js/oneui.app.min.js"></script>

<!-- Page JS Plugins -->
<script src="{{$url}}/assets/js/plugins/chart.js/Chart.bundle.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<!--         <script src="{{$url}}/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script> -->
<script src="{{$url}}/assets/js/plugins/datatables/buttons/dataTables.buttons.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.print.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.html5.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.flash.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.colVis.min.js"></script>

<!-- Page JS Code -->
<script src="{{$url}}/assets/js/pages/be_pages_dashboard.min.js"></script>
<script src="{{$url}}/assets/js/pages/be_tables_datatables.min.js"></script>

<script>
    var graphInfo = {!! json_encode($phpObj ) !!}
    console.log(graphInfo);

    var baseUrl = {!! json_encode($base_url ) !!};
            console.log("Base Url");
            console.log(baseUrl);

    var graph_labels = [];

    // GRAPH STARTS
var ctx = document.getElementById('myChart');
  var myChart = new Chart(ctx, {
      type: 'line',
      data: {
          labels: graphInfo.graph_labels,
          //labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      // labels:[ "2021-05-30", "2021-05-29", "2021-05-28", "2021-05-27", "2021-05-26", "2021-05-25", "2021-05-24" ],
        //   data.graph_labels[3], data.graph_labels[4], data.graph_labels[5], data.graph_labels[6]],
          datasets: [{
              // label: '# of Votes',
              label: 'Amount in GHS',
            //   fill: false,
            // backgroundColor: 'rgba(193,237,254,1.00)'
              // data: [12, 19, 3, 5, 2, 3],
  			  // data: [ 430, 0, 7140, 1100, 50, 200, 20 ],
  			  data: graphInfo.graph_data,
             backgroundColor: [
                'rgba(193,237,254,1.00)'
                //   'rgba(255, 99, 132, 0.2)',
                //   'rgba(54, 162, 235, 0.2)',
                //   'rgba(255, 206, 86, 0.2)',
                //   'rgba(75, 192, 192, 0.2)',
                //   'rgba(153, 102, 255, 0.2)',
                //   'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                //   'rgba(255, 99, 132, 1)',
                //   'rgba(54, 162, 235, 1)',
                //   'rgba(255, 206, 86, 1)',
                //   'rgba(75, 192, 192, 1)',
                //   'rgba(153, 102, 255, 1)',
                //   'rgba(255, 159, 64, 1)'
                'rgba(255, 0, 0, 1)'
              ],
              borderWidth: 1
          }]
      },
      options: {
        // responsive: false,
          scales: {
              y: {
                  beginAtZero: true
              }
          }
      }
  });
  // GRAPH ENDS

	// Initiate Chartjs with empty data
    // Draw default chart with page load
// var myData = {};

// var ctx = document.getElementById('myChart').getContext('2d');
//     var myChart = new Chart(ctx, {
//         type: 'line',    // Define chart type
//         data: myData    // Chart data
//     });

    function getChartData() {
        $.get(baseUrl + '/charts', function(response_data){
                    console.log("Chart data")
                    console.log(response_data);
                    graph_labels = response_data.graph_labels;
                    console.log("GRAPH LABELS");
                    console.log(graph_labels);
        			console.log('Graphp data');
        			console.log(response_data.graph_data);
// GRAPH STARTS
// var ctx = document.getElementById('myChart');
                        // myChart.destroy();
 // <canvas id="myChart" width="1000" height="400"></canvas>
        $('#chart').replaceWith('<canvas id="myChart" width="1000" height="400"></canvas>');        
  setTimeout(function () {
        ctx = document.getElementById('myChart');
     myChart =      new Chart(ctx, {
      type: 'line',
      data: {
          labels: response_data.graph_labels,
          // labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        //   data.graph_labels[3], data.graph_labels[4], data.graph_labels[5], data.graph_labels[6]],
          datasets: [{
              label: 'Amount of Revenue Generated',
              data: response_data.graph_data,
              // data: [12, 19, 3, 5, 2, 3],
              // backgroundColor: [
              //   //   'rgba(255, 99, 132, 0.2)',
              //   //   'rgba(54, 162, 235, 0.2)',
              //   //   'rgba(255, 206, 86, 0.2)',
              //   //   'rgba(75, 192, 192, 0.2)',
              //   //   'rgba(153, 102, 255, 0.2)',
              //   //   'rgba(255, 159, 64, 0.2)'
              // ],
              // borderColor: [
              //     'rgba(255, 99, 132, 1)',
              //     'rgba(54, 162, 235, 1)',
              //     'rgba(255, 206, 86, 1)',
              //     'rgba(75, 192, 192, 1)',
              //     'rgba(153, 102, 255, 1)',
              //     'rgba(255, 159, 64, 1)'
              // ],
            //   borderWidth: 1
            // backgroundColor: "rgba(0,0,0,1.0)",
            background: 'rgba(255,255,255, 0.9)',
            borderColor: "rgba(0,0,0,0.1)",
          }]
      },
      options: {
          title: {
      display: true,
      text: 'TEST'
    },
        responsive: true,
          // scales: {
          //     y: {
          //         beginAtZero: true
          //     }
          // }
        scaleShowValues: true,
  scales: {
    yAxes: [{
      ticks: {
        beginAtZero: true
      }
    }],
    xAxes: [{
      ticks: {
        autoSkip: false
      }
    }]
  }
      }
  });
        
        // myChart.update();

  }, 5000);            
  // GRAPH ENDS

    });
    }

    $(function() {
        console.log("Ready")
        //getChartData()
    });
  
  </script>

<style>
    .totals-border {
        border: 1px solid;
    }
</style>
@stop