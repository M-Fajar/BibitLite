@section('title', __("Personal Information"))

<div class="nk-block">
    <div class="nk-block-head">
        <h5 class="title">{{ __('Personal Information') }}</h5>
        <p>{{ __('Basic info, like your name and address, that you use on Nio Platform.') }}</p>
    </div>
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Full Name') }}</span>
                <span class="profile-ud-value">{{ $user->name ?? '' }}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Date of Birth') }}</span>
                <span class="profile-ud-value">{!! ($user->meta('profile_dob')) ? show_date($user->meta('profile_dob'), false, false) : '<em class="text-soft small">'.__('Not updated yet').'</em>' !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Display Name') }}</span>
                <span class="profile-ud-value">{!! ($user->meta('profile_display_name')) ? $user->meta('profile_display_name') : '<em class="text-soft small">'.__('Not updated yet').'</em>' !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Mobile Number') }}</span>
                <span class="profile-ud-value">{!! ($user->meta('profile_phone')) ? $user->meta('profile_phone') : '<em class="text-soft small">'.__('Not updated yet').'</em>' !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Email Address') }}</span>
                <span class="profile-ud-value">{{ str_protect($user->email) }}</span>
            </div>
        </div>
    </div>
</div>
<div class="nk-block">
    <div class="nk-block-head nk-block-head-line">
        <h6 class="title overline-title text-base">{{ __('Additional Information') }}</h6>
    </div>
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Join Date') }}</span>
                <span class="profile-ud-value">{{ show_date($user->created_at) }}</span>
            </div>
        </div>
        @if($user->meta('email_verified'))
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Email Verified At') }}</span>
                <span class="profile-ud-value">{{ show_date($user->meta('email_verified'), true) }}</span>
            </div>
        </div>
        @endif
        @if($user->meta('registration_method'))
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Reg Method') }}</span>
                <span class="profile-ud-value">{{ __("By :Method", ['method' => ucfirst($user->meta('registration_method'))]) }}</span>
            </div>
        </div>
        @endif
        @if($user->meta('setting_activity_log'))
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Save Activity Logs') }}</span>
                <span class="profile-ud-value">{{ ($user->meta('setting_unusual_activity')=='on') ? __("Enabled") : __("Disabled") }}</span>
            </div>
        </div>
        @endif
        @if($user->meta('setting_unusual_activity'))
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Email Unusual Activity') }}</span>
                <span class="profile-ud-value">{{ ($user->meta('setting_unusual_activity')=='on') ? __("Enabled") : __("Disabled") }}</span>
            </div>
        </div>
        @endif
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Country') }}</span>
                <span class="profile-ud-value">{!! ($user->meta('profile_country')) ? ucfirst($user->meta('profile_country')) : '<em class="text-soft small">'.__('Not updated yet').'</em>' !!}</span>
            </div>
        </div>
    </div>
</div>
