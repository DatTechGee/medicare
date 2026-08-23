@extends('backend.admin-master')
@section('title', __('All Campaigns'))
@section('style')
    <x-media.css/>
    <style>
        .acx-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .acx-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,.4); border-color: rgba(98,126,234,.35) !important; }
        .acx-thumb { height: 150px; object-fit: cover; width: 100%; }
        .acx-chip { font-size: 10px; font-weight: 700; letter-spacing: .04em; padding: 4px 10px; border-radius: 999px; text-transform: uppercase; }
        .acx-chip.active { background: rgba(98,126,234,.2) !important; color: #aab6f8 !important; border-color: rgba(98,126,234,.5) !important; }
        .acx-act { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 9px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); color: #9aa3c7; font-size: 12px; transition: all .15s; text-decoration: none !important; }
        .acx-act:hover { background: rgba(98,126,234,.18); color: #fff; border-color: rgba(98,126,234,.4); }
        .acx-act.danger:hover { background: rgba(239,68,68,.18); border-color: rgba(239,68,68,.45); color: #fca5a5; }
        .acx-act.success:hover { background: rgba(16,185,129,.18); border-color: rgba(16,185,129,.45); color: #6ee7b7; }
        .acx-pagination .page-link { background: transparent; border: 1px solid rgba(255,255,255,.08); color: #9aa3c7; border-radius: 10px !important; margin: 0 3px; font-size: 12.5px; font-weight: 600; }
        .acx-pagination .page-item.active .page-link { background: linear-gradient(135deg,#627EEA,#4B6CEB); border-color: transparent; color: #fff; }
        .acx-pagination .page-item.disabled .page-link { opacity: .35; }
        .acx-search { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; padding: 9px 14px 9px 38px; color: #e5e8f5; font-size: 13px; outline: none; min-width: 230px; }
        .acx-search:focus { border-color: rgba(98,126,234,.5); box-shadow: 0 0 0 3px rgba(98,126,234,.15); }
        .acx-search::placeholder { color: rgba(229,232,245,.35); }
        .acx-empty { border: 1.5px dashed rgba(255,255,255,.1); border-radius: 18px; }
    </style>
@endsection
@section('content')
@php
    $statTotal = \App\Cause::count();
    $statRaised = \App\Cause::sum('raised');
    $statGoal = \App\Cause::sum('amount');
    $statVerified = \App\Cause::where('verification_status','approved')->count();
    $statPending = \App\Cause::whereIn('verification_status',['pending','under_review'])->count();
    $statRejected = \App\Cause::where('verification_status','rejected')->count();
    $statFlagged = \App\Cause::where('fraud_score','>=',60)->count();
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Campaigns</span>
            <div class="w-7 h-7 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-bullhorn text-t-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-d-100">{{ number_format($statTotal) }}</div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Total Raised</span>
            <div class="w-7 h-7 rounded-lg bg-green-500/10 flex items-center justify-center"><i class="fas fa-hand-holding-usd text-green-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-d-100">{{ amount_with_currency_symbol($statRaised) }}</div>
        <div class="text-[10px] text-d-400 mt-0.5">&asymp; {{ number_format($statRaised/3450, 2) }} ETH of {{ amount_with_currency_symbol($statGoal) }}</div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Verified</span>
            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 flex items-center justify-center"><i class="fas fa-check-circle text-emerald-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-emerald-400">{{ number_format($statVerified) }}</div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Pending</span>
            <div class="w-7 h-7 rounded-lg bg-yellow-500/10 flex items-center justify-center"><i class="fas fa-clock text-yellow-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-yellow-400">{{ number_format($statPending) }}</div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Rejected</span>
            <div class="w-7 h-7 rounded-lg bg-red-500/10 flex items-center justify-center"><i class="fas fa-times-circle text-red-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-red-400">{{ number_format($statRejected) }}</div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-d-300 uppercase tracking-wider">Fraud Flagged</span>
            <div class="w-7 h-7 rounded-lg bg-orange-500/10 flex items-center justify-center"><i class="fas fa-shield-alt text-orange-400 text-xs"></i></div>
        </div>
        <div class="text-xl font-extrabold text-orange-400">{{ number_format($statFlagged) }}</div>
    </div>
</div>

<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-file-medical text-t-400 text-sm"></i></div>
            <span class="text-sm font-bold text-d-100">All Campaigns</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#f1f5fa] text-d-400 font-semibold">{{$all_donations->total()}} total</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @can('donation-delete')
                <x-bulk-action/>
            @endcan
            @can('donation-create')
                <a href="{{route('admin.donations.new')}}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-t-500 to-t-600 hover:from-t-600 hover:to-t-700 text-white text-xs font-semibold transition shadow-lg shadow-t-500/20"><i class="fas fa-plus text-[10px]"></i>Add New Campaign</a>
            @endcan
        </div>
    </div>

    {{-- toolbar --}}
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="relative">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-d-500 text-xs"></i>
            <input type="text" id="acxSearch" class="acx-search" placeholder="{{__('Search campaigns...')}}">
        </div>
        <div class="flex items-center gap-2 flex-wrap" id="acxFilters">
            <button data-f="all" class="acx-chip active bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer">All</button>
            <button data-f="publish" class="acx-chip bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer"><i class="fas fa-globe mr-1"></i>Published</button>
            <button data-f="pending" class="acx-chip bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer"><i class="fas fa-clock mr-1"></i>Pending</button>
            <button data-f="approved" class="acx-chip bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer"><i class="fas fa-check-circle mr-1"></i>Verified</button>
            <button data-f="rejected" class="acx-chip bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer"><i class="fas fa-times-circle mr-1"></i>Rejected</button>
            <button data-f="flagged" class="acx-chip bg-[#f1f5fa] text-d-300 border border-[#e8edf5] cursor-pointer"><i class="fas fa-shield-alt mr-1"></i>Flagged</button>
        </div>
    </div>

    {{-- card grid --}}
    <div class="p-6">
        <x-msg.error/>
        <x-msg.success/>
        <div id="acxGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($all_donations as $row)
                @php
                    $vstatus = $row->verification_status ?? 'pending';
                    $progress = $row->amount > 0 ? round(($row->raised / $row->amount) * 100) : 0;
                    $fraudScore = $row->fraud_score ?? 0;
                    $fraudColor = $fraudScore >= 60 ? '#ef4444' : ($fraudScore >= 21 ? '#f59e0b' : '#22c55e');
                    $img = get_attachment_image_by_id($row->image);
                    $thumb = $img['img_url'] ?? '';
                    $categoryName = trim(donation_category_by_id($row->categories_id));
                @endphp
                <div class="acx-card bg-d-800 border border-[#e8edf5] rounded-2xl overflow-hidden flex flex-col"
                     data-title="{{strtolower($row->title)}}"
                     data-status="{{$row->status}}"
                     data-vstatus="{{$vstatus}}"
                     data-flagged="{{($fraudScore >= 60) ? 'yes' : 'no'}}">
                    <div class="relative">
                        @if($thumb)
                            <img src="{{$thumb}}" alt="{{$row->title}}" class="acx-thumb">
                        @else
                            <div class="acx-thumb flex items-center justify-center" style="background:linear-gradient(135deg,#1a2036,#131728);"><i class="fas fa-image text-3xl" style="color:rgba(255,255,255,.12);"></i></div>
                        @endif
                        <div class="absolute top-3 left-3 flex items-center gap-1.5">
                            @if($vstatus === 'approved')
                                <span class="acx-chip" style="background:rgba(16,185,129,.85);color:#fff;"><i class="fas fa-check-circle mr-1"></i>Verified</span>
                            @elseif($vstatus === 'rejected')
                                <span class="acx-chip" style="background:rgba(239,68,68,.85);color:#fff;"><i class="fas fa-times-circle mr-1"></i>Rejected</span>
                            @elseif($vstatus === 'under_review')
                                <span class="acx-chip" style="background:rgba(59,130,246,.85);color:#fff;"><i class="fas fa-search mr-1"></i>Reviewing</span>
                            @else
                                <span class="acx-chip" style="background:rgba(245,158,11,.85);color:#fff;"><i class="fas fa-clock mr-1"></i>Pending</span>
                            @endif
                            @if($fraudScore >= 60)
                                <span class="acx-chip" style="background:rgba(234,88,12,.9);color:#fff;"><i class="fas fa-shield-alt mr-1"></i>Fraud {{$fraudScore}}</span>
                            @endif
                        </div>
                        @if($row->featured == 'on')
                            <span class="absolute top-3 right-3 w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(245,158,11,.9);color:#fff;" title="Featured"><i class="fas fa-star text-[10px]"></i></span>
                        @endif
                        @can('donation-delete')
                            <label class="absolute bottom-3 right-3 w-7 h-7 rounded-lg flex items-center justify-center cursor-pointer m-0" style="background:rgba(10,12,24,.75);backdrop-filter:blur(4px);" title="Select for bulk action">
                                <input type="checkbox" class="bulk-checkbox m-0" name="bulk_delete[]" value="{{$row->id}}" style="accent-color:#627EEA;">
                            </label>
                        @endcan
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-t-500/10 text-t-400">{{trim($categoryName) ?: __('Uncategorized')}}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md {{($row->status==='publish')?'bg-green-500/10 text-green-400':'bg-[#f1f5fa] text-d-400'}}">{{ucfirst($row->status)}}</span>
                            <span class="text-[10px] text-d-500 ml-auto">#{{$row->id}}</span>
                        </div>

                        <h5 class="text-sm font-bold text-d-100 leading-snug mb-1" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{Str::limit($row->title, 70)}}</h5>
                        <p class="text-[11px] text-d-500 mb-3"><i class="far fa-calendar mr-1"></i>{{optional($row->created_at)->format('d M Y')}} @if(!empty($row->wallet_address))<span class="ml-2"><i class="fab fa-ethereum mr-1" style="color:#627EEA;"></i><code class="text-[10px]" style="color:#8A9FF5;">{{substr(strtolower($row->wallet_address),0,10)}}...</code></span>@endif</p>

                        <div class="mt-auto">
                            <div class="flex items-center justify-between text-[11px] mb-1.5">
                                <span class="font-bold text-green-400">{{amount_with_currency_symbol($row->raised ?? 0)}}</span>
                                <span class="text-d-500">{{__('of')}} {{amount_with_currency_symbol($row->amount)}} &bull; {{number_format(($row->amount ?? 0)/3450, 3)}} ETH</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-[#f1f5fa] overflow-hidden mb-3">
                                <div class="h-full rounded-full" style="width:{{min($progress,100)}}%;background:linear-gradient(90deg,#627EEA,#00D4AA);"></div>
                            </div>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] text-d-500 uppercase tracking-wider font-bold">Fraud score</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 rounded-full bg-[#f1f5fa] overflow-hidden"><div class="h-full rounded-full" style="width:{{min($fraudScore,100)}}%;background:{{$fraudColor}};"></div></div>
                                    <span class="text-[10px] font-extrabold" style="color:{{$fraudColor}};">{{$fraudScore}}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 pt-3 border-t border-[#e8edf5] flex-wrap">
                                <a href="{{route('frontend.donations.single',$row->slug)}}" target="_blank" class="acx-act" title="{{__('View')}}"><i class="fas fa-eye"></i></a>
                                @can('donation-edit')
                                    <a href="{{route('admin.donations.edit',$row->id)}}" class="acx-act" title="{{__('Edit')}}"><i class="fas fa-edit"></i></a>
                                    <a href="{{route('admin.milestones.index',$row->id)}}" class="acx-act" title="{{__('Milestones')}}"><i class="fas fa-road"></i></a>
                                    <a href="{{route('admin.donations.donors',$row->id)}}" class="acx-act" title="{{__('Donors')}}"><i class="fas fa-users"></i></a>
                                    <form action="{{route('admin.donations.clone')}}" method="post" class="inline m-0">@csrf<input type="hidden" name="id" value="{{$row->id}}"><button type="submit" class="acx-act" title="{{__('Clone')}}" style="cursor:pointer;"><i class="fas fa-copy"></i></button></form>
                                    @if($row->created_by === 'user' && $row->status === 'pending')
                                        <form action="{{route('admin.donation.approve')}}" method="post" class="inline m-0">@csrf<input type="hidden" name="id" value="{{$row->id}}"><button type="submit" class="acx-act success" title="{{__('Approve This Campaign')}}" style="cursor:pointer;"><i class="fas fa-thumbs-up"></i></button></form>
                                    @endif
                                @endcan
                                @can('donation-delete')
                                    <span class="ml-auto inline-flex items-center gap-0">
                                        <a tabindex="0" class="acx-act danger swal_delete_button" title="{{__('Delete')}}"><i class="fas fa-trash"></i></a>
                                        <form method="post" action="{{route('admin.donations.delete',$row->id)}}" class="d-none">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn d-none"></button>
                                        </form>
                                    </span>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full acx-empty text-center py-16">
                    <i class="fas fa-box-open text-4xl mb-4" style="color:rgba(255,255,255,.12);"></i>
                    <h5 class="text-sm font-bold text-d-200 mb-1">{{__('No campaigns found')}}</h5>
                    <p class="text-xs text-d-500 mb-4">{{__('Create your first campaign to get started.')}}</p>
                    @can('donation-create')
                        <a href="{{route('admin.donations.new')}}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-t-500 to-t-600 text-white text-xs font-semibold"><i class="fas fa-plus text-[10px]"></i>Add New Campaign</a>
                    @endcan
                </div>
            @endforelse
        </div>

        @if($all_donations->hasPages())
            <div class="mt-6 flex justify-center">
                <nav class="acx-pagination">
                    {{$all_donations->appends(request()->query())->links()}}
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection

@section('script')
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                /* live search + status filter for card grid */
                function applyFilters() {
                    var q = $('#acxSearch').val().toLowerCase().trim();
                    var f = $('#acxFilters .acx-chip.active').data('f');
                    $('#acxGrid > .acx-card').each(function () {
                        var $card = $(this);
                        var title = $card.data('title') || '';
                        var status = $card.data('status');
                        var vstatus = $card.data('vstatus');
                        var flagged = $card.data('flagged');
                        var matchQ = !q || title.indexOf(q) !== -1;
                        var matchF = (f === 'all')
                            || (f === 'flagged' && flagged === 'yes')
                            || (f === 'pending' && (vstatus === 'pending' || vstatus === 'under_review'))
                            || (f !== 'flagged' && f !== 'pending' && status === f);
                        $card.toggle(matchQ && matchF);
                    });
                }
                $('#acxSearch').on('keyup', applyFilters);
                $('#acxFilters .acx-chip').on('click', function () {
                    $('#acxFilters .acx-chip').removeClass('active');
                    $(this).addClass('active');
                    applyFilters();
                });
            })
        })(jQuery)
    </script>
@endsection
