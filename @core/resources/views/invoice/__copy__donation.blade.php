

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{__('Donations Invoice')}}</title>
    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        table {
            font-size: x-small;
        }

         td  {
            font-size: 14px;
            padding: 10px;
            vertical-align: bottom !important;
        }

        table tr th {
            line-height: 20px;
            font-size: 14px;
            font-weight: 700;
            padding: 15px 5px;
        }
        tfoot tr td{
            font-weight: bold;
            font-size: x-small;
        }
        .gray {
            background-color: lightgray
        }
        .table-footer tr td {
            text-align: left;
            font-size: 14px;
            padding: 10px;
        }
        .table-footer {
            margin-top: 150px;
        }
        .table-top td p {
            line-height: 18px;
            display: block;
            padding: 5px 0;
        }
    </style>
</head>

<style>
    .totalAmount{
        font-width: 700;
        font-size: 25px;
        text-align: right;
        display: block;
    }

    table thead tr th{
        border: 0;
    }
    table thead tr th{
        border: 0;
    }


    table thead tr th:first-child{
        text-align: left;
        padding: 10px 30px;
    }
    table thead tr th:last-child{
        text-align: right;
        padding: 10px 30px;
    }

    .borderStyle{
        border-bottom: 2px solid #000;
    }
    .singleItems{
        font-size: 14px;
        margin-bottom: 10px;
    }

</style>
<body>

    <table width="100%" class="table-top">
        <tr>
            <td valign="top">
                @php
                    $logo = get_attachment_image_by_id(get_static_option('site_logo'));
                @endphp
                <img src="{{$logo['img_url']}}" alt="" width="150"/>
            </td>

            <td align="right">
                <pre>
                    <h3> {{get_static_option('company_name')}} </h3>
                    <p></p>
                    <p>{{get_static_option('company_address')}}</p>
                    <p></p>
                    <p>{{get_static_option('company_email') }}</p>
                    <p></p>
                    <p> {{get_static_option('company_phone')}} </p>
                    <p></p>
                </pre>
            </td>
        </tr>
        <tr>
            <td>
                <pre>
                    <p><strong>{{__('Date : ')}}</strong> {{date('d-m-Y',strtotime($donation_details->created_at))}}</p>
                    <p></p>
                    <p><strong>{{__('From : ')}}</strong> {{get_static_option('site_global_email')}}</p>
                    <p></p>
                    <p><strong>{{__('To : ')}}</strong>{{$donation_details->name}}</p>
                </pre>

            </td>
        </tr>

    </table>

    <br/>

    <table class="table-footer" width="100%">
        <thead style="background-color: lightgray;">
            <tr>
                <th>{{__('Description')}}</th>
                <th>{{__('Amount')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
{{--                <td>--}}
                    @php
                        $all_custom_fields = json_decode($donation_details->custom_fields) ?? [];
                    @endphp
{{--                    <div class="descriptionWrapper">--}}
{{--                        <div class="cap" >--}}
{{--                                <p class="singleItems"><strong>{{__('Title : ')}}</strong> {{$donation_details->cause->title}}</p><br>--}}
{{--                                <p class="singleItems"><strong>{{__('Donor Name : ')}}</strong>{{$donation_details->name}}</p><br>--}}
{{--                                <p class="singleItems"><strong>{{__('Donor Email : ')}}</strong>{{$donation_details->email}}</p><br>--}}
{{--                                @if(!empty($all_custom_fields))--}}
{{--                                    @foreach($all_custom_fields ?? [] as $key=> $field)--}}
{{--                                        <p class="singleItems"><strong>{{ ucfirst($key) .' : ' }}</strong>{{$field}}</p><br>--}}
{{--                                    @endforeach--}}
{{--                                @endif--}}
{{--                                <p class="singleItems"><strong>{{__('Donor Country : ')}}</strong>{{optional($donation_details->user)->country}}</p><br>--}}
{{--                                <p class="singleItems"><strong>{{__('Payment Gateway : ')}}</strong>{{str_replace('_',' ',$donation_details->payment_gateway)}}</p><br>--}}
{{--                                <p class="singleItems"><strong>{{__('Payment Status : ')}}</strong>{{str_replace('_',' ',$donation_details->status)}}</p><br>--}}
{{--                                <p class="singleItems"><strong>{{__('Transaction ID : ')}}</strong>{{str_replace('_',' ',$donation_details->transaction_id)}}</p><br>--}}

{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="d" style="float: left">
                        <p class="singleItems"><strong>{{__('Title : ')}}</strong> {{$donation_details->cause->title}}</p><br>
                        <p class="singleItems"><strong>{{__('Donor Name : ')}}</strong>{{$donation_details->name}}</p><br>
                        <p class="singleItems"><strong>{{__('Donor Email : ')}}</strong>{{$donation_details->email}}</p><br>
                        @if(!empty($all_custom_fields))
                            @foreach($all_custom_fields ?? [] as $key=> $field)
                                <p class="singleItems"><strong>{{ ucfirst($key) .' : ' }}</strong>{{$field}}</p><br>
                            @endforeach
                        @endif
                        <p class="singleItems"><strong>{{__('Donor Country : ')}}</strong>{{optional($donation_details->user)->country}}</p><br>
                        <p class="singleItems"><strong>{{__('Payment Gateway : ')}}</strong>{{str_replace('_',' ',$donation_details->payment_gateway)}}</p><br>
                        <p class="singleItems"><strong>{{__('Payment Status : ')}}</strong>{{str_replace('_',' ',$donation_details->status)}}</p><br>
                        <p class="singleItems"><strong>{{__('Transaction ID : ')}}</strong>{{str_replace('_',' ',$donation_details->transaction_id)}}</p><br>
                    </div>



                    <div class="d" style="float: right">
                            <div class="borderStyle">
                                {{--                    <br><br><br><br><br><br>--}}

                                <p><strong>{{__('Donated Amount : ')}}</strong>10</p><br>
                                <p><strong>{{__('Admin Tip : ')}}</strong>20</p><br>
                                <p style="margin-bottom: 10px; display: block"><strong>{{__('Subtotal : ')}}</strong>{{amount_with_currency_symbol($donation_details->amount)}}</p><br>
                                <p class="totalAmount"> {{amount_with_currency_symbol($donation_details->amount)}}</p>
                            </div>
                    </div>



{{--                </td>--}}
{{--                <td class="borderStyle">--}}
{{--                    <br><br><br><br><br><br>--}}

{{--                    <p><strong>{{__('Donated Amount : ')}}</strong>10</p><br>--}}
{{--                    <p><strong>{{__('Admin Tip : ')}}</strong>20</p><br>--}}
{{--                    <p style="margin-bottom: 10px; display: block"><strong>{{__('Subtotal : ')}}</strong>{{amount_with_currency_symbol($donation_details->amount)}}</p><br>--}}
{{--                    <p class="totalAmount"> {{amount_with_currency_symbol($donation_details->amount)}}</p>--}}
{{--                </td>--}}
            </tr>
{{--        <tr>--}}
{{--            <td>3</td>--}}
{{--            <th scope="row">{{__('Donor Name : ')}}</th>--}}
{{--            <td>{{$donation_details->name}}</td>--}}
{{--        </tr>--}}

{{--        <tr>--}}
{{--            <td>4</td>--}}
{{--            <th scope="row">{{__('Donor Email : ')}}</th>--}}
{{--            <td>{{$donation_details->email}}</td>--}}
{{--        </tr>--}}

{{--        @php--}}
{{--            $all_custom_fields = json_decode($donation_details->custom_fields) ?? [];--}}
{{--        @endphp--}}
{{--        <tr>--}}
{{--            <td>5</td>--}}
{{--            <th scope="row">{{__('Other Infos : ')}}</th>--}}
{{--            <td>--}}
{{--                @if(!empty($all_custom_fields))--}}
{{--                    @foreach($all_custom_fields ?? [] as $key=> $field)--}}
{{--                        <li style="list-style-type: none"><strong class="text-dark ">{{ ucfirst($key) .' : ' }}</strong>{{$field}}</li><br>--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            </td>--}}
{{--        </tr>--}}


{{--        <tr>--}}
{{--            <td>6</td>--}}
{{--            <th scope="row">{{__('Donor Country : ')}}</th>--}}
{{--            <td>{{optional($donation_details->user)->country}}</td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td>7</td>--}}
{{--            <th scope="row">{{__('Payment Gateway : ')}}</th>--}}
{{--            <td>{{str_replace('_',' ',$donation_details->payment_gateway)}}</td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td>8</td>--}}
{{--            <th scope="row">{{__('Payment Status : ')}}</th>--}}
{{--            <td>{{str_replace('_',' ',$donation_details->status)}}</td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td>9</td>--}}
{{--            <th scope="row">{{__('Transaction ID : ')}}</th>--}}
{{--            <td>{{str_replace('_',' ',$donation_details->transaction_id)}}</td>--}}
{{--        </tr>--}}
        </tbody>

    </table>

