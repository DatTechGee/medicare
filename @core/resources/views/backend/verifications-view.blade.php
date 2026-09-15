@extends("backend.admin-master")
@section("title", __("Verification Review"))
@section("content")

@php
    $upPath        = 'assets/uploads/media-uploader/';
    $statusMap     = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
    $statusColor   = $statusMap[$verification->status] ?? 'secondary';
    $campStatusCol = $statusMap[optional($campaign)->verification_status ?? 'pending'] ?? 'secondary';
    $fraudScore    = optional($campaign)->fraud_score ?? 0;
    $fraudColor    = $fraudScore > 50 ? 'text-red-400' : ($fraudScore > 25 ? 'text-amber-400' : 'text-green-400');
    $daysLeft      = optional($campaign)->deadline ? max(0, \Carbon\Carbon::now()->diffInDays($campaign->deadline, false)) : 0;
    $progress      = $campaign && $campaign->amount > 0 ? round(($campaign->raised / $campaign->amount) * 100) : 0;
    $allTypes      = ['patient' => 'fa-user-md', 'hospital' => 'fa-hospital', 'document' => 'fa-file-medical-alt', 'amount' => 'fa-dollar-sign'];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ============================================================ --}}
    {{-- LEFT COLUMN (2/3) — Campaign + Patient + Documents + Verifications --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Header --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-t-500/10 flex items-center justify-center">
                        <i class="fas fa-shield-alt text-t-400"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-d-100">Verification Review</h3>
                        <p class="text-[11px] text-d-500">ID #{{$verification->id}} &middot; Type: <span class="font-semibold">{{ucfirst($verification->type)}}</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-lg bg-{{$statusColor}}/10 text-{{$statusColor}} text-xs font-bold">{{ucfirst($verification->status)}}</span>
                    <a href="{{route("admin.verifications.all")}}" class="px-3 py-1.5 rounded-lg bg-d-800 border border-[#e8edf5] text-[11px] font-semibold text-d-300 hover:bg-[#f2f6fc] transition"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                </div>
            </div>
        </div>

        {{-- Campaign Overview --}}
        @if($campaign)
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-file-medical text-[#4285f4] text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Campaign Overview</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Title</span>
                    <p class="text-sm text-d-100 font-semibold mt-1">{{$campaign->title ?? '-'}}</p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Category</span>
                    <p class="text-sm text-d-100 font-medium mt-1">{{$campaign->category->title ?? '-'}}</p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Goal</span>
                    <p class="text-sm text-d-100 font-semibold mt-1">{{number_format($campaign->amount, 2)}} ETH</p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Raised</span>
                    <p class="text-sm text-d-100 font-semibold mt-1">{{number_format($campaign->raised ?? 0, 2)}} ETH <span class="text-d-500 font-normal">({{$progress}}%)</span></p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Deadline</span>
                    <p class="text-sm text-d-100 font-medium mt-1">{{$campaign->deadline ? $campaign->deadline->format('M j, Y') : '-'}} <span class="text-d-500">({{$daysLeft}} days left)</span></p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Submitted By</span>
                    <p class="text-sm text-d-100 font-medium mt-1">{{optional($campaign->user)->name ?? '-'}} <span class="text-d-500 text-[11px]">({{optional($campaign->user)->email ?? '-'}})</span></p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Excerpt</span>
                    <p class="text-xs text-d-300 mt-1 leading-relaxed">{{Str::limit($campaign->excerpt ?? $campaign->cause_content ?? '-', 300)}}</p>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="mt-4">
                <div class="w-full h-2 rounded-full bg-d-800 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-t-500 to-t-400" style="width:{{min($progress, 100)}}%"></div>
                </div>
            </div>
        </div>

        {{-- Patient / Recipient --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-blue-400/10 flex items-center justify-center"><i class="fas fa-user-injured text-blue-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Patient / Recipient</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Patient Name</span>
                    <p class="text-sm text-d-100 font-semibold mt-1">{{$campaign->patient_name ?: '-'}}</p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Hospital</span>
                    <p class="text-sm text-d-100 font-medium mt-1">{{$campaign->hospital_name ?: '-'}}</p>
                </div>
                <div>
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Medical Details</span>
                    <p class="text-xs text-d-300 mt-1 leading-relaxed">{{Str::limit($campaign->medical_details ?? '-', 200)}}</p>
                </div>
            </div>
        </div>

        {{-- Medical Documents --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-amber-400/10 flex items-center justify-center"><i class="fas fa-file-medical text-amber-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Medical Documents</span>
                <span class="px-2 py-0.5 rounded-md bg-d-800 text-d-400 text-[10px] font-bold">{{$medicalDocs->count()}} file(s)</span>
            </div>
            @if($medicalDocs->isEmpty())
                <div class="text-center py-6"><i class="fas fa-folder-open text-d-600 text-2xl mb-2"></i><p class="text-xs text-d-500">No medical documents uploaded.</p></div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($medicalDocs as $doc)
                        @php
                            $url    = asset($upPath . $doc->path);
                            $exists = file_exists(public_path($upPath . $doc->path));
                            $isImg  = str_contains(strtolower($doc->type ?? ''), 'image');
                        @endphp
                        <div class="bg-d-800 border border-[#e8edf5] rounded-xl p-3 text-center">
                            @if($isImg && $exists)
                                <a href="{{$url}}" target="_blank"><img src="{{$url}}" alt="{{$doc->title ?? ''}}" class="w-full h-20 object-cover rounded-lg mb-2"></a>
                            @else
                                <a href="{{$url}}" target="_blank"><div class="w-full h-20 rounded-lg bg-d-700 flex items-center justify-center mb-2"><i class="fa fa-{{$isImg ? 'image' : 'file-pdf-o'}} text-d-400 text-xl"></i></div></a>
                            @endif
                            @if(!$exists)<span class="text-[9px] font-bold text-red-400">MISSING</span>@endif
                            <p class="text-[10px] text-d-400 truncate mt-1">{{$doc->title ?? basename($doc->path)}}</p>
                            @if($exists)<a href="{{$url}}" target="_blank" class="text-[10px] text-t-400 font-semibold hover:underline">Open</a>@endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Gallery --}}
        @if($galleryMedia->isNotEmpty())
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-purple-400/10 flex items-center justify-center"><i class="fas fa-images text-purple-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Gallery</span>
                <span class="px-2 py-0.5 rounded-md bg-d-800 text-d-400 text-[10px] font-bold">{{$galleryMedia->count()}} image(s)</span>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                @foreach($galleryMedia as $img)
                    @php
                        $url    = asset($upPath . $img->path);
                        $exists = file_exists(public_path($upPath . $img->path));
                    @endphp
                    @if($exists)
                        <a href="{{$url}}" target="_blank"><img src="{{$url}}" alt="" class="w-full h-20 object-cover rounded-lg border border-[#e8edf5] hover:opacity-80 transition"></a>
                    @else
                        <div class="w-full h-20 rounded-lg bg-d-800 border border-[#e8edf5] flex items-center justify-center"><span class="text-[9px] font-bold text-red-400">MISSING</span></div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- All Campaign Verifications --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-list-check text-t-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">All Verification Items for this Campaign</span>
            </div>
            @if($relatedVerifications->isEmpty())
                <p class="text-xs text-d-500">No verification records found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-[#e8edf5]">
                                <th class="px-3 py-2 text-[10px] font-bold text-d-500 uppercase tracking-wider">Type</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-d-500 uppercase tracking-wider">Verified By</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-d-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatedVerifications as $rv)
                                @php $rvColor = $statusMap[$rv->status] ?? 'secondary'; @endphp
                                <tr class="border-b border-[#e8edf5] last:border-0 {{$rv->id === $verification->id ? 'bg-t-500/5' : ''}}">
                                    <td class="px-3 py-2.5">
                                        <span class="text-xs font-semibold text-d-200"><i class="fas {{$allTypes[$rv->type] ?? 'fa-question'}} mr-1 text-d-400"></i>{{ucfirst($rv->type)}}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="px-2 py-0.5 rounded-md bg-{{$rvColor}}/10 text-{{$rvColor}} text-[10px] font-bold">{{ucfirst($rv->status)}}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-xs text-d-300">{{$rv->verified_by ?? '-'}}</td>
                                    <td class="px-3 py-2.5 text-xs text-d-400">{{optional($rv->created_at)->format('M j, H:i')}}</td>
                                    <td class="px-3 py-2.5">
                                        <a href="{{route('admin.verification.view', $rv->id)}}" class="text-[10px] font-semibold text-t-400 hover:underline">View</a>
                                        @if($rv->id === $verification->id)<span class="ml-1 text-[10px] font-bold text-d-400">(current)</span>@endif
                                        @if($rv->status === 'pending')
                                            <form action="{{route('admin.verification.verify', $rv->id)}}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Approve {{ucfirst($rv->type)}} verification?')" class="text-[10px] font-bold text-green-400 hover:text-green-300" title="Approve"><i class="fas fa-check-circle"></i></button>
                                            </form>
                                            <form action="{{route('admin.verification.reject', $rv->id)}}" method="POST" class="inline-block ml-1">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Reject {{ucfirst($rv->type)}} verification?')" class="text-[10px] font-bold text-red-400 hover:text-red-300" title="Reject"><i class="fas fa-times-circle"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @endif {{-- end if campaign --}}

    </div>

    {{-- ============================================================ --}}
    {{-- RIGHT COLUMN (1/3) — Verification Detail + Actions --}}
    {{-- ============================================================ --}}
    <div class="space-y-6">

        {{-- This Verification --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-stamp text-t-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Verification Item</span>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Status</span>
                    <span class="px-2.5 py-1 rounded-lg bg-{{$statusColor}}/10 text-{{$statusColor}} text-xs font-bold">{{ucfirst($verification->status)}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Type</span>
                    <span class="text-xs text-d-100 font-semibold"><i class="fas {{$allTypes[$verification->type] ?? 'fa-question'}} mr-1 text-d-400"></i>{{ucfirst($verification->type)}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Reviewed By</span>
                    <span class="text-xs text-d-200 font-medium">{{$verification->verified_by ?? 'Not yet reviewed'}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Created</span>
                    <span class="text-xs text-d-200">{{optional($verification->created_at)->format('M j, Y H:i')}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Updated</span>
                    <span class="text-xs text-d-200">{{optional($verification->updated_at)->format('M j, Y H:i')}}</span>
                </div>
                @if(!empty($verification->notes))
                <div class="py-2">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider block mb-1">Notes</span>
                    <p class="text-xs text-d-200 leading-relaxed bg-d-800 rounded-lg p-3">{{$verification->notes}}</p>
                </div>
                @endif
                @if(!empty($verification->document_path))
                <div class="py-2">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider block mb-1">Attached Document</span>
                    @php $docUrl = asset($upPath . $verification->document_path); @endphp
                    <a href="{{$docUrl}}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#4285f4]/10 text-[#4285f4] text-xs font-semibold hover:bg-[#4285f4]/20 transition">
                        <i class="fas fa-external-link-alt"></i>View Document
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Fraud & Campaign Status --}}
        @if($campaign)
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-red-400/10 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-red-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Fraud & Status</span>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Campaign Verification</span>
                    <span class="px-2 py-0.5 rounded-md bg-{{$campStatusCol}}/10 text-{{$campStatusCol}} text-[10px] font-bold">{{ucfirst($campaign->verification_status ?? 'pending')}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Fraud Score</span>
                    <span class="text-sm font-bold {{$fraudColor}}">{{$fraudScore}} / 100</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Wallet Verified</span>
                    @if($campaign->wallet_verified)
                        <span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-400 text-[10px] font-bold"><i class="fas fa-check mr-1"></i>Verified</span>
                    @else
                        <span class="px-2 py-0.5 rounded-md bg-d-800 text-d-400 text-[10px] font-bold">Not Verified</span>
                    @endif
                </div>
                @if(!empty($campaign->document_hash))
                <div class="flex items-center justify-between py-2 border-b border-[#e8edf5]">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Doc Hash</span>
                    <code class="text-[9px] text-d-300 bg-d-800 px-2 py-0.5 rounded font-mono">{{substr($campaign->document_hash, 0, 16)}}…</code>
                </div>
                @endif
                <div class="flex items-center justify-between py-2">
                    <span class="text-[10px] text-d-500 uppercase tracking-wider">Campaign Status</span>
                    <span class="px-2 py-0.5 rounded-md bg-d-800 text-d-300 text-[10px] font-bold">{{ucfirst($campaign->status ?? 'pending')}}</span>
                </div>
            </div>
        </div>

        {{-- Wallet --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-400/10 flex items-center justify-center"><i class="fab fa-ethereum text-emerald-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Blockchain Wallet</span>
            </div>
            @if(!empty($campaign->wallet_address))
                <div class="bg-d-800 rounded-xl p-3">
                    <code class="text-[10px] text-d-300 font-mono break-all block mb-2">{{$campaign->wallet_address}}</code>
                    <button type="button" onclick="navigator.clipboard.writeText('{{$campaign->wallet_address}}').then(()=>alert('Copied'))" class="text-[10px] font-semibold text-t-400 hover:underline"><i class="fas fa-copy mr-1"></i>Copy</button>
                </div>
            @else
                <p class="text-xs text-d-500">No wallet address.</p>
            @endif
        </div>
        @endif

        {{-- Actions --}}
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-gavel text-t-400 text-xs"></i></div>
                <span class="text-sm font-bold text-d-100">Actions</span>
            </div>

            @if($relatedVerifications->where('status','pending')->count() > 0 && $campaign)
                <div class="mb-4 p-3 rounded-xl bg-d-800/60 border border-[#e8edf5]">
                    <span class="block text-[10px] text-d-500 font-bold uppercase tracking-wider mb-2"><i class="fas fa-layers mr-1 text-t-400"></i>Bulk — {{$relatedVerifications->where('status','pending')->count()}} pending item(s) for this campaign</span>
                    <div class="flex gap-2">
                        <form action="{{route('admin.verification.verify.all',$campaign->id)}}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Approve ALL pending verification items for this campaign?')" class="w-full px-3 py-2 rounded-xl bg-green-500/15 border border-green-500/40 text-green-400 text-[11px] font-bold hover:bg-green-500 hover:text-white transition">
                                <i class="fas fa-check-double mr-1"></i>Approve All Pending
                            </button>
                        </form>
                        <form action="{{route('admin.verification.reject.all',$campaign->id)}}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject ALL pending verification items for this campaign?')" class="w-full px-3 py-2 rounded-xl bg-red-500/15 border border-red-500/40 text-red-400 text-[11px] font-bold hover:bg-red-500 hover:text-white transition">
                                <i class="fas fa-ban mr-1"></i>Reject All Pending
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($verification->status === 'verified')
                <div class="mb-3 text-center">
                    <span class="px-4 py-2 rounded-xl bg-green-500/10 text-green-400 text-sm font-bold"><i class="fas fa-check-circle mr-1"></i>{{ucfirst($verification->status)}}</span>
                    @if($verification->verified_by)
                        <p class="text-[11px] text-d-400 mt-2">By <span class="text-d-200 font-medium">{{$verification->verified_by}}</span> on {{optional($verification->updated_at)->format('M j, Y H:i')}}</p>
                    @endif
                </div>
            @elseif($verification->status === 'rejected')
                <div class="mb-3 text-center">
                    <span class="px-4 py-2 rounded-xl bg-red-500/10 text-red-400 text-sm font-bold"><i class="fas fa-times-circle mr-1"></i>{{ucfirst($verification->status)}}</span>
                    @if($verification->verified_by)
                        <p class="text-[11px] text-d-400 mt-2">By <span class="text-d-200 font-medium">{{$verification->verified_by}}</span> on {{optional($verification->updated_at)->format('M j, Y H:i')}}</p>
                    @endif
                </div>
            @endif

            <form action="{{route('admin.verification.verify', $verification->id)}}" method="POST" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-[10px] text-d-500 font-bold uppercase tracking-wider mb-1.5">Admin Notes (optional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-xs text-d-100 placeholder-d-600 focus:border-t-500 focus:ring-1 focus:ring-t-500/30 outline-none transition" placeholder="Add a note for this decision…"></textarea>
                    </div>
                    <button type="submit" onclick="return confirm('Approve this verification?')" class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs font-bold transition mb-2">
                        <i class="fas fa-check mr-1"></i>Approve Verification
                    </button>
                </form>
                <form action="{{route('admin.verification.reject', $verification->id)}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-[10px] text-d-500 font-bold uppercase tracking-wider mb-1.5">Rejection Reason (optional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-xs text-d-100 placeholder-d-600 focus:border-red-500 focus:ring-1 focus:ring-red-500/30 outline-none transition" placeholder="Why is this being rejected?"></textarea>
                    </div>
                    <button type="submit" onclick="return confirm('Reject this verification?')" class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs font-bold transition">
                        <i class="fas fa-times mr-1"></i>Reject Verification
                    </button>
                </form>
        </div>

    </div>
</div>

@endsection
