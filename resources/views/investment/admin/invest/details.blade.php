@extends('admin.layouts.master')
@section('title', __('Investment Details'))

@php
use App\Enums\InvestmentStatus;
use App\Enums\InterestRateType;
use App\Enums\RefundType;

$currency = base_currency();

@endphp

@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head">
            <div class="nk-block-between-md g-3">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">
                        {!! __('Investment / :summary', ['summary' => '<span class="text-primary small">'.data_get($invest, 'summary_title').'</span>' ]) !!}
                    </h3>
                    <div class="nk-block-des text-soft">
                        <ul class="list-inline">
                            <li>{{ __("Invested By:") }} <span class="text-base">{{ the_uid($invest->user->id) }} ({{ str_protect($invest->user->email) }})</span></li>
                            <li>{{ __("Invest ID:") }} <span class="text-base">{{ the_inv($invest->ivx) }}</span></li>
                            <li><span class="badge{{ the_state($invest->status, ['prefix' => 'badge']) }}">{{ ucfirst($invest->status) }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <ul class="nk-block-tools gx-1">
                        @if($invest->status != InvestmentStatus::CANCELLED && $invest->status != InvestmentStatus::COMPLETED)
                            @if($invest->status == InvestmentStatus::ACTIVE)
                            <li class="order-md-last"><a href="javascript:void(0)" class="btn btn-danger" data-toggle="modal" data-target="#cancel-investment"><em class="icon ni ni-cross"></em> <span>{{ __('Cancel') }}</span> </a></li>
                            @else
                            <li class="order-md-last"><a href="javascript:void(0)" class="btn btn-danger m-ivs-cancel" data-confirm="yes" data-reload="yes" data-action="cancelled" data-uid="{{ the_hash($invest->id) }}"><em class="icon ni ni-cross"></em> <span>{{ __('Cancel') }}</span> </a></li>
                            @endif
                        @endif

                        @if($invest->status == InvestmentStatus::PENDING)
                            <li class="order-md-last"><a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#approve-investment"><em class="icon ni ni-cross"></em> <span>{{ __('Approve') }}</span> </a></li>
                        @endif

                        @if($invest->status == InvestmentStatus::ACTIVE)
                        <li><a href="{{ route('admin.investment.details', ['id' => the_hash($invest->id)]) }}" class="btn btn-icon btn-white btn-light"><em class="icon ni ni-reload"></em></a></li>
                        @endif
                        <li class="order-md-first ml-auto">
                            <a href="{{ (url()->previous() && (url()->previous() != url()->current())) ? url()->previous() : route('admin.investment.list') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>{{ __('Back') }}</span></a>
                            <a href="{{ (url()->previous() && (url()->previous() != url()->current())) ? url()->previous() : route('admin.investment.list') }}" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none back-me"><em class="icon ni ni-arrow-left"></em></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row gx-gs gy-3">
                <div class="col-12">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row gy-gs">
                                <div class="col-md-6 col-xl col-xxl-8">
                                    <div class="nk-wgacc">
                                        <div class="nk-wgacc-group flex-lg-nowrap gx-4">
                                            <div class="nk-wgacc-sub">
                                                <div class="nk-wgacc-amount">
                                                    <div class="number">{{ amount_z($invest->amount, $currency) }} <span class="fw-normal text-base">{{ $currency }}</span></div>
                                                </div>
                                                <div class="nk-wgacc-subtitle">{{ __('Invested') }}</div>
                                            </div>
                                            <div class="nk-wgacc-sub">
                                                <span class="nk-wgacc-sign text-soft"><em class="icon ni ni-plus"></em></span>
                                                <div class="nk-wgacc-amount">
                                                    <div class="number">{{ amount_z($invest->profit, $currency) }}</div>
                                                </div>
                                                <div class="nk-wgacc-subtitle">{{ __('Profit') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl col-xxl-4">
                                    <div class="nk-wgacc pl-md-3">
                                        <div class="nk-wgacc-group flex-lg-nowrap gx-4">
                                            <div class="nk-wgacc-sub">
                                                <div class="nk-wgacc-amount">
                                                    <div class="number">
                                                        {{ amount_z($invest->received, $currency) }} <span class="fw-normal text-base">{{ $currency }}</span>
                                                        @if($invest->pending_amount > 0)
                                                            <em class="icon ni ni-info nk-tooltip" title="{{ __('Remain :amount', ['amount' => money($invest->pending_amount, $currency)]) }}"></em>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="nk-wgacc-subtitle">
                                                    {{ __('Paid Amount') }} {{ (data_get($invest, 'scheme.capital', 0)==0) ? __("(inc. cap)") : (($invest->status != InvestmentStatus::COMPLETED) ? __("(exc. cap)") : '') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nk-plan-details">
                            <ul class="nk-wgacc-list">
                                <li>
                                    <div class="sub-text">{{ __('Term duration') }}</div>
                                    <div class="lead-text">{{ data_get($invest, 'term') }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Term compute') }}</div>
                                    <div class="lead-text">{{ ucfirst(__(data_get($invest, 'term_calc'))) }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Term start at') }}</div>
                                    <div class="lead-text">{{ show_date(data_get($invest, 'term_start'), true) ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Term end at') }}</div>
                                    <div class="lead-text">{{ show_date(data_get($invest, 'term_end'), true) ?? 'N/A' }}</div>
                                </li>
                            </ul>
                            <ul class="nk-wgacc-list">
                                <li>
                                    <div class="sub-text">{{ __('Interest (:frequency)', ['frequency' => __(data_get($invest, 'scheme.calc_period'))]) }}</div>
                                    <div class="lead-text">{{ data_get($invest, 'rate_text') }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Total net profit') }}</div>
                                    <div class="lead-text"><span class="currency">{{ $currency }}</span> {{ amount_z(data_get($invest, 'profit'), $currency) }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __(':Calc profit (:capital)', ['calc' => __(data_get($invest, 'scheme.calc_period')), 'capital' => (data_get($invest, 'scheme.capital', 0)==0) ? __("inc. cap") : __("(exc. cap)") ]) }}</div>
                                    <div class="lead-text"><span class="currency">{{ $currency }}</span> {{ amount_z(data_get($invest, 'calc_profit'), $currency) }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Adjust profit') }}</div>
                                    <div class="lead-text">
                                        {{ __(":count / :total times", ['count' => data_get($invest, 'term_count'), 'total' => data_get($invest, 'term_total')]) }}
                                    </div>
                                </li>
                            </ul>
                            <ul class="nk-wgacc-list">
                                <li>
                                    <div class="sub-text">{{ __('Payment source') }}</div>
                                    <div class="lead-text">{{ (data_get($invest, 'payment_source')) ? w2n(data_get($invest, 'payment_source')) : __("N/A") }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Payment reference') }}</div>
                                    <div class="lead-text">{{ data_get($invest, 'reference', __("N/A")) }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Payment date') }}</div>
                                    <div class="lead-text">{{ data_get($invest, 'payment_date', __("N/A")) }}</div>
                                </li>
                                <li>
                                    <div class="sub-text">{{ __('Paid amount') }}</div>
                                    <div class="lead-text">
                                        @if($invest->status!=InvestmentStatus::PENDING)
                                        <span class="currency">{{ $currency  }}</span> {{ amount_z(data_get($invest, 'paid_amount'), $currency) }}
                                        @else
                                        {{ __("N/A") }}
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-bordered card-full">
                        <div class="card-inner pb-2">
                            <h6 class="overline-title text-secondary fs-13px">{{ __("Plan Details") }}</h6>
                        </div>
                        <ul class="nk-wgacc-list w-100">
                            <li>
                                <div class="sub-text">{{ __('Scheme / Plan') }}</div>
                                <div class="lead-text"> 
                                    {{ data_get($invest->scheme, 'name') . ' (' . data_get($invest->scheme, 'short') . ')' }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Payout term') }}</div>
                                <div class="lead-text"> 
                                    {{ str_replace('_', ' ', ucfirst(data_get($invest->scheme, 'payout'))) }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Interest rate') }} ({{ ucfirst(data_get($invest->scheme, 'calc_period')) }})</div>
                                <div class="lead-text">
                                    {{ data_get($invest->scheme, 'rate') . ' (' . ucfirst(data_get($invest->scheme, 'rate_type')) . ')' }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Term duration') }}</div>
                                <div class="lead-text">
                                    {{ data_get($invest->scheme, 'term') . ' ' . ucfirst(data_get($invest->scheme, 'term_type')) }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Capital return') }}</div>
                                <div class="lead-text">
                                    {{ (data_get($invest->scheme, 'capital', 0)==1) ? __("End of term") : __("Each term") }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Fixed investment') }}</div>
                                <div class="lead-text">
                                    {{ (data_get($invest->scheme, 'is_fixed', 0)==1) ? __("Yes") : __("No") }}
                                </div>
                            </li>
                        </ul>
                    </div>  
                </div>
                <div class="col-lg-4">
                    <div class="card card-bordered card-full">
                        <div class="card-inner pb-2">
                            <h6 class="overline-title text-secondary fs-13px">{{ __("Action Details") }}</h6>
                        </div>
                        <ul class="nk-wgacc-list w-100">
                            <li>
                                <div class="sub-text">{{ __('Ordered date') }}</div>
                                <div class="lead-text"> 
                                    {{ show_date(data_get($invest, 'order_at'), true) }}
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Ordered by') }}</div>
                                <div class="lead-text"> 
                                    @if(data_get($invest, 'order_by'))
                                        <em class="icon ni ni-info text-soft small nk-tooltip" title="{{ str_protect(get_user(data_get($invest, 'order_by'))->email) }}"></em> 
                                        {{ get_user(data_get($invest, 'order_by'))->name }}
                                    @else 
                                        {{ __("Unknown") }}
                                    @endif
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Approved date') }}</div>
                                <div class="lead-text">
                                    @if(data_get($invest, 'approve_at'))
                                        {{ show_date(data_get($invest, 'approve_at'), true) }}
                                    @else
                                        <em class="text-soft">{{ __("Not yet") }}</em>
                                    @endif
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Approved by') }}</div>
                                <div class="lead-text"> 
                                    @if(data_get($invest, 'approve_by'))
                                        <em class="icon ni ni-info text-soft small nk-tooltip" title="{{ str_protect(get_user(data_get($invest, 'approve_by'))->email) }}"></em> 
                                        {{ get_user(data_get($invest, 'approve_by'))->name }}
                                    @else 
                                        {{ __("N/A") }}
                                    @endif
                                </div>
                            </li>
                            <li>
                                <div class="sub-text">{{ __('Completed date') }}</div>
                                <div class="lead-text">
                                    @if(data_get($invest, 'completed_at'))
                                        {{ show_date(data_get($invest, 'completed_at'), true) }}
                                    @else
                                        @if($invest->status==InvestmentStatus::PENDING)
                                        <em class="text-soft">{{ __("Not started") }}</em>
                                        @else
                                        <em class="text-soft">{{ __("Not completed") }}</em>
                                        @endif
                                    @endif
                                </div>
                            </li>
                            @if($invest->status==InvestmentStatus::CANCELLED)
                            <li>
                                <div class="sub-text">{{ __('Cancelation date') }}</div>
                                <div class="lead-text">
                                    @if(data_get($invest, 'cancelled_by'))
                                    <em class="icon ni ni-info text-soft small nk-tooltip" title="{{ __("Cancelled by :name", ['name' => get_user(data_get($invest, 'cancelled_by'))->name ]) }}"></em>
                                    @endif
                                    {{ show_date(data_get($invest, 'cancelled_at'), true) }}
                                </div>
                            </li>
                            @else
                            <li>
                                <div class="sub-text">{{ __('Last updated') }}</div>
                                <div class="lead-text">
                                    {{ show_date(data_get($invest, 'updated_at'), true) }}
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-bordered card-full">
                        <div class="card-inner pb-2">
                            <h6 class="overline-title text-secondary fs-13px">{{ __("Additional") }}</h6>
                        </div>
                        <ul class="nk-wgacc-list w-100">
                            <li class="flex-column align-start w-100">
                                <div class="sub-text">{{ __('Desc') }}</div>
                                <div class="lead-text"> 
                                    {{ (data_get($invest, 'desc')) ? data_get($invest, 'desc') : __("N/A") }}
                                </div>
                            </li>
                            <li class="flex-column align-start w-100">
                                <div class="sub-text">{{ __('Notes') }}</div>
                                <div class="lead-text"> 
                                    {!! (data_get($invest, 'note')) ? auto_p(data_get($invest, 'note')) : __("N/A") !!}
                                </div>
                            </li>
                            <li class="flex-column align-start w-100">
                                <div class="sub-text">{{ __('Remarks (Admin Only)') }}</div>
                                <div class="lead-text"> 
                                    {!! (data_get($invest, 'remarks')) ? auto_p(data_get($invest, 'remarks')) : __("N/A") !!}
                                </div>
                            </li>
                            @if(data_get($invest, 'cancelled_by')==$invest->user_id)
                            <li class="flex-column align-start w-100">
                                <div class="sub-text text-danger pt-2">{{ __('Caution:') }} {{ __("The investment cancelled by investor.") }}</div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <h5 class="nk-block-title">{{ __('Graph View') }}</h5>
            </div>
            <div class="row g-gs">
                <div class="col-lg-5">
                    <div class="card card-bordered h-100">
                        <div class="card-inner justify-center text-center h-100">
                            <div class="nk-wgpg">
                                <div class="nk-wgpg-head">
                                    <h5 class="nk-wgpg-title">{{ __('Overview') }}</h5>
                                </div>
                                <div class="nk-wgpg-graph">
                                    <input type="text" class="knob-half" value="{{ data_get($invest, 'progress') }}" data-fgColor="#6576ff" data-bgColor="#d9e5f7" data-thickness=".06" data-width="300" data-height="155" data-displayInput="false">
                                    <div class="nk-wgpg-graph-result">
                                        <div class="text-lead">{{ data_get($invest, 'progress') }}%</div>
                                        <div class="text-sub">{{ data_get($invest, 'rate_text') }} / {{ strtolower(data_get($invest, 'period_text')) }}</div>
                                    </div>
                                    <div class="nk-wgpg-graph-minmax"><span>{{ money(0.0, $currency) }}</span><span>{{ money(data_get($invest, 'total'), $currency) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-sm-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner justify-center text-center h-100">
                            <div class="nk-wgpg">
                                <div class="nk-wgpg-head">
                                    <h5 class="nk-wgpg-title">{{ __('Net Profit') }}</h5>
                                    <div class="nk-wgpg-subtitle">{!! __('Earn so far :amount', ['amount' => '<strong>' . money(data_get($invest, 'received'), $currency) . '</strong>']) !!}</div>
                                </div>
                                <div class="nk-wgpg-graph sm">
                                    <input type="text" class="knob-half" value="{{ data_get($invest, 'progress') }}" data-fgColor="#33d895" data-bgColor="#d9e5f7" data-thickness=".07" data-width="240" data-height="125" data-displayInput="false">
                                    <div class="nk-wgpg-graph-result">
                                        <div class="text-lead sm">{{ str_replace($currency, '', data_get($invest, 'rate_text')) }}</div>
                                        <div class="text-sub">{{ __(':calc profit', ['calc' => strtolower(data_get($invest, 'scheme.calc_period'))]) }}</div>
                                    </div>
                                    <div class="nk-wgpg-graph-minmax"><span>{{ money(0.0, $currency) }}</span><span>{{ money(data_get($invest, 'profit'), $currency) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-sm-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner justify-center text-center h-100">
                            <div class="nk-wgpg">
                                <div class="nk-wgpg-head">
                                    <h5 class="nk-wgpg-title">{{ __('Remain') }}</h5>
                                    <div class="nk-wgpg-subtitle">{!! __('Adjusted so far :count', ['count' => '<strong>' . data_get($invest, 'term_count') . ' '.__('times').'</strong>']) !!}</div>
                                </div>
                                <div class="nk-wgpg-graph sm">
                                    <input type="text" class="knob-half" value="{{ data_get($invest, 'progress') }}" data-fgColor="#816bff" data-bgColor="#d9e5f7" data-thickness=".07" data-width="240" data-height="125" data-displayInput="false">
                                    <div class="nk-wgpg-graph-result">
                                        <div class="text-lead sm">{{ data_get($invest, 'remaining_term') }}</div>
                                        <div class="text-sub">{{ __('remain to adjust') }}</div>
                                    </div>
                                    <div class="nk-wgpg-graph-minmax"><span>{{ __('0 Time') }}</span><span>{{ __(':count Times', ['count' => data_get($invest, 'term_total')]) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!blank(data_get($invest, 'profits', [])))
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <h5 class="nk-block-title">{{ __('Transactions') }}</h5>
            </div>
            <div class="card card-bordered">
                <table class="nk-plan-tnx table">
                    <thead class="thead-light">
                    <tr>
                        <th class="tb-col-type w-70"><span class="overline-title">{{ __('Details') }}</span></th>
                        <th class="tb-col-date"><span class="overline-title">{{ __('Date & Time') }}</span></th>
                        <th class="tb-col-amount tb-col-end"><span class="overline-title">{{ __('Amount') }}</span></th>
                        <th class="tb-col-paid tb-col-end" style="width: 20px"><em class="icon ni ni-info nk-tooltip small text-soft" title="{{ __("The profit transfered into account balance or not.") }}"></em></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td class="tb-col-type"><span class="sub-text">{{ __("Investment") }}</span></td>
                        <td class="tb-col-date">
                            <span class="sub-text">{{ show_date(data_get($invest, 'order_at'), true) }}</span>
                        </td>
                        <td class="tb-col-amount tb-col-end"><span class="lead-text text-danger">- {{ amount_z(data_get($invest, 'amount'), $currency) }}</span></td>
                        <td class="tb-col-paid tb-col-end"><span class="sub-text"><em class="icon ni ni-info nk-tooltip text-soft" title="{{ __("Source: :account", ['account' => w2n(data_get($invest, 'payment_source')) ]) }}"></em></span></td>
                    </tr>

                    @foreach(data_get($invest, 'profits') as $profit)
                    <tr>
                        <td class="tb-col-type"><span class="sub-text">{{ __("Profit Earn - :rate", ['rate' => (($profit->type=='fixed') ? $profit->rate . ' '.$currency . ' ('.$profit->type.')' : $profit->rate . '%')]) }}</span></td>
                        <td class="tb-col-date">
                            <span class="sub-text">{{ show_date(data_get($profit, 'calc_at'), true) }}</span>
                        </td>
                        <td class="tb-col-amount tb-col-end"><span class="lead-text">+ {{ amount_z($profit->amount, $currency) }}</span></td>
                        <td class="tb-col-paid tb-col-end">
                            <span class="sub-text">{!! ($profit->payout) ? '<em class="icon ni ni-info nk-tooltip text-soft" title="'. __("Batch #:id", ['id' => $profit->payout]). '"></em> ' : '' !!}</span>
                        </td>
                    </tr>
                    @endforeach

                    @if(data_get($invest, 'scheme.capital') && $invest->status==InvestmentStatus::COMPLETED)
                    <tr>
                        <td class="tb-col-type"><span class="sub-text">{{ __("Captial Return") }}</span></td>
                        <td class="tb-col-date">
                            <span class="sub-text">{{ show_date(data_get($invest, 'updated_at'), true) }}</span>
                        </td>
                        <td class="tb-col-amount tb-col-end"><span class="lead-text">+ {{ amount_z(data_get($invest, 'amount'), $currency) }}</span></td>
                        <td class="tb-col-paid tb-col-end"><span class="sub-text"></span></td>
                    </tr>
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('modal')
@if($invest->status == InvestmentStatus::PENDING)
<div class="modal fade" tabindex="-1" role="dialog" id="approve-investment">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-md">
                <h5 class="title nk-modal-title">{{ __("Approved the Investment") }}</h5>
                <form action="{{ route('admin.investment.plan.approve', ['id' => the_hash($invest->id)]) }}" method="POST" data-confirm="approved" class="form-validate is-alter">
                    <div class="form-group">
                        <label class="form-label" for="note">{{ __('Note') }}</label>
                        <div class="form-control-wrap">
                            <input type="text" name="note" class="form-control form-control-lg" id="note" placeholder="{{ __('Enter a note for user') }}" maxlength="190">
                        </div>
                        <div class="form-note">{{ __('The note will display to user from transaction details.') }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="remarks">{{ __('Remarks') }}</label>
                        <div class="form-control-wrap">
                            <input type="text" name="remarks" class="form-control form-control-lg" id="remarks" placeholder="{{ __('Enter a remarks note for admin') }}" maxlength="190">
                        </div>
                        <div class="form-note">{{ __('The remarks note help to reminder. Only administrator can read from transaction details.') }}</div>
                    </div>
                    <ul class="align-center flex-wrap flex-sm-nowrap gx-1 gy-2">
                        <li>
                            <button type="button" class="btn btn-lg btn-primary m-ivs-approve">{{ __('Approve') }}</button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-lg btn-white" data-dismiss="modal" data-target="#approve-investment">{{ __('Dismiss') }}</button>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@if($invest->status == InvestmentStatus::ACTIVE)
<div class="modal fade" tabindex="-1" role="dialog" id="cancel-investment">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-md">
                <h5 class="title nk-modal-title">{{ __("Cancelled the Investment") }}</h5>
                <p class="text-danger"><strong>{{ __("Please confirm that you want to cancel this ACTIVE plan.") }}</strong></p>
                <form action="{{ route('admin.investment.plan.cancel', ['id' => the_hash($invest->id)]) }}" method="POST" data-confirm="cancelled" class="form-validate is-alter">
                    <div class="form-group">
                        <label class="form-label" for="cancel-note">{{ __('Investment Return Method') }}</label>
                        <div class="form-control-wrap">
                            <input type="hidden" name="reload" value="true">
                            <select class="form-select" name="cancel-method">
                                <option value="{{ RefundType::TOTAL }}">{{ __("Return :Type Amount", ['type' => RefundType::TOTAL]) }}</option>
                                <option value="{{ RefundType::PARTIAL }}">{{ __("Return :Type Amount", ['type' => RefundType::PARTIAL]) }}</option>
                            </select>
                        </div>
                        <div class="form-note">{{ __('How do you want to return the investment amount.') }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cancel-note">{{ __('Cancelation Note') }}</label>
                        <div class="form-control-wrap">
                            <input type="text" name="note" class="form-control" id="cancel-note" placeholder="{{ __('Enter a note for user') }}" maxlength="190">
                        </div>
                        <div class="form-note">{{ __('The note will display to user from transaction details.') }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cancel-remarks">{{ __('Remarks') }}</label>
                        <div class="form-control-wrap">
                            <input type="text" name="remarks" class="form-control" id="cancel-remarks" placeholder="{{ __('Enter a remarks note for admin') }}" maxlength="190">
                        </div>
                        <div class="form-note">{{ __('The remarks note help to reminder. Only administrator can read from transaction details.') }}</div>
                    </div>
                    <ul class="align-center flex-wrap flex-sm-nowrap gx-1 gy-2">
                        <li>
                            <button type="button" class="btn btn-lg btn-danger m-ivs-cancel-plan">{{ __('Cancel the Plan') }}</button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-lg btn-white" data-dismiss="modal" data-target="#cancel-investment">{{ __('Dismiss') }}</button>
                        </li>
                    </ul>
                </form>
                <div class="divider stretched"></div>
                <div class="notes">
                    <ul>
                        <li class="alert-note is-plain">
                            <em class="icon ni ni-info"></em>
                            <p>{{ __(":Type Amount: The total invested amount will return to investor account.", ['type' => RefundType::TOTAL]) }}</p>
                        </li>
                        <li class="alert-note is-plain">
                            <em class="icon ni ni-info"></em>
                            <p>{{ __(":Type Amount: The adjusted profit will deduct from the amount invested and return amount partially to investor account.", ['type' => RefundType::PARTIAL]) }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endpush

@push('scripts')
<script type="text/javascript">
    const routes = {
            cancelled: "{{ route( 'admin.investment.plan.cancel', ['id' => the_hash($invest->id)] ) }}"
        },
        msgs = {
            approved: {
                title: "{{ __('Approved Investment?') }}",
                btn: {cancel: "{{ __('Cancel') }}", confirm: "{{ __('Yes, Procced') }}"},
                context: "{!! __("Please confirm that you want to procced the request and start the investment.") !!}",
                custom: "success", type: "info"
            },
            cancelled: {
                title: "{{ __('Cancel Investment?') }}",
                btn: {cancel: "{{ __('No') }}", confirm: "{{ __('Yes, Cancel') }}"},
                context: "{!! __("You cannot revert back this action, so please confirm that you want to cancel.") !!}",
                custom: "danger", type: "warning"
            }
        };
</script>
@endpush
