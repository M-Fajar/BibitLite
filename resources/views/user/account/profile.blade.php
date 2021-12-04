@extends('user.layouts.master')

@section('title', __('Profile Info'))

@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h2 class="nk-block-title fw-normal">{{ __('Profile Info') }}</h2>
                <div class="nk-block-des">
                    <p>{{ __('You have full control to manage your own account setting.') }}</p>
                </div>
            </div>
        </div>
        <ul class="nk-nav nav nav-tabs">
            @include('user.account.nav-tab')
        </ul>
        <div class="nk-block">
            @if(session('email-sent'))
                <div class="alert alert-pro alert-success alert-dismissible alert-icon">
                    <em class="icon ni ni-check-circle"></em> 
                    <strong>{{ session('email-sent') }}</strong>
                    <button class="close" data-dismiss="alert"></button>
                </div>
            @endif
            {!! Panel::profile_alerts('verify_email', ['class' => 'alert-plain', 'type' => 'info', 'link_modal' => '#change-unverified-email', 'link_modal_verify' => '#send-verification-link']) !!}
            {!! Panel::profile_alerts('profile', ['class' => 'alert-plain', 'type' => 'primary', 'link_modal' => '#profile-edit']) !!}
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title">{{ __('Personal Information') }}</h5>
                    <div class="nk-block-des">
                        <p>{{ __('Basic info, like your name and address, that you use on our platform.') }}</p>
                    </div>
                </div>
            </div>
            {{-- .nk-block-head --}}
            <div class="card card-bordered">
                <div class="nk-data data-list">
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                        <div class="data-col">
                            <span class="data-label">{{ __('Full Name') }}</span>
                            <span class="data-value">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                        <div class="data-col">
                            <span class="data-label">{{ __('Display Name') }}</span>
                            <span class="data-value">{{ $metas['profile_display_name'] ?? '' }}</span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item">
                        <div class="data-col">
                            <span class="data-label">{{ __('Email') }}</span>
                            <span class="data-value">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                        <div class="data-col">
                            <span class="data-label">{{ __('Phone Number') }}</span>
                            <span class="data-value{{ (empty($metas['profile_phone'])) ? ' text-soft font-italic' : '' }}">
                                {{ $metas['profile_phone'] ?? __('Not add yet') }}
                            </span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                        <div class="data-col">
                            <span class="data-label">{{ __('Telegram') }}</span>
                            <span class="data-value{{ (empty($metas['profile_telegram'])) ? ' text-soft font-italic' : '' }}">
                                {{ empty($metas['profile_telegram']) ? __('Not added yet') : "@".$metas['profile_telegram'] }}
                            </span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                        <div class="data-col">
                            <span class="data-label">{{ __('Date of Birth') }}</span>
                            <span class="data-value{{ (empty($metas['profile_dob'])) ? ' text-soft font-italic' : '' }}">
                                {{ !empty(data_get($metas, 'profile_dob')) ? show_date($metas['profile_dob'], false, false) : __('Not add yet') }}
                            </span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit" data-tab-target="#address">
                        <div class="data-col">
                            <span class="data-label">{{ __('Country') }}</span>
                            <span class="data-value{{ (empty($metas['profile_country'])) ? ' text-soft font-italic' : '' }}">
                                {{ empty($metas['profile_country']) ? __('Not added yet') : $metas['profile_country'] }}
                            </span>
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                    <div class="data-item" data-toggle="modal" data-target="#profile-edit" data-tab-target="#address">
                        <div class="data-col">
                            <span class="data-label">{{ __('Address') }}</span>
                            @if(!empty($metas['profile_address_line_1']) || !empty($metas['profile_address_line_2']) || !empty($metas['profile_state']))
                            <span class="data-value">
                                @if (!empty($metas['profile_address_line_1']) || !empty($metas['profile_address_line_2']))
                                    {{ $metas['profile_address_line_1'] . (($metas['profile_address_line_1'] && $metas['profile_address_line_2']) ? ', ' : '') . $metas['profile_address_line_2'] }}
                                @endif
                                @if (!empty($metas['profile_state']))
                                    <br>{{ $metas['profile_state'] }}
                                @endif
                            </span>
                            @else
                            <span class="data-value text-soft font-italic">
                                {{ __('Not add yet') }}
                            </span>
                            @endif
                        </div>
                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    {{-- Profile Edit Modal --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="profile-edit">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <a href="javascript:void(0)" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
                <div class="modal-body modal-body-lg">
                    <h4 class="title">{{ __('Update Profile') }}</h4>
                    <ul class="nk-nav nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#personal">{{ __('Personal') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#address">{{ __('Address') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="personal">
                            <form action="{{ route('account.profile.personal') }}" method="POST" class="form-validate is-alter form-profile" id="profile-personal-form">
                                @csrf
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="full-name">{{ __('Full Name') }}  <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="name" value="{{ auth()->user()->name }}" class="form-control form-control-lg" id="full-name" placeholder="{{ __('Enter Full name') }}" required maxlength="190">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="display-name">{{ __('Nice Name') }} <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_display_name" value="{{ $metas['profile_display_name'] ?? '' }}" class="form-control form-control-lg" id="display-name" placeholder="{{ __('Enter display name') }}" required maxlength="190">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="phone-no">{{ __('Phone Number') }}</label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_phone" value="{{ $metas['profile_phone'] ?? '' }}" class="form-control form-control-lg" id="phone-no" placeholder="{{ __('Phone Number') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="telegram">{{ __('Telegram') }}</label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_telegram" value="{{ $metas['profile_telegram'] ?? '' }}" class="form-control form-control-lg" id="telegram" placeholder="{{ __('Telegram') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="birth-day">{{ __('Date of Birth') }}</label>
                                            <input type="text" name="profile_dob" value="{{ $metas['profile_dob'] ?? '' }}" data-date-start-date="-85y" data-date-end-date="-12y" class="form-control form-control-lg date-picker-alt" id="birth-day" placeholder="{{ __('Enter your date of birth') }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="profile_display_full_name" class="custom-control-input" id="display-full-name"{{ (data_get($metas, 'profile_display_full_name') == 'on') ? ' checked' : '' }}>
                                            <label class="custom-control-label" for="display-full-name">{{ __('Use full name to display') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2 pt-2">
                                            <li>
                                                <button type="button" id="update-profile" class="btn btn-lg btn-primary">{{ __('Update Profile') }}</button>
                                            </li>
                                            <li>
                                                <a href="#" data-dismiss="modal" class="link link-light">{{ __('Cancel') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="address">
                            <form action="{{ route('account.profile.address') }}" method="POST" class="form-validate is-alter form-profile" id="profile-address-form">
                                @csrf
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="address-l1">{{ __('Address Line 1') }} <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_address_line_1" class="form-control form-control-lg" id="address-l1" value="{{ $metas['profile_address_line_1'] ?? '' }}" required maxlength="190">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="address-l2">{{ __('Address Line 2') }}</label>
                                            </div>
                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_address_line_2" class="form-control form-control-lg" id="address-l2" value="{{ $metas['profile_address_line_2'] ?? '' }}" maxlength="190">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="address-st">{{ __('State') }} <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="form-control-wrap">
                                                <input type="text" name="profile_state" class="form-control form-control-lg" id="address-st" value="{{ $metas['profile_state'] ?? '' }}" required maxlength="190">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="address-county">{{ __('Country') }} <span class="text-danger">*</span></label>
                                            <select name="profile_country" class="form-select" id="address-county" data-ui="lg" data-search="on">
                                                @foreach(config('countries') as $item)
                                                    <option value="{{ $item }}"{{ (isset($metas['profile_country']) && $metas['profile_country'] == $item) ? ' selected' : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2 pt-2">
                                            <li>
                                                <button type="button" id="update-address" class="btn btn-lg btn-primary">{{ __('Update Address') }}</button>
                                            </li>
                                            <li>
                                                <a href="#" data-dismiss="modal" class="link link-light">{{ __('Cancel') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Unverified Email Modal --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="change-unverified-email">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
                <div class="modal-body modal-body-md">
                    <h5 class="title">{{ __('Enter Your Valid Email Address') }}</h5>
                    <form action="{{ route('account.profile.update-unverified-email') }}" method="POST" class="form-validate is-alter mt-4 form-profile" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="email-address">{{ __('Current Email Address') }}</label>
                            <div class="form-control-wrap">
                                <input type="email" class="form-control form-control-lg" id="email-address" readonly value="{{ auth()->user()->email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new-unverified-email-address">{{ __('New Email Address') }}  <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="email" id="new-unverified-email-address" autocomplete="new-email" name="user_new_unverified_email" class="form-control form-control-lg"  placeholder="{{ __('Enter Email Address') }}" required maxlength="190">
                            </div>
                        </div>
                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                            <li>
                                <button type="submit" id="update-unverified-email" class="btn btn-md btn-primary">{{ __('Send Verification Email') }}</button>
                            </li>
                        </ul>
                        <div class="notes mt-gs">
                            <ul>
                                <li class="alert-note is-plain text-danger">
                                    <em class="icon ni ni-alert-circle"></em>
                                    <p>{{ __("Wether you verify your email or not, from next login you have to use your new email address.") }}</p>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Send Verification Link for Unverified Email --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="send-verification-link">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
                <div class="modal-body modal-body-md">
                    <h5 class="title">{{ __('Resend Email Verification Link') }}</h5>
                    <form action="{{ route('account.profile.verify-unverified-email', auth()->user()) }}" method="POST" class="form-validate is-alter mt-4">
                        @csrf
                        <div class="form-group">
                            <p class="text-dark fs-16px"><strong>{{ __('Are you sure to proceed with email verification link for your exisiting email?') }}</strong></p>
                        </div>
                        <div class="form-group">
                            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <button type="submit" class="btn btn-md btn-primary">{{ __('Send Verification Email') }}</button>
                                </li>
                            </ul>
                        </div>
                        <div class="notes mt-gs">
                            <ul>
                                <li class="alert-note is-plain text-danger">
                                    <em class="icon ni ni-alert-circle"></em>
                                    <p>{{ __("After verification, from next login you have to use your new verified email address.") }}</p>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script type="text/javascript">
    const profileSetting = "{{ route('account.settings.save') }}";
</script>
@endpush