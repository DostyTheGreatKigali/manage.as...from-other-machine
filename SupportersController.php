<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Supporter;
use App\Transaction;
use Carbon\Carbon;

class SupportersController extends Controller
{

    public function showSupporters(Request $request)
    {

        $realTotalRegisteredSupporters = count(Supporter::all());

            // GH Total Revenue
    $ghanaBaseTransactions  = Transaction::join('supporters', 'supporters.id', '=', 'transactions.supporter_id')
    ->whereIn('supporter_id', Supporter::where('country', 'Ghana')->pluck('id')->all())
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
    
    // $ghanaTotalRevenue =     $ghanaBaseTransactions->sum('amount');

    // $ghanaTotalKotokoVISACardRevenue = count($ghanaBaseTransactions->get());

    // dd($ghanaTotalRevenue);

    // Outside Total Revenue
    $outsideBaseTransactions = Transaction::join('supporters', 'supporters.id', '=', 'transactions.supporter_id')
    // ->whereNotIn('supporter_id', Supporter::where('country', 'Ghana')->pluck('id')->all())
    ->whereIn('supporter_id', Supporter::where('country', '!=', 'Ghana')->pluck('id')->all())
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
    
    // $outsideTotalRevenue = $outsideBaseTransactions->sum('usd_to_ghs');

    // $outsideTotalKotokoVISACardRevenue = count($outsideBaseTransactions->get());


    //dd($outsideTotalRevenue);

        // $totalKotokoVISACardRevenue = sprintf('%.2f', $ghanaTotalRevenue + $outsideTotalRevenue);
        // dd($totalKotokoVISACardRevenue);

        // array_push($myArr, 5, 8);
        // print_r($myArr); 

    // $totalVisaCardPaidSupporters = $ghanaTotalKotokoVISACardRevenue + $outsideTotalKotokoVISACardRevenue;
    // dd(gettype(json_decode($ghanaBaseTransactions->get())));
    // dd(gettype(json_decode($ghanaBaseTransactions->get())));
    // $ghanaPiad = json_decode($ghanaBaseTransactions->get());
    // $outsidePiad = json_decode($outsideBaseTransactions->get());

    // MERGE TWO OR MORE ARRAYS
    // https://www.php.net/manual/en/function.array-merge.php
    // https://www.hashbangcode.com/article/append-one-array-another-php
    // $totalVisaCardPaidSupporters = array_merge($ghanaPiad, $outsidePiad);
    // dd($totalVisaCardPaidSupporters);
        
        $dateIsValid = FALSE;


        $allSupporters = Supporter::whereIn('id', Transaction::where('response', 'not like', '%"newMandateId"%')
        ->where('status', '!=' , 'Refunded')
        ->where('status', '!=' , 'Recheck Failed')
        ->whereNotNull('status')
        ->where(function($query) {
        //  $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
        // ->orWhere('status', "Completed")// DIRECT DEBIT
        // ->orWhere('query_response', 'like', '%"status":"success"%'); // PAYSTACK
        $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
        ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
        ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
        ->orWhere('status', "Completed");// DIRECT DEBIT
        
        })
        ->distinct('supporter_id')->pluck('supporter_id')->all());

        // dd($allSupporters);

        $allSupportersVisaCard = Supporter::whereIn('id', Transaction::where('type', '=' , 'card')
        ->where('response', 'not like', '%"newMandateId"%')
        ->where('status', '!=' , 'Refunded')
        ->where('status', '!=' , 'Recheck Failed')
        ->whereNotNull('status')
        ->where(function($query) {
        //  $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
        // ->orWhere('status', "Completed")// DIRECT DEBIT
        // ->orWhere('query_response', 'like', '%"status":"success"%'); // PAYSTACK
        $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
        ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
        ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
        ->orWhere('status', "Completed");// DIRECT DEBIT
        
        })
        ->distinct('supporter_id')->pluck('supporter_id')->all());
        // ->limit(5)
        // ->get();

    //    dd($allSupportersVisaCard);

        // set_time_limit(300);
        // $allSupporters = Supporter::select('*')->where('subscribed', TRUE);
        // $allSupporters = Supporter::all();
        

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $monthInterval = "";

                //Filter by date
                if ($request->start_date !== null && $request->end_date !== null) {

                    //
                    if ($request->start_date == $request->end_date) {
                            // Set date range for display
                            $monthInterval = Carbon::parse($request->start_date)->format('jS F');
                            // dd($monthInterval);
                    }
        
                    // Start date must not be >= End Date
                    if($startDate > $endDate) {
                        $message = 'Start Date, ' . Carbon::parse($startDate)->toFormattedDateString() . ', cannot be greater than End Date, '. Carbon::parse($endDate)->toFormattedDateString();
                        // $message = "The Min Age cannot be greater than or equal to the Max Age";
                        echo "<script type='text/javascript'>alert('$message');</script>";
                    }
        
                    // if ($request->start_date == Carbon::$startDate->startOfMonth && $request->end_date == $startDate->endOfMonth) {
                    //     $monthInterval = Carbon::parse($request->start_date)->format('F');
                    //     dd($monthInterval);
                    // }
        
                    // Check if Start Date is less than End Date
                    if ($request->start_date < $request->end_date) 
                    {
                            // Set date range for display
                            $monthInterval = Carbon::parse($request->start_date)->format('jS F') . " to " . Carbon::parse($request->end_date)->format('jS F');
                            // dd($monthInterval);
                    }
                    
                // if (Carbon::parse($request->start_date)->format('F') == Carbon::parse($request->end_date)->format('F')) {
                // $monthInterval = Carbon::parse($request->start_date)->format('F');
                // // dd($monthInterval);
                // } 
            // else {
            //     $monthInterval = Carbon::parse($request->start_date)->format('F') . " to " . Carbon::parse($request->end_date)->format('F');
            //     dd($monthInterval);
            //     }
                    
                    $start_date = Carbon::parse($request->start_date);
                    // dd($start_date);
                    // 1 second before the next day
                    $end_date = Carbon::parse($request->end_date)->setTime(23, 59, 59);
                    $dateIsValid = $start_date->lessThan($end_date);
                    // dd($end_date);
                    // dd( $dateIsValid);
        
                }

                if ($dateIsValid) {
                    $allSupporters = $allSupporters->whereBetween('created_at', [$start_date->format('Y-m-d H:i:s'), $end_date->format('Y-m-d H:i:s')]);    
                    $allSupportersVisaCard = $allSupportersVisaCard->whereBetween('created_at', [$start_date->format('Y-m-d H:i:s'), $end_date->format('Y-m-d H:i:s')]);    
                }

         
        $minAge = $request->min_age;
        // dd($minAge);
        $maxAge = $request->max_age;

        // Filter by REGION
        // if (!empty($request->supporter_type)) {
        //     instead of
        //     if ($request->supporter_type !== null) {
        if ($request->country !== null) {
            // if (!empty($request->region)) {
            $country = $request->country;
            // dd($region);
            $allSupporters = $allSupporters->where('country', $country);
            $allSupportersVisaCard = $allSupportersVisaCard->where('country', $country);
            // dd($allSupporters->get());
        }

        if ($request->region !== null) {
            // if (!empty($request->region)) {
            $region = $request->region;
            // dd($region);
            $allSupporters = $allSupporters->where('region', $region);
            $allSupportersVisaCard = $allSupportersVisaCard->where('region', $region);
            // dd($allSupporters->get());
        }
        // $region = $request->region;
        // dd($region);
        // dd(empty($region));
        // Filter by CITY
        // if (!empty($request->region)) {
        if ($request->city !== null) {
            $city = $request->city;
            // dd($city);
            // $allSupporters = $allSupporters->where('city', 'like', '%' . $city . '%');
            $allSupporters = $allSupporters->where(function ($q) use($city) {               
                $q->where('city', 'iLIKE', '%'.$city.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            $allSupportersVisaCard = $allSupportersVisaCard->where(function ($q) use($city) {               
                $q->where('city', 'iLIKE', '%'.$city.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            // dd($allSupporters->get());
        }

        // Filter by Suburb
        if ($request->suburb !== null) {
            $suburb = $request->suburb;
            // dd($city);
            // $allSupporters = $allSupporters->where('suburb', 'like', '%' . $suburb . '%');
            $allSupporters = $allSupporters->where(function ($q) use($suburb) {               
                $q->where('suburb', 'iLIKE', '%'.$suburb.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            $allSupportersVisaCard = $allSupportersVisaCard->where(function ($q) use($suburb) {               
                $q->where('suburb', 'iLIKE', '%'.$suburb.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            // dd($allSupporters->get());
        }

        // Filter by AGE RANGE
        // if (!empty($request->region)) {
        if ($request->min_age !== null && $request->max_age !== null) {
            $minAge = $request->min_age;
            // dd($minAge);
            $maxAge = $request->max_age;
            // dd($maxAge); 

            if ($minAge < $maxAge) {
                // dd("The age pairing is wrong");
                // dd($minAge . " is less than" . $maxAge);
                $allSupporters = $allSupporters->whereBetween('age', [$minAge, $maxAge]);
                $allSupportersVisaCard = $allSupportersVisaCard->whereBetween('age', [$minAge, $maxAge]);
                // $allSupporters = $allSupporters->where('age', $allSupporters);
                // dd($allSupporters->get());
            } else {
                // echo "The Min Age cannot be greater than or equal to the Max Age";
                $message = 'Your choice of Min Age, ' . $minAge . ', cannot be greater than or equal to your choice of Max Age, '. $maxAge;
                // $message = "The Min Age cannot be greater than or equal to the Max Age";
                echo "<script type='text/javascript'>alert('$message');</script>";
                // return back()->with('error', 'Your choice of Min Age ' . $minAge . ' cannot be greater than or equal to your choice of Max Age '. $maxAge);
                // dd("The age pairing is wrong");
                // dd($minAge . " is less than" . $maxAge);
                // $allSupporters = $allSupporters->whereBetween('age', [$minAge, $maxAge]);
                // $allSupporters = $allSupporters->where('age', $allSupporters);
                // dd($allSupporters->get());
            }
        }

        // Filter by CIRCLE MEMBERSHIP
        //https://laracasts.com/discuss/channels/laravel/how-to-store-the-radio-button-values-into-database?page=1
        //How to store the radio button values into database
        // $value_to_insert = Input::get('optradio1') == 'true' ? 1 : 0;

        if ($request->is_circle_member !== null) {
            $circleMember = $request->is_circle_member == 'true' ? 1 : 0;
            // dd($circleMember);
            $allSupporters = $allSupporters->where('is_circle_member', $circleMember);
            $allSupportersVisaCard = $allSupportersVisaCard->where('is_circle_member', $circleMember);
        }

        // Filter by CIRLCLE NUMBER
        if ($request->circle_number !== null) {
            // if ($request->is_circle_member == "false") {
            //   $message = "Please choose 'Yes' for Circle Member ";
            //   echo "<script type='text/javascript'>alert('$message');</script>";
            // }
            $circleNumber = $request->circle_number;
            // dd($circleNumber);
            $allSupporters = $allSupporters->where('circle_number', $circleNumber);
            $allSupportersVisaCard = $allSupportersVisaCard->where('circle_number', $circleNumber);
        }
        // FILTER BY PHONE NUMBER
        if ($request->phone !== null) {
            $phone = $request->phone;
            // dd($circleNumber);
            $allSupporters = $allSupporters->where('phone', $phone);
            $allSupportersVisaCard = $allSupportersVisaCard->where('phone', $phone);
        }

        // Filter by PAYMENT METHOD
        if (!empty($request->network)) {
            // if ($request->payment_method !== null) {
            $paymentMethod = $request->network;
            // dd($paymentMethod);
            $allSupporters = $allSupporters->where('network', $paymentMethod);
            $allSupportersVisaCard = $allSupportersVisaCard->where('network', $paymentMethod);
        }

        // Filter by SUPPORTER TYPE
        if (!empty($request->supporter_type)) {
            // if ($request->supporter_type !== null) {
            $supporterType = $request->supporter_type;
            // dd($supporterType);
            // $allSupporters = $allSupporters->where('supporter_type', $supporterType);
            // $allSupporters = $allSupporters->where('supporter_type', 'like', '%' .  $supporterType. '%');
            $allSupporters = $allSupporters->where(function ($q) use($supporterType) {               
                $q->where('supporter_type', 'iLIKE', '%'.$supporterType.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            $allSupportersVisaCard = $allSupportersVisaCard->where(function ($q) use($supporterType) {               
                $q->where('supporter_type', 'iLIKE', '%'.$supporterType.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
        }

        // Filter by SUPPORTER's FULL NAME
        if (!empty($request->fullname)) {
            // if ($request->supporter_type !== null) {
            $fullname = $request->fullname;
            // dd($supporterType);
            // $allSupporters = $allSupporters->where('supporter_type', $supporterType);
            // $allSupporters = $allSupporters->where('full_name', 'like', '%' .  $fullname. '%');
            $allSupporters = $allSupporters->where(function ($q) use($fullname) {               
                $q->where('full_name', 'iLIKE', '%'.$fullname.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            $allSupportersVisaCard = $allSupportersVisaCard->where(function ($q) use($fullname) {               
                $q->where('full_name', 'iLIKE', '%'.$fullname.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
        }
        // $allSupporters = $allSupporters->where('age', 'like', '%"result":1%');

        //  Filter by Supporters Group
        // if ($request->supporter_group !== null) {
        //     $supporter_group = $request->supporter_group;
        //     dd($supporter_group);
        //     $allSupporters = $allSupporters->where('group_name', 'like', '%' . $supporter_group . '%');
        //     // dd($allSupporters->get());
        // }

        // CASE INSENSITIVE LIKE SEARCH
        // https://stackoverflow.com/questions/51497890/how-to-search-case-insensitive-in-eloquent-model/67317285#67317285

        // $transactions = Transaction::where('category_id', $category_id);
        // $transactions = $transactions->where(function ($q) use($keyword) {               
        //     $q->where('firstName', 'iLIKE', '%'.$keyword.'%');
        //     $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
        // });
       
        if (!empty($request->supporter_group)) {
            // if ($request->supporter_type !== null) {
            $supporter_group = $request->supporter_group;
            // dd($supporter_group);
            // $allSupporters = $allSupporters->where('supporter_type', $supporterType);
            // $allSupporters = $allSupporters->where('group_name', 'like', '%' .  $supporter_group. '%');
            $allSupporters = $allSupporters->where(function ($q) use($supporter_group) {               
                $q->where('group_name', 'iLIKE', '%'.$supporter_group.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
            $allSupportersVisaCard = $allSupportersVisaCard->where(function ($q) use($supporter_group) {               
                $q->where('group_name', 'iLIKE', '%'.$supporter_group.'%');
                // $q->orwhere('lastName', 'LIKE', '%'.$keyword.'%');
            });
        }

        $successTotal = count($allSupporters->get());

        $visaCardSuccessTotal = count($allSupportersVisaCard->get());
        // dd($visaCardSuccessTotal);

        $allSupporters = $allSupporters->paginate(100);

         // Getting site url for generating QR Code link
         $site_url = env('APP_URL') . "/supporter/details/";
        // dd($site_url);

        $supporter_names = '';

         // Loooping over each supporter and providing a qr code
        foreach($allSupporters as $supporter) {
            // dd($supporter);

            $supporter_names = $supporter->full_name;

            // Getting values for supporter image name and  image path
            $supporterId = $supporter->id;
            // dd($supporterId);
            $supporterPhone = $supporter->phone;
            // dd($supporterPhone);
            $supporterNumber = $supporter->supporter_number;
            // dd($supporterNumber);
            $forwardSlash = "/";
            $hyphen = "-";
            // Creating the supporter image name
            $supporterQRKtkCodeImageName =  $supporterNumber . $hyphen . $supporterId . $hyphen . $supporterPhone;
            
            // Creating the supporter image path
            $supporterViewQRCode = $site_url . $supporterId . $forwardSlash . $supporterNumber . $forwardSlash . $supporterPhone;
            // dd($supporterQRKtkCodeImageName);
            
            // Preventing repetitive generation of qr code
            if (!file_exists(public_path('ktk_codes_supporters/' . $supporterQRKtkCodeImageName . '.png'))) {
                $ktkSupporterQRCode = \QrCode::size(500)
                    ->format('png')
                    // ->generate($supporterUrl, public_path('ktk_codes_supporters/' . $supporterQRKtkCodeImageName . '.png'));
                    ->generate($supporterViewQRCode, public_path('ktk_codes_supporters/' . $supporterQRKtkCodeImageName . '.png'));
                // dd(public_path());
                // ->generate('www.google.com', public_path('ktk_codes_supporters/' . $supporterQRKtkCodeImageName . '.png'));
                // dd($ktkSupporterQRCode);
            }
        }

        // Displaying TOTAL SEARCH RESULTS
        // $successTotal = count($allSupporters);

        // $fields = [
        //     'min_age',
        //     'max_age',
        //     'region',
        //     'city',
        //     'is_circle_member',
        //     'circle_number',
        //     'payment_method',
        //     'supporter_type',
        // ];
        // foreach ($fields as $field) {
        //     if (empty($request->$field)) {
        //         // $message = "You are enter some search text to see results";
        //         // echo "<script type='text/javascript'>alert('$message');</script>";
        //         $allSupporters = $allSupporters->get();
        //     }
        // }

        $api_url = env('API_URL');

        return view('supporters', [
            'successTotal' => $successTotal,
            'allSupporters' => $allSupporters,
            // 'end_date' => $end_date,
            'site_url' => $site_url,
            'supporter_names' => $supporter_names,
            'api_url' => $api_url,
            'monthInterval' => $monthInterval,
            'totalKotokoVISACardRevenue' => $totalKotokoVISACardRevenue,
            'realTotalRegisteredSupporters' => $realTotalRegisteredSupporters,
            'visaCardSuccessTotal' => $visaCardSuccessTotal,
        ]);
    }


    public function showSupporterDetails(Request $request, $supporter_id)
    {
        $supporterDetail = Supporter::find($supporter_id);
        if($supporterDetail !== null) {
            // dd($supporterDetail);
        // Getting site url for generating QR Code link
        $site_url = env('APP_URL') . "/supporter/details/";
        return view('supporter-detail', [
            'supporterDetail' => $supporterDetail,
            'site_url' => $site_url,
        ]);
        }
        return "No such existing record";

    }

    public function showUnauthorizedSupporterDetails(Request $request, $supporter_id, $supporter_number, $supporter_phone) {
        // public function showUnauthorizedSupporterDetails(Request $request, $supporter_id) {
    
            $supporterDetail = Supporter::find($supporter_id);
            // dd($supporterDetail);
            if($supporterDetail !== null) {
                $supporter_phone = $supporterDetail->phone;
                // dd($supporter_phone);
                $supporter_number = $supporterDetail->supporter_number;
                // dd($supporter_number);
    
            return view('unauthorized_supporter_details', [
                'supporterDetail' => $supporterDetail,
                'supporter_phone' => $supporter_phone,
                'supporter_number' => $supporter_number,
                ]);
            }
    
            return "No such existing record";        
            
        }

    public function regionalSupporters()
    {
        // $allSupporters = Supporter::where('subscribed', TRUE)->get();
        $allSupporters = Supporter::all();
        // dd(count($allSupporters));
        // $allSupporters = Supporter::select('*')->where('subscribed', TRUE)->get();
        // $ashantiSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Ashanti')->pluck('id')->all())->distinct('supporter_id')->count('supporter_id');
        // $ashantiSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Ashanti')->pluck('id')->all())->count('supporter_id');
        $ashantiSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Ashanti'));
        // dd($ashantiSupporters);

        // Only paid
    // $ashantiSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Ashanti')->pluck('id')->all())
    // ->where('response', 'not like', '%"newMandateId"%')
    // ->where('status', '!=' , 'Refunded')
    // ->where('status', '!=' , 'Recheck Failed')
    // ->whereNotNull('status')
    // ->where(function($query) {

    // //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // // B4 ZEEPAY
    // // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    // $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    // ->orWhere('status', "Completed");// DIRECT DEBIT
    // })
    // ->distinct('supporter_id')
    // ->count('supporter_id');

      $ashantiPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Ashanti')->pluck('id')->all())
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

    //   dd($ashantiPaidSupporters);


    $ashantiUnpaidSupporters = $ashantiSupporters - $ashantiPaidSupporters;
    // dd($ashantiUnpaidSupporters);

    $accraSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Greater Accra'));
    
    $accraPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Greater Accra')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $accraUnpaidSupporters = $accraSupporters - $accraPaidSupporters;


    $easternSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Eastern'));

    $easternPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Eastern')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $easternUnpaidSupporters = $easternSupporters - $easternPaidSupporters;


    $westernSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Western'));

    $westernPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Western')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $westernUnpaidSupporters = $westernSupporters - $westernPaidSupporters;


    $westernNorthSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Western North'));

    $westernNorthPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Western North')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $westernNorthUnpaidSupporters =$westernNorthSupporters - $westernNorthPaidSupporters;


    $bonoSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Bono'));

    $bonoPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Bono')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $bonoUnpaidSupporters = $bonoSupporters - $bonoPaidSupporters;


    $bonoEastSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Bono East'));

    $bonoEastPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Bono East')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $bonoEastUnpaidSupporters = $bonoEastSupporters - $bonoEastPaidSupporters;


    $ahafoSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Ahafo'));

    $ahafoPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Ahafo')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $ahafoUnpaidSupporters = $ahafoSupporters - $ahafoPaidSupporters;


    $centralSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Central'));

    $centralPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Central')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $centralUnpaidSupporters = $centralSupporters - $centralPaidSupporters;


    $northernSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Northern'));

    $northernPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Northern')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $northernUnpaidSupporters = $northernSupporters - $northernPaidSupporters;


    $voltaSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Volta'));

    $voltaPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Volta')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $voltaUnpaidSupporters = $voltaSupporters - $voltaPaidSupporters;


    $otiSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Oti'));

    $otiPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Oti')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $otiUnpaidSupporters = $otiSupporters - $otiPaidSupporters;


    $savanahSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Savanah'));

    $savanahPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Savanah')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $savanahUnpaidSupporters = $savanahSupporters - $savanahPaidSupporters;


    $northWestSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'North West'));

    $northWestPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'North West')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $northWestUnpaidSupporters = $northWestSupporters - $northWestPaidSupporters;


    $upperEastSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Upper East'));

    $upperEastPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Upper East')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $upperEastUnpaidSupporters = $upperEastSupporters - $upperEastPaidSupporters;

    $upperWestSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Upper West'));
   
    $upperWestPaidSupporters = Transaction::whereIn('supporter_id', Supporter::where('country', 'Ghana')->where('region', 'Upper West')->pluck('id')->all())
    ->where('type', '!=' , 'card')
    ->where('response', 'not like', '%"newMandateId"%')
    ->where('status', '!=' , 'Refunded')
    ->where('status', '!=' , 'Recheck Failed')
    ->whereNotNull('status')
    ->where(function($query) {

    //->where('query_response', 'like', '%' . '"result-text":"Success"' . '%')
    // $query->orWhere('query_response', 'like', '%"result":1%')// EXPRESSPAY
    // ->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET
    // //->orWhere('query_response', 'like', '%"status":"Completed"%')// DIRECT DEBIT
    // ->orWhere('status', "Completed")// DIRECT DEBIT
    // // ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    // ->orWhere('query_response', 'like', '%"code":"000"%')// PAYSWITCH
    // ->orWhere('init_response', 'like', '%"code":"000"%');// PAYSWITCH
    $query->orWhere('query_response', 'like', '%"responseCode":"01"%')// UNIWALLET , DIRECT DEBIT
    ->orWhere('query_response', 'like', '%"status":"success"%')// PAYSTACK
    ->orWhere('query_response', 'like', '%"code":200%')// ZEEPAY
    ->orWhere('status', "Completed");// DIRECT DEBIT
    })
    ->distinct('supporter_id')
    ->count('supporter_id');

    $upperWestUnpaidSupporters = $upperWestSupporters - $upperWestPaidSupporters;

        // Regional Supporters
        // $ashantiSupporters = count($allSupporters->where('region', 'Ashanti'));
        // $accraSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Greater Accra'));
        // $easternSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Eastern'));
        // $westernSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Western'));
        // $westernNorthSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Western North'));
        // $bonoSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Bono'));
        // $bonoEastSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Bono East'));
        // $ahafoSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Ahafo'));
        // $centralSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Central'));
        // $northernSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Northern'));
        // $voltaSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Volta'));
        // $otiSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Oti'));
        // $savanahSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Savanah'));
        // $northWestSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'North West'));
        // $upperEastSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Upper East'));
        // $upperWestSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Upper West'));

        // $ashantiSupporters = count($allSupporters->where('country', 'Ghana')->where('region', 'Ashanti'));

        // dd($ashSupporters);


        return view('supporters-regional', [
            'ashantiSupporters' => $ashantiSupporters,
            'accraSupporters' => $accraSupporters,
            'easternSupporters' => $easternSupporters,
            'westernSupporters' => $westernSupporters,
            'westernNorthSupporters' => $westernNorthSupporters,
            'bonoSupporters' => $bonoSupporters,
            'bonoEastSupporters' => $bonoEastSupporters,
            'ahafoSupporters' => $ahafoSupporters,
            'centralSupporters' => $centralSupporters,
            'northernSupporters' => $northernSupporters,
            'voltaSupporters' => $voltaSupporters,
            'otiSupporters' => $otiSupporters,
            'savanahSupporters' => $savanahSupporters,
            'northWestSupporters' => $northWestSupporters,
            'upperEastSupporters' => $upperEastSupporters,
            'upperWestSupporters' => $upperWestSupporters,

            // PAID
            'ashantiPaidSupporters' => $ashantiPaidSupporters,
            'accraPaidSupporters' => $accraPaidSupporters,
            'easternPaidSupporters' => $easternPaidSupporters,
            'westernPaidSupporters' => $westernPaidSupporters,
            'westernNorthPaidSupporters' => $westernNorthPaidSupporters,
            'bonoPaidSupporters' => $bonoPaidSupporters,
            'bonoEastPaidSupporters' => $bonoEastPaidSupporters,
            'ahafoPaidSupporters' => $ahafoPaidSupporters,
            'centralPaidSupporters' => $centralPaidSupporters,
            'northernPaidSupporters' => $northernPaidSupporters,
            'voltaPaidSupporters' => $voltaPaidSupporters,
            'otiPaidSupporters' => $otiPaidSupporters,
            'savanahPaidSupporters' => $savanahPaidSupporters,
            'northWestPaidSupporters' => $northWestPaidSupporters,
            'upperEastPaidSupporters' => $upperEastPaidSupporters,
            'upperWestPaidSupporters' => $upperWestPaidSupporters,

            // UNPAID
            'ashantiUnpaidSupporters' => $ashantiUnpaidSupporters,
            'accraUnpaidSupporters' => $accraUnpaidSupporters,
            'easternUnpaidSupporters' => $easternUnpaidSupporters,
            'westernUnpaidSupporters' => $westernUnpaidSupporters,
            'westernNorthUnpaidSupporters' => $westernNorthUnpaidSupporters,
            'bonoUnpaidSupporters' => $bonoUnpaidSupporters,
            'bonoEastUnpaidSupporters' => $bonoEastUnpaidSupporters,
            'ahafoUnpaidSupporters' => $ahafoUnpaidSupporters,
            'centralUnpaidSupporters' => $centralUnpaidSupporters,
            'northernUnpaidSupporters' => $northernUnpaidSupporters,
            'voltaUnpaidSupporters' => $voltaUnpaidSupporters,
            'otiUnpaidSupporters' => $otiUnpaidSupporters,
            'savanahUnpaidSupporters' => $savanahUnpaidSupporters,
            'northWestUnpaidSupporters' => $northWestUnpaidSupporters,
            'upperEastUnpaidSupporters' => $upperEastUnpaidSupporters,
            'upperWestUnpaidSupporters' => $upperWestUnpaidSupporters,
        ]);
    }
}
// 
