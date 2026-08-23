@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Donation Post')}}
@endsection
@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/select2.min.css')}}">
@endsection
@section('content')
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-pen text-t-400 text-sm"></i></div>
            <span class="text-sm font-bold text-d-100">Edit Campaign</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route('admin.donations.all')}}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-d-800 border border-[#e8edf5] hover:bg-[#f2f6fc] text-d-200 text-xs font-semibold transition"><i class="fas fa-list text-[10px]"></i>All Campaigns</a>
            <a href="{{route('admin.donations.new')}}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-t-500 to-t-600 hover:from-t-600 hover:to-t-700 text-white text-xs font-semibold transition shadow-lg shadow-t-500/20"><i class="fas fa-plus text-[10px]"></i>Add New</a>
        </div>
    </div>
    <div class="p-6">
        @include('backend/partials/message')
        @include('backend/partials/error')

        @php
            $frReport = \App\FraudReport::where('campaign_id',$donation->id)->orderByDesc('id')->first();
            $frScore = (int) ($donation->fraud_score ?? 0);
            $frColor = $frScore > 50 ? '#ef4444' : ($frScore > 20 ? '#f59e0b' : '#22c55e');
            $frLabel = $frScore > 50 ? 'HIGH RISK' : ($frScore > 20 ? 'MEDIUM RISK' : 'LOW RISK');
            $vStatus = $donation->verification_status ?? 'pending';
        @endphp
        <div class="mb-5 rounded-xl overflow-hidden" style="border:1px solid {{ $frColor }}33; background: linear-gradient(135deg, {{ $frColor }}0d, rgba(24,24,27,0.9));">
            <div class="px-5 py-3 border-b flex items-center justify-between flex-wrap gap-2" style="border-color:{{ $frColor }}22;">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $frColor }}1f;"><i class="fas fa-shield-alt text-sm" style="color:{{ $frColor }};"></i></div>
                    <span class="text-sm font-bold text-d-100">Fraud Analysis</span>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:{{ $frColor }}22;color:{{ $frColor }};">{{$frLabel}} — {{$frScore}}/100</span>
                </div>
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="px-2 py-1 rounded-md font-semibold" style="background:rgba(139,92,246,.12);color:#a78bfa;">Verification: {{ucfirst($vStatus)}}</span>
                    @if($frReport)<a href="{{route('admin.fraud.view',$frReport->id)}}" class="text-[#4285f4] hover:text-[#2563eb] font-semibold">Full Report <i class="fas fa-external-link-alt text-[9px]"></i></a>@endif
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <div class="h-2.5 rounded-full bg-[#f1f5fa] overflow-hidden"><div class="h-full rounded-full transition-all" style="width:{{$frScore}}%;background:{{ $frColor }};"></div></div>
                    <div class="mt-3 space-y-1.5 text-xs">
                        <div class="flex justify-between items-center"><span class="text-d-500">Patient</span><span class="font-semibold text-d-200">{{($donation->patient_name ?: '-')}}</span></div>
                        <div class="flex justify-between items-center"><span class="text-d-500">Hospital</span><span class="font-semibold text-d-200">{{($donation->hospital_name ?: '-')}}</span></div>
                        <div class="flex justify-between items-center"><span class="text-d-500">Goal</span><span class="font-semibold text-d-200">{{amount_with_currency_symbol($donation->amount)}}</span></div>
                        <div class="flex justify-between items-center"><span class="text-d-500">Wallet ({{substr($donation->wallet_address,0,10)}}…)</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold" style="{{ $donation->wallet_verified ? 'background:rgba(34,197,94,.12);color:#4ade80;' : 'background:rgba(239,68,68,.12);color:#f87171;' }}">
                                <i class="fas {{ $donation->wallet_verified ? 'fa-circle-check' : 'fa-circle-xmark' }} text-[9px]"></i>{{ $donation->wallet_verified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                    </div>
                    <form action="{{route('admin.causes.verify.wallet',$donation->id)}}" method="post" class="mt-3">
                        @csrf
                        @if($donation->wallet_verified)
                            <input type="hidden" name="unverify" value="1">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold transition" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#f87171;">
                                <i class="fas fa-rotate-left text-[10px]"></i>Revoke Wallet Verification
                            </button>
                        @else
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                                <i class="fas fa-shield-halved text-[10px]"></i>Verify Receiving Wallet
                            </button>
                        @endif
                    </form>
                </div>
                <div class="md:col-span-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-d-500 mb-2">Detected Signals — Explainable Risk Panel</div>
                    @if($frReport && !empty($frReport->check_results))
                        <div class="space-y-1.5 max-h-[290px] overflow-y-auto pr-1">
                            @php
                                $ev = $frReport->evidence ?? [];
                                $checkLabels = [
                                    'patient_verified' => 'Patient Identity',
                                    'hospital_verified' => 'Hospital Registry',
                                    'documents_verified' => 'Medical Documents',
                                    'no_duplicate_exact' => 'Exact Duplicate',
                                    'amount_normal' => 'Amount Sanity',
                                    'wallet_verified' => 'Wallet Verification',
                                    'text_quality' => 'Story Text Quality',
                                    'no_fuzzy_duplicate' => 'Fuzzy Duplicate',
                                    'goal_reasonable' => 'Goal Benchmark',
                                    'wallet_reputation' => 'Wallet Reputation',
                                    'account_trust' => 'Account Behaviour',
                                ];
                            @endphp
                            @foreach($frReport->check_results as $k => $v)
                                @if(is_bool($v) || is_numeric($v))
                                    @php
                                        $flagOn = boolval($v);
                                        $detail = $ev[$k]['detail'] ?? null;
                                        $pts = $ev[$k]['points'] ?? null;
                                    @endphp
                                    <div class="rounded-lg px-3 py-2" style="{{ $flagOn ? 'background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.18);' : 'background:rgba(34,197,94,.05);border:1px solid rgba(34,197,94,.14);' }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold {{ $flagOn ? 'text-red-400' : 'text-green-400' }}">
                                                <i class="fas {{ $flagOn ? 'fa-triangle-exclamation' : 'fa-circle-check' }} text-[10px]"></i>{{ $checkLabels[$k] ?? ucwords(str_replace('_',' ',$k)) }}
                                            </span>
                                            @if(!$flagOn && $pts)<span class="text-[10px] font-extrabold text-red-400">+{{ $pts }} pts</span>@endif
                                        </div>
                                        @if($detail)<div class="mt-0.5 text-[10.5px] leading-relaxed text-d-400">{{ $detail }}</div>@endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold" style="background:rgba(148,163,184,.1);color:#cbd5e1;"><i class="fas fa-info-circle text-[9px]"></i>No report yet — rescore from Fraud Dashboard</span>
                        </div>
                    @endif
                    @if($frReport)
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-[11px]">
                        <span class="text-d-500">Recommendation: <b style="color:{{ $frReport->recommendation === 'AUTO_APPROVE' ? '#4ade80' : '#fbbf24' }}">{{ $frReport->recommendation }}</b></span>
                        <span class="text-d-500">Status: <b class="text-d-200">{{ ucfirst($frReport->status) }}</b></span>
                    </div>
                    @endif

                    @php
                        $docIntegrity = \App\Services\DocumentIntegrityService::verify($donation);
                        $docMeta = [
                            'no_doc' => ['icon' => 'fa-file-circle-question', 'label' => 'No medical document attached', 'color' => '#94a3b8'],
                            'unsealed' => ['icon' => 'fa-file-shield', 'label' => 'Document present but not yet sealed', 'color' => '#fbbf24'],
                            'missing_file' => ['icon' => 'fa-file-circle-xmark', 'label' => 'SEALED FILE MISSING from storage', 'color' => '#f87171'],
                            'match' => ['icon' => 'fa-file-circle-check', 'label' => 'Sealed — file matches approval-time hash', 'color' => '#4ade80'],
                            'mismatch' => ['icon' => 'fa-triangle-exclamation', 'label' => 'TAMPERED — content differs from seal', 'color' => '#f87171'],
                        ];
                        $dm = $docMeta[$docIntegrity['status']];
                    @endphp
                    <div class="mt-3 rounded-xl px-3 py-2.5" style="background:rgba(15,23,42,.55);border:1px solid rgba(148,163,184,.14);">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold" style="color:{{ $dm['color'] }};">
                                <i class="fas {{ $dm['icon'] }} text-[10px]"></i>Document Integrity
                            </span>
                            <form action="{{ route('admin.campaign.verify_document', $donation->id) }}" method="post">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#2563eb,#3b82f6);">
                                    <i class="fas fa-fingerprint text-[9px]"></i>Verify SHA-256
                                </button>
                            </form>
                        </div>
                        <div class="mt-1 text-[10.5px] text-d-400">{{ __($dm['label']) }}</div>
                        @if($docIntegrity['stored'])
                            <div class="mt-1 font-mono text-[9.5px] leading-relaxed" style="color:#7dd3fc;word-break:break-all;">
                                seal: {{ substr($docIntegrity['stored'],0,32) }}…
                                @if($docIntegrity['computed'] && $docIntegrity['computed'] !== $docIntegrity['stored'])
                                    <br><span style="color:#f87171;">disk: {{ substr($docIntegrity['computed'],0,32) }}…</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @php
                        $escrowHeld = \App\Escrow::where('campaign_id', $donation->id)->where('status', 'held')->sum('amount');
                        $disputedRows = \App\Escrow::where('campaign_id', $donation->id)->where('status', 'held')->where('disputed', 1)->count();
                    @endphp
                    <div class="mt-3 rounded-xl px-3 py-2.5" style="background:rgba(127,29,29,.14);border:1px solid rgba(239,68,68,.22);">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-bold text-red-300"><i class="fas fa-user-shield text-[10px] mr-1"></i>Safety Actions</span>
                            <span class="text-[10px] text-d-400">{{ number_format((float) $escrowHeld, 0) }} {{ __('held') }}@if($disputedRows) · <b class="text-red-400">{{ $disputedRows }} {{ __('frozen') }}</b>@endif</span>
                        </div>
                        <div class="mt-2 grid grid-cols-3 gap-1.5">
                            <form action="{{ route('admin.campaign.freeze_escrow', $donation->id) }}" method="post" onsubmit="return confirm('Freeze all held escrow rows of this campaign?')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg text-[10px] font-bold transition hover:opacity-90" style="background:rgba(251,191,36,.14);border:1px solid rgba(251,191,36,.3);color:#fbbf24;" title="Block milestone releases until reviewed">
                                    <i class="fas fa-snowflake text-[9px]"></i>{{ __('Freeze') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.campaign.refund_escrow', $donation->id) }}" method="post" onsubmit="return confirm('Refund ALL held escrow to donors and take the campaign offline? This cannot be undone.')">
                                @csrf
                                <input type="hidden" name="reason" value="Admin-initiated refund from risk panel">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg text-[10px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#dc2626,#ef4444);" title="Return every held donation to its donor via on-chain refund txs">
                                    <i class="fas fa-rotate-left text-[9px]"></i>{{ __('Refund') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.campaign.blacklist_wallet', $donation->id) }}" method="post" onsubmit="return confirm('Blacklist this beneficiary wallet and mark campaign BLOCKED?')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg text-[10px] font-bold transition hover:opacity-90" style="background:rgba(148,163,184,.12);border:1px solid rgba(148,163,184,.28);color:#e2e8f0;" title="Revoke wallet verification + create BLOCKED report">
                                    <i class="fas fa-ban text-[9px]"></i>{{ __('Blacklist') }}
                                </button>
                            </form>
                        </div>
                        <div class="mt-1.5 text-[9.5px] leading-snug text-d-500">{{ __('Every action is recorded in the audit log with your identity and IP address.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{route('admin.donations.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="donation_id" value="{{$donation->id}}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="title">{{__('Title')}}</label>
                                        <input type="text" class="form-control"  id="title" name="title" value="{{$donation->title}}" >
                                    </div>

                                    <div class="form-group permalink_label">
                                        <label class="text-dark">{{__('Permalink / Slug * : ')}}
                                            <span id="slug_show" class="display-inline"></span>
                                            <span id="slug_edit" class="display-inline">
                                         <button class="btn btn-warning btn-sm slug_edit_button px-2 py-1 ml-1"> <i class="fas fa-edit"></i> </button>
                                          <input type="text" name="slug" value="{{$donation->slug}}" class="form-control blog_slug mt-2" style="display: none">
                                          <button class="btn btn-info btn-sm slug_update_button mt-2 px-2 py-1" style="display: none">{{__('Update')}}</button>
                                    </span>
                                     </label>
                                    </div>


                                    <div class="form-group">
                                        <label>{{__('Content')}}</label>
                                        <input type="hidden" name="cause_content" value="{{$donation->cause_content}}">
                                        <div class="summernote" data-content='{{$donation->cause_content}}'></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount">{{__('Amount')}}</label>
                                        <input type="number" class="form-control"  id="amount" name="amount" value="{{$donation->amount}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="excerpt">{{__('Excerpt')}}</label>
                                        <textarea class="form-control" name="excerpt" rows="5" placeholder="{{__('expert')}}">{{$donation->excerpt}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="categories_id"><strong>{{__('Category')}}</strong></label>
                                        <select name="categories_id" class="form-control">
                                            @foreach($all_category as $cat)
                                                <option value="{{$cat->id}}" @if($cat->id == $donation->categories_id) selected @endif>{{$cat->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="date">{{__('Deadline')}}</label>
                                        <input type="date" class="form-control" value="{{$donation->deadline}}" name="deadline" >
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_title">{{__('Meta Title')}}</label>
                                        <input type="text" name="meta_title" value="{{$donation->meta_title}}"  class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_tags">{{__('Meta Tags')}}</label>
                                        <input type="text" name="meta_tags"  class="form-control" data-role="tagsinput" value="{{$donation->meta_tags}}" id="meta_tags">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_description">{{__('Meta Description')}}</label>
                                        <textarea name="meta_description"  class="form-control" rows="5" id="meta_description">{{$donation->meta_description}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_title">{{__('Og Meta Title')}}</label>
                                        <input type="text" name="meta_title" value="{{$donation->meta_title}}"  class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="meta_description">{{__('Og Meta Description')}}</label>
                                        <textarea name="meta_description"  class="form-control" rows="5" id="meta_description">{{$donation->meta_description}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">{{__('Og Meta Image')}}</label>
                                        <div class="media-upload-btn-wrapper">
                                            <div class="img-wrap">
                                                {!! render_attachment_preview_for_admin($donation->og_meta_image) !!}
                                            </div>
                                            <input type="hidden" name="og_meta_image" value="{{$donation->og_meta_image}}">
                                            <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Donation Image')}}" data-modaltitle="{{__('Upload Donation Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                {{'Change Image'}}
                                            </button>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="image">{{__('Image')}}</label>
                                        <div class="media-upload-btn-wrapper">
                                           <div class="img-wrap">
                                               {!! render_attachment_preview_for_admin($donation->image) !!}
                                           </div>
                                            <input type="hidden" name="image" value="{{$donation->image}}">
                                            <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Donation Image')}}" data-modaltitle="{{__('Upload Donation Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                {{'Change Image'}}
                                            </button>
                                        </div>
                                        <small>{{__('Recommended image size 1920x1280')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{{__('Status')}}</label>
                                        <select name="status" id="status"  class="form-control">
                                            <option @if($donation->status === 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                                            <option @if($donation->status === 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                                            <option @if($donation->status === 'archive') selected @endif value="archive">{{__('Archive')}}</option>
                                            <option @if($donation->status === 'banned') selected @endif value="banned">{{__('Banned')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">{{__('Image Gallery')}}</label>
                                        <div class="media-upload-btn-wrapper">
                                            <div class="img-wrap">
                                                {!! render_gallery_image_attachment_preview($donation->image_gallery) !!}
                                            </div>
                                            <input type="hidden" name="image_gallery" value="{{$donation->image_gallery}}">
                                            <button type="button" class="btn btn-info media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                {{__('Upload Images')}}
                                            </button>
                                        </div>
                                        <small>{{__('Recommended image size 1920x1280')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">{{__('Medical Documents')}}</label>
                                        <div class="media-upload-btn-wrapper">
                                            <div class="img-wrap">
                                                {!! render_gallery_image_attachment_preview($donation->medical_document) !!}
                                            </div>
                                            <input type="hidden" name="medical_document" value="{{$donation->medical_document}}">
                                            <button type="button" class="btn btn-info media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Document')}}" data-modaltitle="{{__('Upload Document')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                {{__('Upload Images')}}
                                            </button>
                                        </div>
                                        <small>{{__('Recommended image size 1920x1280')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured"><strong>{{__('Monthly Donation')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="monthly_donation_status"  @if(!empty($donation->monthly_donation_status)) checked @endif>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured"><strong>{{__('Featured')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="featured"  @if(!empty($donation->featured)) checked @endif>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured"><strong>{{__('Emmergency')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="emmergency"  @if(!empty($donation->emmergency)) checked @endif>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured"><strong>{{__('Reward')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="reward"  @if(!empty($donation->reward)) checked @endif>
                                            <span class="slider"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="featured"><strong>{{__('Gift')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="gift_status" class="add_gift_status" @if(!empty($donation->gift_status)) checked @endif>
                                            <span class="slider"></span>
                                        </label>
                                    </div>


                                    <div class="gift_select_wrapper">
                                        <div class="form-group">
                                            <label><strong>{{__('Select Gift')}}</strong></label>
                                            <select name="gifts[]" class="form-control gifts" multiple>
                                                @foreach($all_gifts as $gift)
                                                    <option value="{{$gift->id}}"
                                                    @foreach($donation->gift ?? [] as $gift_item)
                                                        {{ $gift_item->id == $gift->id ? 'selected' : '' }}
                                                    @endforeach
                                                    >{{$gift->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <br>

                                    <div style="background: linear-gradient(135deg, #eef2ff, #e8f0fe); border: 2px solid #627EEA; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                        <h5 style="color: #4B6CEB; font-weight: 700; margin-bottom: 15px;"><i class="fas fa-link"></i> {{__('Blockchain Verification Details')}}</h5>
                                        <small style="color: #666;">{{__('These fields help verify the campaign authenticity for blockchain-secured donations.')}}</small>

                                        <div class="form-group mt-3">
                                            <label for="patient_name"><strong>{{__('Patient / Beneficiary Name')}}</strong></label>
                                            <input type="text" class="form-control" name="patient_name" value="{{$donation->patient_name ?? ''}}" placeholder="{{__('Enter the patient or beneficiary name')}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="hospital_name"><strong>{{__('Hospital / Institution Name')}}</strong></label>
                                            <input type="text" class="form-control" name="hospital_name" value="{{$donation->hospital_name ?? ''}}" placeholder="{{__('Enter hospital or institution name')}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="medical_details"><strong>{{__('Medical / Case Details')}}</strong></label>
                                            <textarea class="form-control" name="medical_details" rows="4" placeholder="{{__('Describe the medical case or situation requiring funding...')}}">{{$donation->medical_details ?? ''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="iconbox-repeater-wrapper">
                                        @php
                                             $faq_items = !empty($donation->faq) ? unserialize($donation->faq,['class' => false]) : ['title' => ['']];
                                        @endphp
                                        @forelse($faq_items['title'] ?? [] as $faq)
                                        <div class="all-field-wrap">
                                            <div class="form-group">
                                                <label for="faq">{{__('Faq Title')}}</label>
                                                <input type="text" name="faq[title][]" class="form-control" value="{{$faq}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="faq_desc">{{__('Faq Description')}}</label>
                                                <textarea name="faq[description][]" class="form-control">{{$faq_items['description'][$loop->index] ?? ''}}</textarea>
                                            </div>
                                            <div class="action-wrap">
                                                <span class="add"><i class="ti-plus"></i></span>
                                                <span class="remove"><i class="ti-trash"></i></span>
                                            </div>
                                        </div>
                                        @empty
                                            <div class="all-field-wrap">
                                                <div class="form-group">
                                                    <label for="faq">{{__('Faq Title')}}</label>
                                                    <input type="text" name="faq[title][]" class="form-control" placeholder="{{__('faq title')}}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="faq_desc">{{__('Faq Description')}}</label>
                                                    <textarea name="faq[description][]" class="form-control" placeholder="{{__('faq description')}}"></textarea>
                                                </div>
                                                <div class="action-wrap">
                                                    <span class="add"><i class="ti-plus"></i></span>
                                                    <span class="remove"><i class="ti-trash"></i></span>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button id="update" type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Cause')}}</button>
                                </div>
                            </div>
                        </form>
    </div>
</div>
    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/select2.min.js')}}"></script>
    <script>
        (function($){
            "use strict";
            $(document).ready(function () {
                <x-btn.update/>

                $('.gifts').select2();

                let gift_status = '{{$donation->gift_status}}';

                if(gift_status != 'on'){
                      $('.gifts').prop('disabled',true);
                }
                $(document).on('change','.add_gift_status',function(){
                    $('.gifts').prop('disabled',false);
                    if(this.checked){
                        $('.gift_select_wrapper').removeClass('d-none')
                    }else{
                        $('.gift_select_wrapper').addClass('d-none')
                    }
                });

                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                //Permalink Code
                var sl =  $('.blog_slug').val();
                var url = `{{url('/donation/')}}/` + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');
                var form = $('#blog_new_form');

                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.blog_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.blog_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/donation/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.blog_slug').hide();
                });


                $('.summernote').summernote({
                    height: 500,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function(contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });

                if($('.summernote').length > 0){
                    $('.summernote').each(function(index,value){
                        $(this).summernote('code', $(this).data('content'));
                    });
                }

                $(document).on('change','#language',function(e){
                    e.preventDefault();
                    var selectedLang = $(this).val();
                    $('select[name="categories_id"]').html('<option value="">{{__('Select Category')}}</option>');
                    $.ajax({
                        url: "{{route('admin.donations.category.by.lang')}}",
                        type: "POST",
                        data: {
                            _token : "{{csrf_token()}}",
                            lang : selectedLang
                        },
                        success:function (data) {
                            $.each(data,function(index,value){
                                $('select[name="categories_id"]').append('<option value="'+value.id+'">'+value.title+'</option>')
                            });
                        }
                    });
                });


            });
        })(jQuery)
    </script>
    <script src="{{asset('assets/backend/js/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
   <x-media.js/>
   <x-repeater/>
@endsection
