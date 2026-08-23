@extends('backend.admin-master')
@section('site-title')
    {{__('Milestones')}} - {{$campaign->title}}
@endsection

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4"><i class="fas fa-road"></i> {{__('Campaign Milestones')}}</h4>

                    @if(!empty($milestones) && count($milestones) > 0)
                        @foreach($milestones as $m)
                        <div style="background: #111128; border: 1px solid #1e1e3a; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div>
                                    <h5 style="margin: 0; color: #e8e8f0; font-size: 16px;">{{$m->title}}</h5>
                                    <small style="color: #8b8ba3;">{{$m->created_at->diffForHumans()}}</small>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="color: #00D4AA; font-weight: 700;">{{amount_with_currency_symbol($m->amount)}}</span>
                                    <span class="badge badge-{{$m->status_color}}">{{ucfirst(str_replace('_', ' ', $m->status))}}</span>
                                </div>
                            </div>
                            @if(!empty($m->description))
                                <p style="color: #b8b8d0; font-size: 13px; margin-bottom: 10px;">{{$m->description}}</p>
                            @endif
                            @if(!empty($m->proof_notes))
                                <div style="background: #0a0a1a; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
                                    <small style="color: #8b8ba3;"><i class="fas fa-file-alt"></i> {{__('Proof')}}:</small>
                                    <p style="color: #d0d0e0; font-size: 13px; margin: 4px 0 0;">{{$m->proof_notes}}</p>
                                </div>
                            @endif
                            @if($m->status === 'proof_submitted')
                            <div style="display: flex; gap: 10px; margin-top: 12px;">
                                <form action="{{route('admin.milestones.verify', [$campaign->id, $m->id])}}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" name="action" value="verify" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> {{__('Verify & Release Funds')}}
                                    </button>
                                </form>
                                <form action="{{route('admin.milestones.verify', [$campaign->id, $m->id])}}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> {{__('Reject')}}
                                    </button>
                                </form>
                            </div>
                            @endif
                            @if($m->status === 'released')
                            <div style="margin-top: 8px;">
                                <small style="color: #00D4AA;"><i class="fas fa-check-circle"></i> {{__('Funds released on')}} {{$m->released_at ? $m->released_at->diffForHumans() : '-'}}</small>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p style="color: #8b8ba3; text-align: center; padding: 30px;">{{__('No milestones created yet')}}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-wallet"></i> {{__('Escrow Summary')}}</h5>
                    <div style="background: #0a0a1a; border-radius: 10px; padding: 15px; margin-bottom: 12px;">
                        <small style="color: #8b8ba3;">{{__('Held in Escrow')}}</small>
                        <h4 style="color: #FFB800; margin: 5px 0 0;">{{amount_with_currency_symbol($escrowTotal)}}</h4>
                    </div>
                    <div style="background: #0a0a1a; border-radius: 10px; padding: 15px; margin-bottom: 12px;">
                        <small style="color: #8b8ba3;">{{__('Total Released')}}</small>
                        <h4 style="color: #00D4AA; margin: 5px 0 0;">{{amount_with_currency_symbol($releasedTotal)}}</h4>
                    </div>
                    <div style="background: #0a0a1a; border-radius: 10px; padding: 15px;">
                        <small style="color: #8b8ba3;">{{__('Campaign Goal')}}</small>
                        <h4 style="color: #627EEA; margin: 5px 0 0;">{{amount_with_currency_symbol($campaign->amount)}}</h4>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-plus-circle"></i> {{__('Add Milestone')}}</h5>
                    <form action="{{route('admin.milestones.store', $campaign->id)}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label style="color: #8b8ba3;">{{__('Title')}}</label>
                            <input type="text" name="title" class="form-control" required placeholder="{{__('e.g. Hospital Bill Payment')}}">
                        </div>
                        <div class="form-group">
                            <label style="color: #8b8ba3;">{{__('Description')}}</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="{{__('Describe what this milestone is for...')}}"></textarea>
                        </div>
                        <div class="form-group">
                            <label style="color: #8b8ba3;">{{__('Amount')}}</label>
                            <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" placeholder="{{__('Amount to release')}}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> {{__('Create Milestone')}}
                        </button>
                    </form>
                </div>
            </div>

            <a href="{{route('admin.donations.all')}}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-left"></i> {{__('Back to Campaigns')}}
            </a>
        </div>
    </div>
</div>
@endsection
