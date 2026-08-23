<!doctype html>
<html lang="{{get_default_language()}}">
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>{{config("app.name","MediFund")}} - @yield("title","Dashboard")</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
@php $fav = get_attachment_image_by_id(get_static_option("site_favicon"),"full",false); @endphp
@if(!empty($fav))<link rel="icon" href="{{$fav["img_url"]}}">@endif
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config={darkMode:'class',theme:{extend:{colors:{d:{50:'#1b2537',100:'#1d2635',200:'#33415c',300:'#44526c',400:'#5b6b83',500:'#8a97ad',600:'#9aa7bb',700:'#dbe2ee',800:'#f6f8fc',900:'#ffffff',950:'#eff3fb'},t:{50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#2dd4bf',400:'#14b8a6',500:'#0ea5a4',600:'#0d9488',700:'#0f766e',800:'#115e59',900:'#134e4a'},h:{blue:'#4285f4',bluedeep:'#2563eb'}},fontFamily:{sans:['Inter','system-ui','sans-serif'],mono:['JetBrains Mono','monospace']}}}}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{asset("assets/common/css/bootstrap.min.css")}}">
<link rel="stylesheet" href="{{asset("assets/backend/css/default-css.css")}}">
<link rel="stylesheet" href="{{asset("assets/backend/css/styles.css")}}">
<link rel="stylesheet" href="{{asset("assets/common/css/toastr.css")}}">
<style>
*{scrollbar-width:thin;scrollbar-color:#c9d4e5 transparent}
*::-webkit-scrollbar{width:6px;height:6px}
*::-webkit-scrollbar-track{background:transparent}
*::-webkit-scrollbar-thumb{background:#c9d4e5;border-radius:999px}
*::-webkit-scrollbar-thumb:hover{background:#aebdd4}
.sl{transition:all .15s ease;color:#5b6b83}
.sl:hover{background:#f2f6fc;color:#1d2635}
.sl.act{background:rgba(66,133,244,0.09);color:#2563eb;font-weight:600}
.sl.act i{color:#4285f4}
.sc{transition:all .2s ease;box-shadow:0 1px 2px rgba(30,41,59,.04)}
.sc:hover{transform:translateY(-2px);box-shadow:0 12px 28px -8px rgba(30,41,59,.14)}
@keyframes fu{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.fu{animation:fu .3s ease forwards;opacity:0}
.fu:nth-child(1){animation-delay:0s}.fu:nth-child(2){animation-delay:.04s}.fu:nth-child(3){animation-delay:.08s}.fu:nth-child(4){animation-delay:.12s}.fu:nth-child(5){animation-delay:.16s}.fu:nth-child(6){animation-delay:.2s}.fu:nth-child(7){animation-delay:.24s}.fu:nth-child(8){animation-delay:.28s}
[x-cloak]{display:none!important}
body{background:#eff3fb!important;color:#33415c!important}
.card{background:#ffffff!important;border:1px solid #e8edf5!important;border-radius:16px!important;box-shadow:0 2px 10px rgba(30,41,59,.05)!important}
.card-body{background:transparent!important}
.header-title{color:#1d2635!important}
.table{color:#44526c!important}
.table thead th{color:#8a97ad!important;border-color:#e8edf5!important;font-size:11px;text-transform:uppercase;letter-spacing:.06em;background:#fafbfe}
.table td{border-color:#eef2f9!important;vertical-align:middle}
.table-default{border-color:#e8edf5!important}
.table-default thead th{background:#fafbfe!important;color:#8a97ad!important;border-color:#e8edf5!important}
.table-default td{border-color:#eef2f9!important}
.btn-info{background:#4285f4!important;border-color:#4285f4!important;color:#fff!important}
.btn-info:hover{background:#2f6fe0!important;border-color:#2f6fe0!important}
.btn-primary{background:#4285f4!important;border-color:#4285f4!important}
.btn-primary:hover,.btn-primary:focus{background:#2f6fe0!important;border-color:#2f6fe0!important}
.btn-danger{background:#ef4444!important;border-color:#ef4444!important}
.btn-warning{background:#f59e0b!important;border-color:#f59e0b!important}
.btn-success{background:#22c55e!important;border-color:#22c55e!important}
.form-control{background:#ffffff!important;border-color:#dde5f0!important;color:#1d2635!important;border-radius:10px!important;padding:.55rem .85rem!important;font-size:13px}
.form-control::placeholder{color:#9aa7bb}
.form-control:focus{border-color:#4285f4!important;box-shadow:0 0 0 3px rgba(66,133,244,.14)!important}
select.form-control{background:#ffffff!important;color:#1d2635!important}
label{color:#44526c!important;font-weight:600;font-size:12.5px}
h4{color:#1d2635!important}h5{color:#1d2635!important}h6{color:#1d2635!important}
.text-muted{color:#8a97ad!important}
.pagination .page-link{background:#ffffff!important;border-color:#e2e9f3!important;color:#5b6b83!important;border-radius:10px!important;margin:0 3px;font-weight:600}
.pagination .page-item.active .page-link{background:#4285f4!important;border-color:#4285f4!important;color:#fff!important;box-shadow:0 4px 12px rgba(66,133,244,.35)}
.modal-content{background:#ffffff!important;border:1px solid #e8edf5!important;border-radius:18px!important;box-shadow:0 24px 60px rgba(30,41,59,.18)}
.modal-header{border-color:#eef2f9!important}
.modal-footer{border-color:#eef2f9!important}
.dropdown-menu{background:#ffffff!important;border:1px solid #e8edf5!important;border-radius:14px!important;box-shadow:0 16px 40px rgba(30,41,59,.14)}
.dropdown-item{color:#44526c!important;border-radius:8px;margin:2px 6px;width:auto;padding:.45rem .75rem}
.dropdown-item:hover{background:#f2f6fc!important;color:#1d2635!important}
.alert-success{background:#ecfdf3!important;border-color:#d3f5e2!important;color:#15803d!important}
.alert-danger{background:#fef2f2!important;border-color:#fee2e2!important;color:#dc2626!important}
.alert-warning{background:#fffbeb!important;border-color:#fdf0c8!important;color:#b45309!important}
.alert-info{background:#eff6ff!important;border-color:#dbeafe!important;color:#2563eb!important}
.dataTables_wrapper .dataTables_length select,.dataTables_wrapper .dataTables_filter input{background:#ffffff!important;border:1px solid #dde5f0!important;color:#1d2635!important;border-radius:10px!important;padding:4px 10px!important;outline:none}
.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{color:#8a97ad!important}
.dataTables_wrapper .dataTables_paginate .paginate_button{color:#5b6b83!important;background:#ffffff!important;border:1px solid #e2e9f3!important;margin:0 2px!important;border-radius:8px!important}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:#f2f6fc!important;color:#1d2635!important;border-color:#c9d6ea!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:#4285f4!important;color:#fff!important;border-color:#4285f4!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:.35;cursor:default}
table.dataTable thead th{color:#8a97ad!important}
table.dataTable tbody tr{background-color:transparent!important}
table.dataTable tbody tr:hover{background-color:#f7fafe!important}
table.dataTable.row-border tbody th,table.dataTable.row-border tbody td,table.dataTable.display tbody th,table.dataTable.display tbody td{border-top:1px solid #eef2f9!important}
.dataTables_processing{background:#ffffff!important;border:1px solid #e8edf5!important;color:#2563eb!important;border-radius:12px!important}
table.dataTable thead .sorting:before,table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_desc:before{color:#b6c2d6!important}
/* Hope UI extras */
.h-card{background:#fff;border:1px solid #e8edf5;border-radius:16px;box-shadow:0 2px 10px rgba(30,41,59,.05)}
input[type=checkbox],input[type=radio]{accent-color:#4285f4}
</style>
@yield("head")
@yield("style")
</head>
<body class="bg-d-950 text-d-50 font-sans antialiased">
@php $admin = auth()->guard("admin")->user(); @endphp

<div id="sidebar" class="fixed top-0 left-0 h-full w-[260px] bg-d-900 border-r border-[#e8edf5] z-50 flex flex-col transition-all duration-300">
    <div class="h-16 flex items-center gap-3 px-5 border-b border-[#e8edf5] shrink-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#4285f4] to-[#2563eb] flex items-center justify-center shadow-lg shadow-[#4285f4]/25 shrink-0">
            <i class="fas fa-heartbeat text-white text-sm"></i>
        </div>
        <span class="text-lg font-extrabold tracking-tight">Medi<span class="text-[#4285f4]">Fund</span></span>
    </div>
    <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5" id="snav">
        <a href="{{route("admin.home")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400 {{request()->routeIs("admin.home")?"act":""}}">
            <i class="fas fa-th-large w-5 text-center text-sm"></i><span>Dashboard</span>
        </a>
        <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest text-d-500">Campaigns</div>
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-file-medical w-5 text-center text-sm"></i><span>Campaigns</span>
            @if($pending_cases_count > 0)<span class="ml-auto text-[10px] font-bold bg-[#4285f4] text-white px-2 py-0.5 rounded-full">{{$pending_cases_count}}</span>@endif
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/donations*")?"":"hidden"}}">
            <a href="{{route("admin.donations.all")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">All Campaigns</a>
            <a href="{{route("admin.donations.new")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Create Campaign</a>
            <a href="{{route("admin.donations.category.all")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Categories</a>
            <a href="{{route("admin.donations.pending.all")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Pending Review</a>
            <a href="{{route("admin.donations.payment.logs")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Donation Logs</a>
        </div>
        <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest text-d-500">Blockchain & Security</div>
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-link w-5 text-center text-sm"></i><span>Blockchain</span>
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/blockchain*")?"":"hidden"}}">
            <a href="{{route("admin.blockchain.all")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">All Transactions</a>
            <a href="{{route("admin.blockchain.settings")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Wallet Settings</a>
        </div>
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-shield-alt w-5 text-center text-sm"></i><span>Fraud Detection</span>
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/fraud*")?"":"hidden"}}">
            <a href="{{route("admin.fraud.dashboard")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Dashboard</a>
            <a href="{{route("admin.fraud.reports")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">All Reports</a>
        </div>
        <a href="{{route("admin.patient.wallets")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400 {{request()->is("admin-home/patient-wallets*")?"act":""}}">
            <i class="fas fa-hand-holding-usd w-5 text-center text-sm"></i><span>Patient Wallets</span>
        </a>
        <a href="{{route("admin.verifications.all")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400 {{request()->routeIs("admin.verifications*")?"act":""}}">
            <i class="fas fa-check-double w-5 text-center text-sm"></i><span>Verifications</span>
        </a>
        <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest text-d-500">Administration</div>
        @canany(["user-list","user-create"])
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-users w-5 text-center text-sm"></i><span>Users</span>
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/frontend*")?"":"hidden"}}">
            @can("user-list")<a href="{{route("admin.all.frontend.user")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">All Users</a>@endcan
            @can("user-create")<a href="{{route("admin.frontend.new.user")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Add New</a>@endcan
        </div>
        @endcanany
        @if($admin->hasRole("Super Admin"))
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-user-shield w-5 text-center text-sm"></i><span>Admins</span>
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/admin*")?"":"hidden"}}">
            <a href="{{route("admin.all.user")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">All Admins</a>
            <a href="{{route("admin.new.user")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Add New</a>
            <a href="{{route("admin.all.admin.role")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400">Roles</a>
        </div>
        @endif
        @canany(["donation-withdraw-list"])
        <a href="#" onclick="this.nextElementSibling.classList.toggle('hidden');return false" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400">
            <i class="fas fa-wallet w-5 text-center text-sm"></i><span>Withdrawals</span>
            @if($pending_withdraw_count > 0)<span class="ml-auto text-[10px] font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full">{{$pending_withdraw_count}}</span>@endif
            <i class="fas fa-chevron-down ml-auto text-[10px] opacity-40"></i>
        </a>
        <div class="ml-5 border-l border-[#e8edf5] pl-3 space-y-0.5 {{request()->is("admin-home/donations/withdraw*")||request()->is("admin-home/donations/escrow*")?"":"hidden"}}">
            <a href="{{route("admin.all.donation.withdraw.request")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400 {{request()->is("admin-home/donations/withdraw*")?"act":""}}">All Requests</a>
            <a href="{{route("admin.donations.escrow.index")}}" class="sl block px-3 py-2 rounded-lg text-xs text-d-400 {{request()->is("admin-home/donations/escrow*")?"act":""}}">Escrow Disbursements</a>
        </div>
        @endcanany
        <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest text-d-500">Settings</div>
        <a href="{{route("admin.navbar.settings")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400"><i class="fas fa-palette w-5 text-center text-sm"></i><span>Appearance</span></a>
        <a href="{{route("admin.general.site.identity")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400"><i class="fas fa-cog w-5 text-center text-sm"></i><span>General</span></a>
        <a href="{{route("admin.blockchain.settings")}}" class="sl flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-d-400"><i class="fab fa-ethereum w-5 text-center text-sm"></i><span>Wallet Settings</span></a>
    </nav>
    <div class="p-3 border-t border-[#e8edf5] shrink-0">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#4285f4] to-[#2563eb] flex items-center justify-center text-white text-xs font-bold shrink-0">{{substr($admin->name,0,1)}}</div>
            <div class="min-w-0"><div class="text-sm font-semibold text-d-100 truncate">{{$admin->name}}</div><div class="text-[11px] text-d-500 truncate">Admin</div></div>
        </div>
    </div>
</div>

<div id="main" class="ml-[260px] min-h-screen flex flex-col transition-all duration-300">
    <header class="h-14 bg-d-900/80 backdrop-blur-xl border-b border-[#e8edf5] flex items-center justify-between px-6 sticky top-0 z-40 shrink-0">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="text-d-400 hover:text-[#1d2635] transition p-1.5 rounded-lg hover:bg-[#f2f6fc]"><i class="fas fa-bars text-lg"></i></button>
            <h1 class="text-sm font-semibold text-d-100 hidden sm:block">@yield("title","Dashboard")</h1>
            <div class="relative ml-2 hidden md:block">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-d-500 text-xs"></i>
                <input type="text" placeholder="Search..." class="w-56 lg:w-72 bg-d-800/60 border border-[#e8edf5] rounded-lg pl-9 pr-14 py-1.5 text-xs text-d-200 placeholder-d-500 outline-none focus:border-[#4285f4]/50 focus:ring-2 focus:ring-[#4285f4]/10 transition">
                <kbd class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-d-500 bg-d-700/60 border border-[#e8edf5] rounded px-1.5 py-0.5">?K</kbd>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{url("/")}}" target="_blank" class="text-xs font-medium text-d-400 hover:text-[#1d2635] px-3 py-1.5 rounded-lg border border-[#e8edf5] hover:bg-[#f2f6fc] transition"><i class="fas fa-external-link-alt mr-1"></i>View Site</a>
            <div x-data="{open:false}" class="relative">
                <button @click="open=!open" class="relative p-2 rounded-lg text-d-400 hover:text-[#1d2635] hover:bg-[#f2f6fc] transition"><i class="fas fa-bell text-[15px]"></i>
                    @if(count($new_notification) > 0)<span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{count($new_notification)}}</span>@endif
                </button>
                <div x-show="open" @click.outside="open=false" x-transition x-cloak class="absolute right-0 top-full mt-2 w-80 bg-d-800 border border-[#e8edf5] rounded-xl shadow-2xl overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-[#e8edf5] text-xs font-bold text-d-200">Notifications</div>
                    @forelse($new_notification->take(5) as $n)
                    <a href="{{route("admin.notification.view",$n->id)}}" class="flex items-start gap-3 px-4 py-3 hover:bg-[#f2f6fc] transition border-b border-[#e8edf5] last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center shrink-0 mt-0.5"><i class="fas fa-bell text-[#4285f4] text-xs"></i></div>
                        <div class="min-w-0"><div class="text-xs font-medium text-d-100 truncate">{{$n->title ?? ""}}</div><div class="text-[11px] text-d-500 mt-0.5">{{$n->created_at->diffForHumans()}}</div></div>
                    </a>
                    @empty
                    <div class="px-4 py-6 text-center text-xs text-d-500">No notifications</div>
                    @endforelse
                    <a href="{{route("admin.notification")}}" class="block px-4 py-2.5 text-center text-[11px] font-semibold text-[#4285f4] hover:bg-[#f2f6fc] transition">View All</a>
                </div>
            </div>
            <div x-data="{open:false}" class="relative">
                <button @click="open=!open" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-[#e8edf5] hover:bg-[#f2f6fc] transition">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#4285f4] to-[#2563eb] flex items-center justify-center text-white text-[11px] font-bold">{{substr($admin->name,0,1)}}</div>
                    <span class="text-xs font-medium text-d-200 max-w-[100px] truncate hidden sm:block">{{$admin->name}}</span>
                    <i class="fas fa-chevron-down text-[9px] text-d-500"></i>
                </button>
                <div x-show="open" @click.outside="open=false" x-transition x-cloak class="absolute right-0 top-full mt-2 w-48 bg-d-800 border border-[#e8edf5] rounded-xl shadow-2xl overflow-hidden z-50">
                    <a href="{{route("admin.profile.update")}}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-d-300 hover:bg-[#f2f6fc] transition"><i class="fas fa-user w-4 text-center text-d-500"></i>Profile</a>
                    <a href="{{route("admin.password.change")}}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-d-300 hover:bg-[#f2f6fc] transition"><i class="fas fa-key w-4 text-center text-d-500"></i>Password</a>
                    <div class="border-t border-[#e8edf5]"></div>
                    <a href="{{route("admin.logout")}}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-red-400 hover:bg-[#f2f6fc] transition"><i class="fas fa-sign-out-alt w-4 text-center"></i>Logout</a>
                </div>
            </div>
        </div>
    </header>
    <main class="flex-1 p-6">
        @if(session("msg"))<div class="mb-4 px-4 py-3 rounded-xl bg-[#4285f4]/10 border border-t-500/20 text-[#4285f4] text-sm font-medium flex items-center gap-2"><i class="fas fa-check-circle"></i>{{session("msg")}}</div>@endif
    @yield("content")
    </main>
    <footer class="px-6 py-4 border-t border-[#e8edf5] text-center text-[11px] text-d-500">MediFund &copy; {{date("Y")}} � Blockchain-Powered Medical Crowdfunding</footer>
</div>

<script>
function toggleSidebar(){var s=document.getElementById('sidebar'),m=document.getElementById('main');s.classList.toggle('-translate-x-full');m.classList.toggle('ml-0');m.classList.toggle('ml-[260px]')}
</script>
<script src="{{asset("assets/common/js/jquery-3.6.0.min.js")}}"></script>
<script src="{{asset("assets/common/js/bootstrap.min.js")}}"></script>
<script src="{{asset("assets/common/js/toastr.min.js")}}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
@if(session("msg"))
toastr.options={positionClass:"toast-bottom-right",timeOut:3000};
toastr.{{session("type") ?? "success"}}("{{session("msg")}}");
@endif
</script>
@yield("script")
@yield("scripts")
@stack("script")
</body>
</html>
