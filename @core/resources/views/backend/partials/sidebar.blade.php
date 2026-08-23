<div class="mf-sidebar">
    <div class="mf-slogo">
        <a href="{{route("admin.home")}}" style="display:flex;align-items:center;gap:12px;text-decoration:none">
            <div class="mf-slogo-icon"><i class="fas fa-heartbeat"></i></div>
            <div class="mf-slogo-text">Medi<span>Fund</span></div>
        </a>
    </div>
    <div class="mf-snav">
        <ul class="metismenu" id="menu">
            <li class="{{active_menu("admin-home")}}">
                <a href="{{route("admin.home")}}" title="{{__("Dashboard")}}"><i class="fas fa-th-large"></i><span>{{__("Dashboard")}}</span></a>
            </li>

            <li class="section-label">{{__("Campaigns")}}</li>

            <li class="{{request()->is("admin-home/donations*")?"active":""}}">
                <a href="#" title="{{__("Campaigns")}}"><i class="fas fa-file-medical"></i><span>{{__("Campaigns")}}</span>@if($pending_cases_count > 0)<span class="badge">{{$pending_cases_count}}</span>@endif</a>
                <ul class="collapse {{request()->is("admin-home/donations*")?"show":""}}">
                    <li><a href="{{route("admin.donations.all")}}">{{__("All Campaigns")}}</a></li>
                    <li><a href="{{route("admin.donations.new")}}">{{__("Create Campaign")}}</a></li>
                    <li><a href="{{route("admin.donations.category.all")}}">{{__("Categories")}}</a></li>
                    <li><a href="{{route("admin.donations.pending.all")}}">{{__("Pending Review")}}</a></li>
                    <li><a href="{{route("admin.donations.payment.logs")}}">{{__("Donation Logs")}}</a></li>
                </ul>
            </li>

            <li class="section-label">{{__("Blockchain & Security")}}</li>

            <li class="{{request()->is("admin-home/blockchain*")?"active":""}}">
                <a href="#" title="{{__("Blockchain")}}"><i class="fas fa-link"></i><span>{{__("Blockchain")}}</span></a>
                <ul class="collapse {{request()->is("admin-home/blockchain*")?"show":""}}">
                    <li><a href="{{route("admin.blockchain.all")}}">{{__("All Transactions")}}</a></li>
                    <li><a href="{{route("admin.blockchain.settings")}}">{{__("Wallet Settings")}}</a></li>
                </ul>
            </li>

            <li class="{{request()->is("admin-home/fraud*")?"active":""}}">
                <a href="#" title="{{__("Fraud Detection")}}"><i class="fas fa-shield-alt"></i><span>{{__("Fraud Detection")}}</span></a>
                <ul class="collapse {{request()->is("admin-home/fraud*")?"show":""}}">
                    <li><a href="{{route("admin.fraud.dashboard")}}">{{__("Dashboard")}}</a></li>
                    <li><a href="{{route("admin.fraud.reports")}}">{{__("All Reports")}}</a></li>
                </ul>
            </li>

            <li class="{{request()->is("admin-home/verifications*")?"active":""}}">
                <a href="{{route("admin.verifications.all")}}" title="{{__("Verifications")}}"><i class="fas fa-check-double"></i><span>{{__("Verifications")}}</span></a>
            </li>

            <li class="{{request()->is("admin-home/campaigns/*/milestones*")?"active":""}}">
                <a href="#" title="{{__("Milestones & Escrow")}}"><i class="fas fa-road"></i><span>{{__("Milestones & Escrow")}}</span></a>
            </li>

            <li class="section-label">{{__("Administration")}}</li>

            @canany(["user-list","user-create"])
            <li class="{{request()->is("admin-home/frontend/*")?"active":""}}">
                <a href="#" title="{{__("Users")}}"><i class="fas fa-users"></i><span>{{__("Users")}}</span></a>
                <ul class="collapse {{request()->is("admin-home/frontend/*")?"show":""}}">
                    @can("user-list")<li><a href="{{route("admin.all.frontend.user")}}">{{__("All Users")}}</a></li>@endcan
                    @can("user-create")<li><a href="{{route("admin.frontend.new.user")}}">{{__("Add New User")}}</a></li>@endcan
                </ul>
            </li>
            @endcanany

            @if(auth()->guard("admin")->user()->hasRole("Super Admin"))
            <li class="{{request()->is("admin-home/admin/*")?"active":""}}">
                <a href="#" title="{{__("Admins")}}"><i class="fas fa-user-shield"></i><span>{{__("Admins")}}</span></a>
                <ul class="collapse {{request()->is("admin-home/admin/*")?"show":""}}">
                    <li><a href="{{route("admin.all.user")}}">{{__("All Admins")}}</a></li>
                    <li><a href="{{route("admin.new.user")}}">{{__("Add New")}}</a></li>
                    <li><a href="{{route("admin.all.admin.role")}}">{{__("Roles")}}</a></li>
                </ul>
            </li>
            @endif

            @canany(["donation-withdraw-list"])
            <li class="{{request()->is("admin-home/donations/withdraw*")?"active":""}}">
                <a href="#" title="{{__("Withdrawals")}}"><i class="fas fa-wallet"></i><span>{{__("Withdrawals")}}</span>@if($pending_withdraw_count > 0)<span class="badge">{{$pending_withdraw_count}}</span>@endif</a>
                <ul class="collapse {{request()->is("admin-home/donations/withdraw*")?"show":""}}">
                    <li><a href="{{route("admin.all.donation.withdraw.request")}}">{{__("All Requests")}}</a></li>
                    <li><a href="{{route("admin.donations.escrow.index")}}">{{__("Escrow Disbursements")}}</a></li>
                </ul>
            </li>
            @endcanany

            <li class="section-label">{{__("Settings")}}</li>

            <li class="{{request()->is("admin-home/appearance-settings*")?"active":""}}">
                <a href="{{route("admin.navbar.settings")}}" title="{{__("Appearance")}}"><i class="fas fa-palette"></i><span>{{__("Appearance")}}</span></a>
            </li>
            <li class="{{request()->is("admin-home/general-settings*")?"active":""}}">
                <a href="{{route("admin.general.site.identity")}}" title="{{__("General")}}"><i class="fas fa-cog"></i><span>{{__("General")}}</span></a>
            </li>
            <li class="{{request()->is("admin-home/blockchain/settings")?"active":""}}">
                <a href="{{route("admin.blockchain.settings")}}" title="{{__("Wallet Settings")}}"><i class="fab fa-ethereum"></i><span>{{__("Wallet Settings")}}</span></a>
            </li>
            @if(auth()->guard("admin")->user()->hasRole("Super Admin"))
            <li class="{{request()->is("admin-home/notification*")?"active":""}}">
                <a href="{{route("admin.notification")}}" title="{{__("Notifications")}}"><i class="fas fa-bell"></i><span>{{__("Notifications")}}</span></a>
            </li>
            @endif
        </ul>
    </div>
</div>
