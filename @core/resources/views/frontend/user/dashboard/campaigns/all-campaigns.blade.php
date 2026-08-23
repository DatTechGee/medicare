@extends('frontend.user.dashboard.user-master')
@section('site-title')
    {{__('My Campaigns')}}
@endsection
@section('style')
@parent
<style>
.campaign-thumb{width:150px;min-height:180px;flex-shrink:0;background:#0D0D1A;overflow:hidden}
.campaign-thumb img{width:100%;height:100%;object-fit:cover;display:block}
@media(max-width:640px){.campaign-thumb{width:110px;min-height:150px}}
</style>
@endsection
@section('section')
 <div class="ud-section-title">
     <h2><i class="fas fa-file-medical"></i>{{__('My Campaigns')}}</h2>
     <a href="{{route('user.campaign.new')}}" class="ud-btn-primary"><i class="fas fa-plus"></i> {{__('Create New Campaign')}}</a>
 </div>

 @if(count($all_donations) > 0)
 <div class="row">
    @foreach($all_donations as $data)
        <div class="col-lg-6" style="margin-bottom:22px;">
            <div class="ud-card" style="padding:0;overflow:hidden;">
                <div style="display:flex;gap:0;">
                    <div class="campaign-thumb">
                        {!! render_image_markup_by_attachment_id($data->image,null,'thumb') !!}
                    </div>
                    <div style="padding:18px 20px;flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;">
                            <span style="font-size:10.5px;font-weight:700;color:rgba(224,224,224,.45);letter-spacing:.8px;">#{{$data->id}} &bull; {{date("d M Y", strtotime($data->created_at))}}</span>
                            @php
                                $vStat = $data->verification_status;
                                $badgeMap = [
                                    'approved' => ['c' => '#00D4AA', 'i' => 'check-circle',   't' => __('Approved')],
                                    'verified' => ['c' => '#00D4AA', 'i' => 'check-circle',   't' => __('Verified')],
                                    'rejected' => ['c' => '#ff6b6b', 'i' => 'times-circle',   't' => __('Rejected')],
                                    'pending'  => ['c' => '#FFB800', 'i' => 'hourglass-half', 't' => __('Under Review')],
                                ];
                                $badge = $badgeMap[$vStat] ?? $badgeMap['pending'];
                                $fs = (int) ($data->fraud_score ?? 0);
                                $riskC = $fs <= 20 ? '#00D4AA' : ($fs <= 50 ? '#FFB800' : '#ff5c7a');
                            @endphp
                            <span style="display:flex;gap:6px;">
                                <span style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $badge['c'] }}1f;color:{{ $badge['c'] }};border:1px solid {{ $badge['c'] }}4d;"><i class="fas fa-{{ $badge['i'] }}"></i> {{ $badge['t'] }}</span>
                                <span style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $riskC }}14;color:{{ $riskC }};border:1px solid {{ $riskC }}44;" title="{{__('Automated fraud screening score')}}"><i class="fas fa-shield-halved"></i> {{ $fs }}/100</span>
                            </span>
                        </div>

                        <h4 style="color:#fff;font-size:15.5px;font-weight:700;margin:0 0 10px;line-height:1.35;">
                            <a href="{{route('frontend.donations.single',$data->slug)}}" style="color:#fff;text-decoration:none;">{{Str::limit($data->title, 60)}}</a>
                        </h4>

                        @php $progress = $data->amount > 0 ? round(($data->raised / $data->amount) * 100) : 0; @endphp
                        <div style="height:7px;background:rgba(255,255,255,.07);border-radius:4px;overflow:hidden;margin-bottom:6px;">
                            <div style="height:100%;width:{{min($progress,100)}}%;background:linear-gradient(90deg,#627EEA,#00D4AA);border-radius:4px;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:11.5px;margin-bottom:12px;">
                            <span style="color:#00D4AA;font-weight:700;">{{amount_with_currency_symbol($data->raised)}} {{__('raised')}}</span>
                            <span style="color:rgba(224,224,224,.5);">{{__('Goal')}}: {{amount_with_currency_symbol($data->amount)}} &bull; {{$progress}}%</span>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                            <span style="font-size:11px;color:rgba(224,224,224,.55);">
                                <i class="fas fa-wallet" style="color:#00D4AA;margin-right:4px;"></i>
                                @if(!empty($data->wallet_address))
                                    <code style="color:#00D4AA;background:rgba(0,212,170,.07);padding:2px 7px;border-radius:6px;font-size:10.5px;">{{substr($data->wallet_address,0,8)}}...{{substr($data->wallet_address,-6)}}</code>
                                @else
                                    {{__('No wallet set')}}
                                @endif
                            </span>
                            <div style="display:flex;gap:6px;">
                                <a href="{{route('user.campaign.edit',$data->id)}}" class="ud-btn-ghost" style="padding:7px 13px;font-size:11.5px;"><i class="fas fa-pen"></i> {{__('Edit')}}</a>
                                <button type="button" class="ud-btn-ghost swal_delete_button" data-form-id="del-form-{{$data->id}}" style="padding:7px 13px;font-size:11.5px;color:#ff6b6b!important;border-color:rgba(255,107,107,.3);"><i class="fas fa-trash"></i></button>
                                <form method='post' action='{{route('user.campaign.delete',$data->id)}}' id="del-form-{{$data->id}}" class="d-none">@csrf</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
 </div>
 @else
    <div class="ud-card" style="text-align:center;padding:56px 24px;">
        <div class="ud-stat-icon" style="margin:0 auto 16px;width:64px;height:64px;font-size:26px;"><i class="fas fa-folder-open"></i></div>
        <h4 style="color:#fff;font-size:17px;font-weight:700;margin:0 0 8px;">{{__('No Campaigns Yet')}}</h4>
        <p style="color:rgba(224,224,224,.55);font-size:13.5px;margin:0 0 20px;">{{__('Create your first medical campaign request — it will be reviewed and verified by our admin team before going live.')}}</p>
        <a href="{{route('user.campaign.new')}}" class="ud-btn-primary"><i class="fas fa-plus"></i> {{__('Create New Campaign')}}</a>
    </div>
 @endif
@endsection

@section('scripts')
    @parent
    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('click', '.swal_delete_button', function (e) {
                    e.preventDefault();
                    var formId = $(this).data('form-id');
                    swal({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("You will not be able to recover this campaign!")}}',
                        icon: 'warning',
                        buttons: {cancel: '{{__("Cancel")}}', confirm: '{{__("Yes, delete it!")}}'},
                        dangerMode: true,
                    }).then(function (willDelete) {
                        if (willDelete) {
                            $('#' + formId).submit();
                        }
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
