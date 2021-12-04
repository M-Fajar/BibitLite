@php
    use App\Enums\SchemePayout as SPayout;
    use App\Enums\InterestPeriod as IPeriod;
    use App\Enums\SchemeTermTypes as STType;
    use App\Enums\InterestRateType as IRType;
    use App\Enums\SchemeStatus as SStatus;

    $uid = (!blank($scheme)) ? the_hash($scheme->id) : request()->get('uid');

    if ($type=='new' && has_route('admin.investment.scheme.save')) {
        $form_action = route('admin.investment.scheme.save');
    } else {
        $form_action = route('admin.investment.scheme.update', ['id' => $uid]);
    }
@endphp

<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h5 class="title nk-modal-title">{{ __(':Type Scheme / Plan', ['type' => $type]) }}</h5>
            <form action="{{ $form_action }}" class="form-validate is-alter"{!! ($type!='new') ? ' data-confirm="update"' : '' !!}>
                <div class="row gy-2">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label" for="scheme-name">{{ __('Scheme Name') }}</label>
                            <div class="form-control-wrap">
                                <input type="text" name="name" class="form-control" id="scheme-name" value="{{ data_get($scheme, 'name', '') }}" required>
                            </div>
                            <div class="form-note">{{ __("The name of investment scheme.") }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="shortcode">{{ __('Short Name') }}</label>
                            <div class="form-control-wrap">
                                <input type="text" name="short" class="form-control" id="shortcode" maxlength="2" value="{{ data_get($scheme, 'short', '') }}" required>
                            </div>
                            <div class="form-note">{{ __("The short name for plan.") }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="scheme-desc">{{ __('Scheme Description') }}</label>
                            <div class="form-control-wrap">
                                <input type="text" name="desc" class="form-control" id="scheme-desc" value="{{ data_get($scheme, 'desc', '') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divider md mb-3 stretched"></div>
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="invest-amount">{{ __('Investment Amount') }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-control-wrap">
                                        <input type="text" name="amount" class="form-control" id="invest-amount" min="0.001" data-msg-min="{{ __("More than 0.001") }}" value="{{ data_get($scheme, 'amount') }}" required>
                                    </div>
                                    <div class="form-note">{{ __('Minimum (:currency)', ['currency' => base_currency()]) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="form-control-wrap">
                                        <input type="text" name="maximum" class="form-control" id="invest-maximum" value="{{ data_get($scheme, 'maximum') }}">
                                    </div>
                                    <div class="form-note">{{ __('Maximum (:currency)', ['currency' => base_currency()]) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="interest-rate">{{ __('Interest Rate / Profit') }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-control-wrap">
                                        <input name="rate" type="text" class="form-control" id="interest-rate" value="{{ data_get($scheme, 'rate') }}" required>
                                    </div>
                                    <div class="form-note">{{ __('Amount') }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="form-control-wrap">
                                        <select name="types" class="form-select form-control">
                                            @foreach(get_enums(IRType::class, false) as $term)
                                                <option{{ (data_get($scheme, 'rate_type') == $term) ? ' selected' : '' }} value="{{ $term }}">{{ ucfirst($term) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-note">{{ __('Interest Type') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label" for="interest-period">{{ __('Interest Period') }}</label>
                        <div class="form-control-wrap">
                            <select name="period" class="form-select form-control" id="interest-period">
                                @foreach(get_enums(IPeriod::class, false) as $term)
                                    <option{{ (data_get($scheme, 'calc_period') == $term) ? ' selected' : '' }} value="{{ $term }}">{{ ucfirst($term) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label class="form-label" for="payout-policy">{{ __('Payout Term') }}</label>
                            <div class="form-control-wrap">
                                <select name="payout" class="form-select form-control" id="payout-policy" required>
                                    @foreach(get_enums(SPayout::class, false) as $term)
                                        <option{{ (data_get($scheme, 'payout') == $term) ? ' selected' : '' }}{{ ($term == 'after_matured') ? ' disabled' : '' }} value="{{ $term }}">{{ str_replace('_', ' ', ucfirst($term)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="term-duration">{{ __('Term Duration') }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="text" name="term" class="form-control" id="term-duration" value="{{ data_get($scheme, 'term') }}" required>
                                </div>
                                <div class="col-6">
                                    <select name="duration" class="form-select form-control" required>
                                        @foreach(get_enums(STType::class, false) as $term)
                                            <option @if(data_get($scheme, 'term_type') == $term) selected @endif value="{{ $term }}">{{ ucfirst($term) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider md mb-3 stretched"></div>
                <div class="row gy-3">
                    <div class="col-12">
                        <div class="row gy-1">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="form-control-wrap mb-1">
                                        <div class="custom-control custom-control-labeled custom-switch">
                                            <input name="fixed" type="checkbox" class="custom-control-input" id="is-fixed-amount"{{ (data_get($scheme, 'is_fixed')) ? ' checked=""' : '' }}>
                                            <label class="custom-control-label" for="is-fixed-amount">{{ __('Set as Fixed Type investment.') }}</label>
                                        </div>
                                    </div>
                                    <div class="form-control-wrap mb-1">
                                        <div class="custom-control custom-control-labeled custom-switch">
                                            <input name="capital" type="checkbox" class="custom-control-input" id="capital-return"{{ (data_get($scheme, 'capital', 1)) ? ' checked=""' : '' }}>
                                            <label class="custom-control-label" for="capital-return">{{ __('Return capital at end of the term.') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <div class="custom-control custom-control-labeled custom-switch">
                                            <input name="featured" type="checkbox" class="custom-control-input" id="is-featured"{{ (data_get($scheme, 'featured')) ? ' checked=""' : '' }}>
                                            <label class="custom-control-label" for="is-featured">{{ __('Set as Featured plan.') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($type!='new')
                        <div class="notes mt-4">
                            <ul>
                                <li class="alert-note is-plain text-danger">
                                    <em class="icon ni ni-alert"></em>
                                    <p><strong>{{ __('Notes:') }}</strong> {{ __('Your changes does not affect on old subscription as only affect to new subscription.') }}</p>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="divider md stretched"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="align-center flex-nowrap g-3">
                            <div class="col">
                                <div class="custom-control custom-switch">
                                    <input name="status" id="plan-status" type="checkbox" class="custom-control-input"{{ (data_get($scheme, 'status', SStatus::INACTIVE) == SStatus::ACTIVE) ? ' checked=""' : ''}}>
                                    <label for="plan-status" class="custom-control-label">{{ __('Active') }}</label>
                                </div>
                            </div>
                            <div class="col">
                                <ul class="align-center justify-content-end flex-nowrap gx-4">
                                    <li class="order-last">
                                        @if(data_get($scheme, 'id'))
                                            <input name="id" type="hidden" value="{{ data_get($scheme, 'id') }}">
                                        @endif
                                        <button type="button" class="btn btn-primary m-ivs-save" data-action="update">
                                            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true"></span>
                                            <span>{{ (($type=='new') ? __('Add Scheme') : __('Save Scheme')) }}</span>
                                        </button>
                                    </li>
                                    <li class="d-none d-sm-inline">
                                        <a href="#" data-dismiss="modal" class="link link-danger">{{ __('Cancel') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
