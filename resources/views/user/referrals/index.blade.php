@extends('user.layouts.master')

@section('title', __('Referrals'))

@section('content')
<div class="nk-content-body">
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-head-sub"><span>{{ __('Referrals') }}</span></div>
        <div class="nk-block-between-md g-4">
            <div class="nk-block-head-content">
                <h2 class="nk-block-title fw-normal">{{ __('Referral Activity') }}</h2>
                <div class="nk-block-des">
                    <p>{{ __("See who you've referred and statistic of your referrals.") }}</p>
                </div>
            </div>
            {{-- <div class="nk-block-head-content d-none d-sm-inline-block">
                <ul class="nk-block-tools gx-3">
                    <li><a href="#" class="btn btn-primary"><span>{{ __('Invite friends') }}</span></a></li>
                </ul>
            </div> --}}
        </div>
    </div>
    
    {!! Panel::profile_alerts() !!}

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <h5 class="nk-block-title">{{ __('Referral Commissions') }}</h5>
        </div>
        <div class="card card-bordered">
            <table class="nk-plan-tnx table">
                <thead class="thead-light">
                <tr>
                    <th class="tb-col-type w-50"><span class="overline-title">{{ __('Details') }}</span></th>
                    <th class="tb-col-date tb-col-md"><span class="overline-title">{{ __('Date') }}</span></th>
                    <th class="tb-col-status tb-col-sm"><span class="overline-title">{{ __('Status') }}</span></th>
                    <th class="tb-col-amount tb-col-end"><span class="overline-title">{{ __('Earning') }}</span></th>
                </tr>
                </thead>
                <tbody>

                @foreach($transactions as $tranx)
                <tr>
                    <td class="tb-col-type w-50"><span class="sub-text">{{ $tranx->description }}</span></td>
                    <td class="tb-col-date tb-col-md">
                        <span class="sub-text">{{ show_date(data_get($tranx, 'created_at'), true) }}</span>
                    </td>
                    <td class="tb-col-status tb-col-sm">
                        <span class="sub-text">{{ ucfirst(__(tnx_status_switch($tranx->status))) }} {!! ($tranx->completed_at) ? '<em class="icon ni ni-info nk-tooltip text-soft" title="'. __("At :time", ['time' => show_date($tranx->completed_at, true) ]). '"></em> ' : '' !!}</span>
                    </td>
                    <td class="tb-col-amount tb-col-end"><span>{{ amount_z($tranx->amount, base_currency(), ['dp' => 'calc']) }}</span></td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <div class="nk-block">
        {!! Panel::referral('invite-card') !!}
    </div>

    {!! Panel::cards('support') !!}
    
</div>
@endsection
