@php 
foreach ($allSupporters as $supporter ) {
    $country = $supporter->country;
 //dd($supporter->id);
}
@endphp

@extends('master.layout')

@php
$url = url('/');
@endphp

@php
//dd($allSupporters);
@endphp

<?php
$user = \Auth::user();

$url_to_admin = url('/') . '/admin';

// $financierUrl = url('/') . '/admin/dashboard/subscriptions';
// dd($url);
// if ($user->hasRole('executive')) {
//     header('Location: ' . $financierUrl, true);

//     exit();
// }

if ($user == null) {
    header('Location: ' . $url_to_admin, true);

    exit();
}

if ($user->hasRole('republic_bank_admin')) {
    header('Location: ' . $url, true);

    exit();
}

?>

@section('content')
<style>
#supporters_table_paginate {
    display: none;
}
  /* Hiding Number input Arrows */
  input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
-webkit-appearance: none;
margin: 0;
}
input[type="number"] {
-moz-appearance: textfield;
}

.btn-red {
    /* background: #e20000; */
}

#main-container {
    background: #fff;
}
</style>
<div class="content">
  <!-- Supporters' Monthly Contribution -->
  <div class="col-lg-12">
  <div class="block block-mode-loading-oneui">
                <div class="block-header block-header-default">
                    <!-- <h3 class="block-title">Supporters' Monthly Contribution</h3> -->
                    <h3 class="block-title">Supporters</h3>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 4 )
                    <div class="regional-supporters-link text-center">
                        <a href="{{ url('/admin/dashboard/supporters/regional') }}" class="btn btn-alt-danger">Regional Supporters Totals</a>
                    </div>
                    @endif
                    <div class="block-options">
                    <a href="{{ url('/admin/dashboard/supporters') }}" style="color: #e40101;" class="btn" >
                            <i class="si si-refresh"></i>&nbsp;
                    </a>
                        <!-- <button type="button" style="color: #e40101;" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                            <i class="si si-refresh"></i>&nbsp;
                        </button> -->
                        <!--<button type="button" style="color: #e40101;" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                            <i class="si si-printer"></i>
                        </button>-->
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="text-center">
                        <form method="get">
                        @include('partials.flash-message')

                        @php
                            $country_array = array("Afghanistan", "Aland Islands", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Barbuda", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Trty.", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Caicos Islands", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories", "Futuna Islands", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard", "Herzegovina", "Holy See", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Jan Mayen Islands", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea", "Korea (Democratic)", "Kuwait", "Kyrgyzstan", "Lao", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "McDonald Islands", "Mexico", "Micronesia", "Miquelon", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "Nevis", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestinian Territory, Occupied", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Principe", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Barthelemy", "Saint Helena", "Saint Kitts", "Saint Lucia", "Saint Martin (French part)", "Saint Pierre", "Saint Vincent", "Samoa", "San Marino", "Sao Tome", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia", "South Sandwich Islands", "Spain", "Sri Lanka", "Sudan", "Suriname", "Svalbard", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "The Grenadines", "Timor-Leste", "Tobago", "Togo", "Tokelau", "Tonga", "Trinidad", "Tunisia", "Turkey", "Turkmenistan", "Turks Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "US Minor Outlying Islands", "Uzbekistan", "Vanuatu", "Vatican City State", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (US)", "Wallis", "Western Sahara", "Yemen", "Zambia", "Zimbabwe");
                        @endphp
                        <fieldset>
                        <div class="row">
                        <div class="col-md-4">
                        <select id="country" name="country" class="form-control custom-select">
                            <option value="">Choose Country</option>
                            <option value="">All</option>
                            @foreach ($country_array as $country)
                            @if (isset(request()->country) && !empty(request()->country) && request()->country == $country)
                                <option value="{{$country}}" selected="selected">{{$country}}</option>                                                                
                            @else                                                                    
                                <option value="{{$country}}" >{{$country}}</option>                                                                    
                            @endif                                                                                                                                    
                            @endforeach
                        </select>
                        </div>

                        @php
                        $regions =  ["Ahafo", "Ashanti", "Bono", "Bono East", "Central", "Eastern", "Greater Accra", 
                        "Northern",
                        "North West", 
                        "Oti",
                        "Savanah",
                        "Upper East", 
                        "Upper West", 
                        "Volta",
                        "Western",
                        "Western North"]
                        @endphp 
                        
                        <div class="col-md-4">
                            <select id="region" name="region" class="form-control custom-select">
                            <option value="">Choose Region</option>
                            <option value="">All</option>
                                    @foreach ($regions as $region)
                                    @if (isset(request()->region) && !empty(request()->region) && request()->region == $region)
                                        <option value="{{$region}}" selected="selected">{{$region}}</option>                                                                
                                    @else                                                                    
                                        <option value="{{$region}}" >{{$region}}</option>                                                                    
                                    @endif                                                                                                                                    
                                    @endforeach      
                                </select>
                        </div>
                        <div class="col-md-4">
                                <!-- <label for="city">Town/City:</label> -->
                                <input class="form-control form-control-lg" id="city" value="{{(isset(request()->city) && !empty(request()->city)) ? request()->city : ''}}" name="city" type="text" placeholder="Town/City"> 
                        </div>

                        </div> <br>
                                <!-- <select name="region" id="region">
                                    <option value="">Choose Region</option>
                                    <option value="">All</option>
                                    <option value="Ashanti">Ashanti</option>
                                    <option value="Greater Accra">Greater Accra</option>
                                    <option value="Eastern">Eastern</option>
                                    <option value="Western">Western</option>
                                    <option value="Western North">Western North</option>
                                    <option value="Bono">Bono</option>
                                    <option value="Bono East">Bono East</option>
                                    <option value="Ahafo">Ahafo</option>
                                    <option value="Central">Central</option>
                                    <option value="Northern">Northern</option>
                                    <option value="Volta">Volta</option>
                                    <option value="Oti">Oti</option>
                                    <option value="Savanah">Savanah</option>
                                    <option value="North West">North West</option>
                                    <option value="Upper East">Upper East</option>
                                    <option value="Upper West">Upper West</option>
                                </select> -->

                                <select id="status_filter" name="status_filter" style="display:none">
                                    <option value="all" @if(request()->status_filter !== null && request()->status_filter == "all") selected @endif>All</option>
                                    <option value="success" @if(request()->status_filter !== null && request()->status_filter == "success") selected @endif>Successful</option>
                                    <option value="failed" @if(request()->status_filter !== null && request()->status_filter == "failed") selected @endif>Failed</option>
                                </select>

                                <div class="row">
                               <div class="col-md-6">
                               <label class="">Registration Date Range</label>
                               <div class="row">
                                    <div class="col-md-6">
                                        <label for="start_date">Start Date</label>
                                        <input class="form-control form-control-lg" id="start_date" type="date" name="start_date" id="start_date" 
                                        value="{{(isset(request()->start_date) && !empty(request()->start_date)) ? request()->start_date : ''}}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date">End Date</label>
                                        <input class="form-control form-control-lg" id="end_date" type="date" name="end_date" id="end_date" 
                                        value="{{(isset(request()->end_date) && !empty(request()->end_date)) ? request()->end_date : ''}}">
                                    </div>
                                </div>
                                </div>

                                <!-- <div class="col-md-8" style="background-color: yellow"> -->
                                <div class="col-md-6">
                                <label class="">Age Range</label>
                                <div class="row">
                                <div class="col-md-6">
                                <label for="minAge">Minimum Age</label>
                                <input class="form-control form-control-lg" id="min_age" value="{{(isset(request()->min_age) && !empty(request()->min_age)) ? request()->min_age : ''}}" min="1" name="min_age" type="number" 
                                placeholder="Min Age">
                                </div>
                                <div class="col-md-6">
                                <label for="maxAge">Maximum Age</label>
                                <input class="form-control form-control-lg" id="max_age" value="{{(isset(request()->max_age) && !empty(request()->max_age)) ? request()->max_age : ''}}" name="max_age" min="2" type="number" 
                                placeholder="Max Age">
                                 </div>
                                </div>
                                 </div>
                                 </div> <br>
                                <!-- <button class="btn btn-danger btn-md" type="submit" id="search">
                                    <i class="fas fa-search"></i>
                                </button>
                                <br /> -->

                                <div class="row">
                                <div class="col-md-4">
                                 <label>Circle Member</label>
                                <!-- <input type="radio" id="yes" name="is_circle_member"> -->
                                <div class="row">
                                <div class="col-md-6">
                                <input class="" type="radio" id="yes" name="is_circle_member" value="true" 
                                {!! isset(request()->is_circle_member) && !empty(request()->is_circle_member) && request()->is_circle_member  == 'true' ? 'checked="checked"' : '' !!}> 
                                <label for="yes">Yes</label>
                                </div>
                                <!-- <input type="radio" id="no" name="is_circle_member"> -->
                                <div class="col-md-6">
                                <input class="" type="radio" id="no" name="is_circle_member" value="false" 
                                {!! isset(request()->is_circle_member) && !empty(request()->is_circle_member) && request()->is_circle_member  == 'false' ? 'checked="checked"': '' !!}>
                                <label for="no">No</label>
                                </div>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <label for="circle_number">Circle Number</label>
                                <input class="form-control form-control-lg" type="text" id="circle_number" name="circle_number" 
                                value="{{(isset(request()->circle_number) && !empty(request()->circle_number)) ? request()->circle_number : ''}}"
                                placeholder="Circle Number">
                                </div>

                                <div class="col-md-4">
                                <label for="phone">Phone Number</label>
                                <input name="phone" type="text" class="form-control form-control-lg" id="phone" value="{{(isset(request()->phone) && !empty(request()->phone)) ? request()->phone : ''}}" 
                                placeholder="Phone Number">
                                 </div>
                                </div><br>

                                @php
                                $networks = ["MTN", "AIRTELTIGO", "VODAFONE"];
                                @endphp
                                <div class="row">
                                <div class="col-md-4">
                                <select name="network" id="network" class="form-control custom-select">
                                    <option value="">Choose Payment Method</option>
                                    <option value="">All</option>
                                    @foreach ($networks as $network)
                                    @if(isset(request()->network) && !empty(request()->network) && request()->network == $network)
                                    <option value="{{$network}}" selected="selected">{{$network}}</option>                                                                
                                    @else                                                                    
                                        <option value="{{$network}}" >{{$network}}</option>                                                                    
                                    @endif                                                                                                                                    
                                    @endforeach
                                    <!-- <option value="MTN_MM">MTN MoMo</option>
                                    <option value="V_CASH">Vodafone Cash</option>
                                    <option value="AT_MONEY">AirtelTigo Money</option> -->
                                    <!-- <option value="MTN">MTN</option>
                                    <option value="AIRTELTIGO">AIRTELTIGO</option>
                                    <option value="VODAFONE">VODAFONE</option>
                                    <option value="CREDIT_CARD">CARD</option> -->
                                </select>
                               </div>

                                @php
                                $supporter_types = ["Bronze", "Silver", "Gold", "Diamond", "Platinum"]
                                @endphp
                                <div class="col-md-4">
                                <select name="supporter_type" id="supporter_type" class="form-control custom-select">
                                    <option value="">Select Supporter Type</option>
                                    <option value="">All</option>
                                    @foreach ($supporter_types as $supporter_type)
                                    @if(isset(request()->supporter_type) && !empty(request()->supporter_type) && request()->supporter_type == $supporter_type)
                                    <option value="{{$supporter_type}}" selected="selected">{{$supporter_type}}</option>                                                                
                                    @else                                                                    
                                        <option value="{{$supporter_type}}" >{{$supporter_type}}</option>                                                                    
                                    @endif                                                                                                                                    
                                    @endforeach 
                                    <!-- <option value="Diamond">Diamond</option>
                                    <option value="Platinum">Platinum</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Bronze">Bronze</option> -->
                                </select>
                               </div>
                                <div class="col-md-4">
                                <!-- <label for="fullname">Full Name</label> -->
                                <input class="form-control form-control-lg" id="fullname" value="{{(isset(request()->fullname) && !empty(request()->fullname)) ? request()->fullname : ''}}" name="fullname" type="text" placeholder="Supporter's Full Name"> 
                               </div>
                                </div> <br>

                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <input class="form-control form-control-lg" id="supporter_group" value="{{(isset(request()->supporter_group) && !empty(request()->supporter_group)) ? request()->supporter_group : ''}}" name="supporter_group" type="text" placeholder="Supporters Group"> 
                                    </div>
                                    <div class="col-md-4"></div>
                                </div><br>
                                <button class="btn btn-block btn-primary" type="submit" id="search">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                                <br />
                            </fieldset>
                        </form>
                    </div>

                   {{-- @if(Auth::user()->role_id == 5) --}}
                    <div class="container d-none">
                    <div class="text-center mb-3">
                    <span class="p-2" style="background-color: green; color: white; font-size: 20px; border-radius: 15px"><strong>Kotoko VISA Card Total:  GH₵ {{ number_format($totalKotokoVISACardRevenue, 0, ".", ", ") }}<strong></span> 
                    </div>
                    <div class="text-right mb-3">
                    <span style="font-size: 20px;"> <strong>{{ $monthInterval }} </strong> </span>
                    <span class="p-2" style="background-color: #e20000; color: white; font-size: 20px; border-radius: 15px"><strong>Total: {{ number_format($successTotal, 0, ".", ", ") }}<strong></span> 
                    </div>
                    </div>
                    {{-- @else --}}
                    <div class="text-right mb-3">
                    <span style="font-size: 20px;"> <strong>{{ $monthInterval }} </strong> </span>
                    <span class="p-2" style="background-color: #e20000; color: white; font-size: 20px; border-radius: 15px"><strong>Total Registered: {{ number_format($realTotalRegisteredSupporters, 0, ".", ", ") }}<strong></span> 
                    </div>
                    <div class="text-right mb-3">
                    <span class="p-2" style="background-color: #000; color: white; font-size: 20px; border-radius: 15px"><strong>Paid Supporter Type: {{ number_format($successTotal, 0, ".", ", ") }}<strong></span> 
                    {{-- <span class="p-2" style="background-color: #6495ED; color: white; font-size: 20px; border-radius: 15px"><strong>Paid Supporter Type: {{ number_format($successTotal, 0, ".", ", ") }}<strong></span>  --}}
                    </div>
                    <div class="text-right mb-3">
                    <span class="p-2" style="background-color: green; color: white; font-size: 20px; border-radius: 15px"><strong>Paid Visa Card: {{ number_format($visaCardSuccessTotal, 0, ".", ", ") }}<strong></span> 
                    </div>

                    <div class="container d-none">
                        <div class="row">
                            <div class="col-md-3">
                                <span style="font-size: 16px;"> <strong>{{ $monthInterval }} </strong> </span>
                            </div>
                            <div class="col-md-3">
                           Hello
                            </div>
                            <div class="col-md-3">
                               Helo
                            </div>
                            <div class="col-md-3">
                               Hello
                            </div>
                        </div>
                    </div>

                    </div>
                   {{--  @endif --}}

                    <!-- Hide some datatable functionalities
                    https://stackoverflow.com/questions/17832742/how-to-remove-pagination-in-datatable
                     -->

                    <table data-paging="false" data-searching="false" data-info="false" class="table table-bordered table-hover js-dataTable-full-pagination" id="supporters_table">
                    <thead>
                    <tr style="color: #e40101;">
                    {{-- <th class="d-sm-table-cell">Supporter ID</th> --}}
                    <th class="d-sm-table-cell">Name</th>
                    <th class="d-sm-table-cell">Age</th>
                    <th class="d-sm-table-cell">Phone</th>
                    <th class="d-sm-table-cell">Region</th>
                    <th class="d-sm-table-cell">City</th>
                    <th class="d-sm-table-cell">Suburb / <br />Address</th>                   
                    <th class="d-sm-table-cell">Is Circle Member</th>
                    <th class="d-sm-table-cell">Circle Number</th>
                    <th class="d-sm-table-cell">Card Payment</th>
                    <th class="d-sm-table-cell">Card Printed</th>
                    <th class="d-sm-table-cell">Supporter Type</th>
                    <th class="d-sm-table-cell">Supporters Group</th>
                    <th class="d-sm-table-cell">Payment Method</th>
                    <th class="d-sm-table-cell">QR Code</th>
                    </tr>
                  </thead>
                    <tbody>
                    @foreach($allSupporters as $supporter)
                            <tr>
                           {{-- 
                               <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        {{ $supporter->id }}
                                    </div>
                                </td> 
                            --}}
                                <td class="font-w600 font-size-sm">
                                    <a href="{{url('/admin/dashboard/supporters/details/' . $supporter->id)}}" class="" id="supporter_name">
                                     {{ ucwords(strtoupper($supporter->full_name)) }}
                                    </a>
                                </td>
                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        {{ $supporter->age }}
                                    </div>
                                </td>
                                <td class="text-left font-size-sm">{{ $supporter->phone }}</td>
                                <td class="text-left font-size-sm">{{ $supporter->region }}</td>
                                <td class="text-left font-size-sm">{{ ucwords(strtoupper($supporter->city)) }}</td>
                                @if($supporter->country == 'Ghana')
                                <td class="text-left font-size-sm">{{ ucwords(strtoupper($supporter->suburb)) }}</td>
                                @endif
                                @if($supporter->country !== 'Ghana')
                                <td class="text-left font-size-sm">{{ ucwords(strtoupper($supporter->address)) }}</td>
                                @endif
                               {{-- <td class="text-left font-size-sm">{{ $supporter->city }}</td>
                                <td class="text-left font-size-sm">{{ $supporter->suburb }}</td> --}}
                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        @if( $supporter->is_circle_member == false)
                                        No
                                        @else
                                        Yes
                                        @endif
                                    </div>
                                </td>
                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        @if( $supporter->circle_number == false)
                                        None
                                        @else
                                        {{ $supporter->circle_number }}
                                        @endif
                                    </div>
                                </td>

                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        @if( $supporter->card_paid == false)
                                          No
                                        <!-- <input name="card_printed" type="checkbox"> -->
                                        @else
                                          Yes
                                        <!-- <input name="card_printed" type="checkbox" checked> -->
                                        @endif
                                    </div>

                                </td>

                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                        @if( $supporter->card_printed == false)
                                          <!-- No -->
                                        <input name="card_printed" type="checkbox">
                                        @else
                                         <!-- Yes -->
                                        <input name="card_printed" type="checkbox" checked>
                                        @endif
                                    </div>

                                </td>

                                <td class="text-left font-size-sm">
                                    @if($supporter->supporter_type == 'Bronze Foreign')
                                    <div class="text-center">
                                    Bronze
                                    </div>
                                    @elseif($supporter->supporter_type == 'Silver Foreign')
                                    <div class="text-center">
                                    Silver
                                    </div>
                                    @elseif($supporter->supporter_type == 'Gold Foreign')
                                    <div class="text-center">
                                    Gold
                                    </div>
                                    @elseif($supporter->supporter_type == 'Diamond Foreign')
                                    <div class="text-center">
                                    Diamond
                                    </div>
                                    @elseif($supporter->supporter_type == 'Platinum Foreign')
                                    <div class="text-center">
                                    Platinum
                                    </div>
                                    @else
                                    <div class="text-center">
                                    {{ $supporter->supporter_type }}
                                    </div>
                                    @endif
                                </td>

                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                    @if($supporter->group_name == '')
                                    None
                                    @else
                                    {{  ucwords(strtoupper($supporter->group_name)) }}
                                    @endif
                                    </div>
                                </td>

                                <td class="text-left font-size-sm">
                                    <div class="text-center">
                                    @if($supporter->network == '')
                                    CARD
                                    @else
                                    {{ $supporter->network }}
                                    @endif
                                    </div>
                                   {{-- <div class="text-center">
                                    @if($supporter->network != 'AIRTELTIGO' || $supporter->network != 'MTN' || $supporter->network != 'VODAFONE')
                                        CARD
                                    @else
                                    {{ $supporter->network }}
                                    @endif
                                    </div> --}}
                                </td>

                                <td class="font-size-sm">
                                @php
                                $forwardSlash = "/";
                                $hypen = '-';

                                // Getting values for supporter image name and  image path
                                $supporter_id = $supporter->id;
                                 //dd($supporter_id);
                                $supporter_phone = $supporter->phone;
                                // dd($supporter_phone);
                                $supporter_number = $supporter->supporter_number;
                                // dd($supporter_number);

                                // Creating the supporter image path                          
                                $supporterViewQRCode = $site_url . $supporter_id . $forwardSlash . $supporter_number . $forwardSlash . $supporter_phone;
                                //dd($supporterViewQRCode);
                                @endphp
                                
                                <div class="text-left visible-print">
                               
                              <img src="data:image/png; base64, {!! base64_encode(QrCode::format('png')->size(50)->generate($supporterViewQRCode)) !!} ">
                             
                              <p class="text-right">
                              <a class="" href="{{ url('/get/download/' . $supporter_id) }}" >
                              <i class="fas fa-download"></i>
                              </a>
                              </p>
                            </div>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                         {{--  {{ $allSupporters->links() }} --}}
                        {{ $allSupporters->appends(request()->query())->links() }}
                        </div>
                     </div>
                 </div>
             {{-- </div> --}}
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
            {{$url}}/assets/js/core/js.cookie.min.js
        -->
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
<script src="{{$url}}/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/dataTables.buttons.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.print.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.html5.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.flash.min.js"></script>
<script src="{{$url}}/assets/js/plugins/datatables/buttons/buttons.colVis.min.js"></script>

<!-- <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script> -->

<!-- Page JS Code -->
<script src="{{$url}}/assets/js/pages/be_pages_dashboard.min.js"></script>
<script src="{{$url}}/assets/js/pages/be_tables_datatables.min.js"></script>

<!-- Custom Javascript -->
<script>
var supportersData = {!! json_encode($allSupporters ) !!};
// console.log("All Supporters")
// console.log(supportersData.data)
var supportersArray = supportersData.data;
// console.log(supportersArray)
var supporterName = null;

var supportersNames = {!! json_encode($supporter_names ) !!};
console.log('Supporter Names')
console.log(supportersNames)
    // Handle all Forms of Names
    // function makeNameProper(personName) {
        function makeNameProper() {
        // var personName = $('#fullname').val();
        // var personName = supportersNames;

        // Looping through to get supporter name

        for (var supporterIndex = 0; supporterIndex < supportersArray.length; supporterIndex++) {
            personName =  supportersArray[supporterIndex].full_name;
            // alert(personName);
            supporterName = personName.replace(/[^\s]+/g, function(word) {
            return word.replace(/^./, function(first) {
                return first.toUpperCase();
            });
            });

            // document.getElementById('supporter_name').innerHTML = supporterName;

            // alert(supporterName)
        
        // Check if String is All Uppercase
        // https://stackoverflow.com/questions/17572873/how-can-i-check-if-a-string-is-all-uppercase-in-javascript
        
        // personName.replace(/[^\s]+/g, function(word) {
        // return word.replace(/^./, function(first) {
        //     return first.toUpperCase();
        // });
        // });
        // alert(personName)

        // if(personName.toUpperCase() === personName) {
        //     // alert("UPPERCASE NAME " + personName)
        //     personName = personName.toLowerCase(); 
        //     // alert("LOWERCASE NAME " + personName)
        //     var personName = personName.replace(/[^-'\s]+/g, function(word) {
        //      return word.replace(/^./, function(first) {
        //         return first.toUpperCase();
        //       });
        //     });
        //     console.log("PERSON NAME STARTED AS UPPERCASE: " + personName);
        //     supporterName = personName;
        //     // $('supporter_name').val()
        //     // document.getElementById('supporter_name').innerHTML = supporterName;
        //     console.log("PERSON NAME MADE PROPER INSIDE FUNC STARTED AS UPPERCASE: " + supporterName);
        // } else {
        //     var personName = personName.replace(/[^-'\s]+/g, function(word) {
        //     return word.replace(/^./, function(first) {
        //         return first.toUpperCase();
        //     });
        //     });
        //     console.log("PERSON NAME: " + personName);
        //     supporterName = personName;
        //     // document.getElementById('supporter_name').innerHTML = supporterName;
        //     console.log("PERSON NAME MADE PROPER INSIDE FUNC: " + supporterName);
        //  }
     }
   
    }

            // Check Min Max Age Values
            function checkMminMaxAgeValues() {
            var minAge = $('#min_age').val();
            console.log("Min Age: " + minAge);
            var maxAge = $('#max_age').val();
            console.log("Max Age: " + maxAge);
            // if (minAge == '' && maxAge == '') {
            //     $("#minmax-age-transaction-modal").modal('hide');
            //     console.log("The minimum age cannot be equal to the maximum age")
            // }
            if (minAge != '' && maxAge != '' && minAge >= maxAge) {
                $("#minmax-age-transaction-modal").modal('show');
                console.log("The minimum age cannot be greater than or equal to the maximum age")
            }

            }

            
            var apiUrl = {!! json_encode($api_url ) !!};
            var countriesArray = [];

            function getGeneralInfo() {
            // $.get(baseUrl + '/api/general', function(data){
            $.get(apiUrl + '/api/general', function(data){
                // termsAndConditions = data.payment_terms_and_conditions;
                //     $('#terms_and_conditions').val(termsAndConditions);
                    console.log("General Info")
                    // console.log(JSON.stringify(data.countries));

                    // Setting regions and countries data
                    countriesArray = data.countries;
                    // regionsArray = data.regions;
                    // Emptying php regions and country data
                    // https://stackoverflow.com/questions/6108509/clearing-select-using-jquery#6108538
                    // $('option', '#country').remove();

                    // Set Country Select Option placeholder text
                    // if($('#country').val() == '') {
                        // $('#country').append(`<option value="">Country of Residence</option>`);
                        // $('#country').append(`<option value="Country of Residence">Country of Residence</option>`);
                    // } 

                    // Get Selected country option
                    // https://stackoverflow.com/questions/10659097/jquery-get-selected-option-from-dropdown
                    // var selectedCountry = $('#country').find(":selected").text();
                    var selectedCountry = $('#country').val();
                    console.log("SELECTED COUNTRY: " + selectedCountry);
                    // // // $('option', '#region-state').remove();
                    // // $('#country').val(selectedCountry);
                    $('option', '#country').remove();
                    $('#country').append(`<option value="">Choose Country</option>`);
                    // // Inserting js ajax regions and countries data
                    // // https://www.w3docs.com/snippets/javascript/how-to-add-options-to-a-select-element-using-jquery.html
                    for(var countryIndex = 0; countryIndex < countriesArray.length; countryIndex++) {
                        if(selectedCountry == countriesArray[countryIndex].name){
                            $('#country').append(`<option value="${countriesArray[countryIndex].name}" selected="selected">${countriesArray[countryIndex].name}</option>`);
                        } else {
                            $('#country').append(`<option value="${countriesArray[countryIndex].name}">${countriesArray[countryIndex].name}</option>`);
                        }
                        // if(selectedCountry == countriesArray[countryIndex].name) {

                        // }
                        // $('#country').append(`<option value="${countriesArray[countryIndex].name}" selected="selected">${countriesArray[countryIndex].name}</option>`);
                        // $('#country').append(`<option value="${countriesArray[countryIndex].name} selected='selected'">${countriesArray[countryIndex].name}</option>`);
                        // $('#country').val(selectedCountry);
                        // $('#country').val(selectedCountry).change();
                        // console.log($('#country').val(selectedCountry));
                    }
                     
                     // Set Selected Country
                        // $('#country').val(selectedCountry);
                        // $('#country').val(selectedCountry).change();
                        // console.log($('#country').val(selectedCountry));
                    // for(var regionIndex = 0; regionIndex < regionsArray.length; regionIndex++) {
                    //     $('#region-state').append(`<option value="${regionsArray[regionIndex]}">${regionsArray[regionIndex]}</option>`);
                    // }
                    // console.log(countriesArray)
                    // console.log(regionsArray)
                        generalInfoVar = data;
                        // window.localStorage.setItem('generalInfo', JSON.stringify(data));
                        // handleGeneralInfoData(generalInfoVar);
                        // handleGeneralInfoDataOutside(generalInfoVar);

                        });
        }

            // $('#supporters_table').dataTable({
            //     "paging": false
            // });

            // checkMminMaxAgeValues()

            $(document).ready(function() {
                var generalInfoVar = null;
                getGeneralInfo();
                //  setTimeout(() => {
                //     alert("Supporter Name " + supporterName)
                //  }, 10000);

                // $("#minmax-age-transaction-modal").modal('hide');
                // checkMminMaxAgeValues()'
                // makeNameProper()
            });
</script>

<!-- <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#supporters_table').DataTable({
            paging: false,
            searching: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('dashboard/supporters/search') }}",
                type: 'GET',
                data: function(d) {
                    d.email = $('#search').val(),
                        d.search = $('input[type="search"]').val()
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });

    $("#searchSupporters").keyup(function() {
        table.draw();
    });
    // $('#btnFiterSubmitSearch').click(function() {
    //     $('#laravel_datatable').DataTable().draw(true);
    // });
</script> -->
@stop