@extends('backend.admin-master')
@section('site-title')
    {{__('User Verification Review')}}
@endsection

@section('content')

    @php
        $upPath = 'assets/uploads/media-uploader/';

        $nidMedia    = !empty($user_verify->user_verify_nid)    ? \App\MediaUpload::find($user_verify->user_verify_nid)    : null;
        $addrMedia   = !empty($user_verify->user_verify_address) ? \App\MediaUpload::find($user_verify->user_verify_address) : null;

        $nidUrl      = $nidMedia  ? asset($upPath . $nidMedia->path)  : '';
        $addrUrl     = $addrMedia ? asset($upPath . $addrMedia->path) : '';
        $nidExists   = $nidMedia  ? file_exists(public_path($upPath . $nidMedia->path))  : false;
        $addrExists  = $addrMedia ? file_exists(public_path($upPath . $addrMedia->path)) : false;
        $nidIsImg    = $nidMedia  ? str_contains(strtolower($nidMedia->type ?? ''), 'image') : false;
        $addrIsImg   = $addrMedia ? str_contains(strtolower($addrMedia->type ?? ''), 'image') : false;

        $nidName     = $nidMedia  ? basename($nidMedia->path) : __('No document submitted');
        $addrName    = $addrMedia ? basename($addrMedia->path) : __('No document submitted');

        $status      = (int)$user_verify->user_verify_status;
        $statusMap   = [
            0 => ['Not Submitted', 'secondary'],
            1 => ['Pending Review', 'warning'],
            2 => ['Verified', 'success'],
            3 => ['Rejected', 'danger'],
        ];
        [$statusLabel, $statusColor] = $statusMap[$status] ?? ['Unknown', 'secondary'];

        $walletVerified = !empty($user_verify->wallet_verified) && $user_verify->wallet_verified == 1;
    @endphp

    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <x-msg.error/>
                        <x-msg.success/>

                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                            <h4 class="header-title mb-0">
                                {{__('User Verification Review')}}
                                <span class="badge badge-{{$statusColor}} ml-2">{{__($statusLabel)}}</span>
                            </h4>
                            <div>
                                <a class="btn btn-info btn-xs" href="{{route('admin.all.frontend.user')}}">{{__('All Users')}}</a>
                                <a class="btn btn-primary btn-xs" href="{{route('admin.frontend.user.tax.information',$user_verify->id)}}" target="_blank">{{__('Tax Data')}}</a>
                            </div>
                        </div>

                        @if(!$nidMedia && !$addrMedia)
                            <div class="alert alert-warning">
                                {{__('No verification documents found for this user. Ask the user to upload an official ID and an address proof from their dashboard → "User Verification" page.')}}
                            </div>
                        @endif

                        <div class="row">
                            {{-- LEFT: account + wallet --}}
                            <div class="col-md-4">
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-dark text-white py-2">
                                        <strong>{{__('Account')}}</strong>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td class="text-muted">{{__('ID')}}</td><td class="text-right">#{{$user_verify->id}}</td></tr>
                                            <tr><td class="text-muted">{{__('Name')}}</td><td class="text-right">{{$user_verify->name}}</td></tr>
                                            <tr><td class="text-muted">{{__('Email')}}</td><td class="text-right">{{$user_verify->email}}</td></tr>
                                            <tr><td class="text-muted">{{__('Phone')}}</td><td class="text-right">{{$user_verify->phone ?? __('N/A')}}</td></tr>
                                            <tr><td class="text-muted">{{__('Role')}}</td><td class="text-right">
                                                <span class="badge badge-{{$user_verify->role === 'patient' ? 'info' : 'light'}}">{{$user_verify->role ?? 'user'}}</span>
                                            </td></tr>
                                            <tr><td class="text-muted">{{__('Status')}}</td><td class="text-right">
                                                <span class="badge badge-{{$user_verify->status == 1 ? 'success' : 'secondary'}}">{{$user_verify->status == 1 ? __('Active') : __('Inactive')}}</span>
                                            </td></tr>
                                            <tr><td class="text-muted">{{__('Joined')}}</td><td class="text-right">{{$user_verify->created_at ? date('M j, Y', strtotime($user_verify->created_at)) : __('N/A')}}</td></tr>
                                            <tr><td class="text-muted">{{__('Campaign Permission')}}</td><td class="text-right">
                                                <span class="badge badge-{{$user_verify->campaign_permission == 'on' ? 'success' : 'secondary'}}">{{$user_verify->campaign_permission == 'on' ? __('On') : __('Off')}}</span>
                                            </td></tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-dark text-white py-2">
                                        <strong>{{__('Blockchain Wallet')}}</strong>
                                    </div>
                                    <div class="card-body py-3">
                                        @if(!empty($user_verify->wallet_address))
                                            <code class="d-block mb-2" style="word-break:break-all;">{{$user_verify->wallet_address}}</code>
                                            <span class="badge badge-{{$walletVerified ? 'success' : 'warning'}}">
                                                {{$walletVerified ? __('Wallet Verified') : __('Wallet Not Verified')}}
                                            </span>
                                            <button class="btn btn-sm btn-outline-secondary mt-2" type="button" onclick="copyWallet()">{{__('Copy Address')}}</button>
                                        @else
                                            <p class="text-muted mb-0">{{__('No wallet address connected yet.')}}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: documents + actions --}}
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4 shadow-sm h-100">
                                            <div class="card-header bg-dark text-white py-2">
                                                <strong>{{__('Official ID')}}</strong>
                                            </div>
                                            <div class="card-body text-center">
                                                @if($nidMedia)
                                                    @if($nidIsImg && $nidExists)
                                                        <a href="{{$nidUrl}}" target="_blank">
                                                            <img src="{{$nidUrl}}" alt="{{$nidName}}" class="img-fluid rounded border mb-3" style="max-height:220px;">
                                                        </a>
                                                    @else
                                                        <div class="my-3">
                                                            <i class="fa fa-{{$nidIsImg ? 'file-image-o' : 'file-pdf-o'}} fa-4x text-danger"></i>
                                                        </div>
                                                    @endif
                                                    <p class="mb-1"><strong>{{$nidName}}</strong></p>
                                                    <p class="text-muted small mb-2">{{$nidMedia->type}}</p>
                                                    @if(!$nidExists)
                                                        <div class="alert alert-danger py-1 px-2 small mb-2">{{__('MISSING: file not found on server')}}</div>
                                                    @endif
                                                    <a href="{{$nidUrl}}" target="_blank" class="btn btn-sm btn-primary">{{__('View / Download')}}</a>
                                                @else
                                                    <div class="py-4">
                                                        <i class="fa fa-id-card-o fa-4x text-muted d-block mb-3"></i>
                                                        <span class="badge badge-warning">{{__('No document submitted')}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card mb-4 shadow-sm h-100">
                                            <div class="card-header bg-dark text-white py-2">
                                                <strong>{{__('Address Proof')}}</strong>
                                            </div>
                                            <div class="card-body text-center">
                                                @if($addrMedia)
                                                    @if($addrIsImg && $addrExists)
                                                        <a href="{{$addrUrl}}" target="_blank">
                                                            <img src="{{$addrUrl}}" alt="{{$addrName}}" class="img-fluid rounded border mb-3" style="max-height:220px;">
                                                        </a>
                                                    @else
                                                        <div class="my-3">
                                                            <i class="fa fa-{{$addrIsImg ? 'file-image-o' : 'file-pdf-o'}} fa-4x text-danger"></i>
                                                        </div>
                                                    @endif
                                                    <p class="mb-1"><strong>{{$addrName}}</strong></p>
                                                    <p class="text-muted small mb-2">{{$addrMedia->type}}</p>
                                                    @if(!$addrExists)
                                                        <div class="alert alert-danger py-1 px-2 small mb-2">{{__('MISSING: file not found on server')}}</div>
                                                    @endif
                                                    <a href="{{$addrUrl}}" target="_blank" class="btn btn-sm btn-primary">{{__('View / Download')}}</a>
                                                @else
                                                    <div class="py-4">
                                                        <i class="fa fa-file-text-o fa-4x text-muted d-block mb-3"></i>
                                                        <span class="badge badge-warning">{{__('No document submitted')}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white py-2">
                                        <strong>{{__('Verification Actions')}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap">
                                            @if($status != 2)
                                                <a href="{{route('admin.frontend.user.verify.update',$user_verify->id)}}"
                                                   class="btn btn-success mr-2 mb-2"
                                                   onclick="return confirm('{{__('Approve this user as verified?')}}');">
                                                    <i class="fa fa-check"></i> {{__('Approve Verification')}}
                                                </a>
                                            @endif
                                            @if($status != 3)
                                                <a href="{{route('admin.frontend.user.verify.reject',$user_verify->id)}}"
                                                   class="btn btn-danger mr-2 mb-2"
                                                   onclick="return confirm('{{__('Reject this verification? The user can resubmit documents.')}}');">
                                                    <i class="fa fa-times"></i> {{__('Reject')}}
                                                </a>
                                            @endif
                                            @if($status == 3)
                                                <a href="{{route('admin.frontend.user.verify.reset',$user_verify->id)}}"
                                                   class="btn btn-warning mr-2 mb-2"
                                                   onclick="return confirm('{{__('Move this verification back to pending?')}}');">
                                                    <i class="fa fa-refresh"></i> {{__('Back to Pending')}}
                                                </a>
                                            @endif
                                            <a href="{{route('admin.all.frontend.user')}}" class="btn btn-outline-secondary mb-2">{{__('Back to Users')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function copyWallet() {
            const t = document.createElement('textarea');
            t.value = '{{$user_verify->wallet_address ?? ''}}';
            document.body.appendChild(t);
            t.select();
            document.execCommand('copy');
            document.body.removeChild(t);
            alert('{{__('Wallet address copied')}}');
        }
    </script>
@endsection