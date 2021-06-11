@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-4">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    {{ translate('Create an account.') }}
                                </h1>
                            </div>
                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form id="reg-form" class="form-default" role="form" action="{{ route('register') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        {{-- firstname --}}
                                        <div class="form-group">
                                            <input type="text"
                                                class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                value="{{ old('name') }}" placeholder="{{ translate('Full Name') }}"
                                                name="name" required>
                                            @if ($errors->has('name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                            <div class="form-group phone-form-group mb-1">
                                                <input type="tel" id="phone-code"
                                                    class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                                    required value="{{ old('phone') }}" placeholder="" name="phone"
                                                    autocomplete="off">
                                            </div>

                                            {{-- email --}}

                                            <div class="form-group email-form-group mb-1 d-none">
                                                <input type="email"
                                                    class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                                    name="email" required autocomplete="off">
                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group text-right">
                                                <button class="btn btn-link p-0 opacity-50 text-reset" type="button"
                                                    onclick="toggleEmailPhone(this)">{{ translate('Use Email Instead') }}</button>
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <input type="email"
                                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                                    name="email">
                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- password --}}
                                        <div class="form-group">
                                            <input type="password" required
                                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                placeholder="{{ translate('Password') }}" value="{{ old('password') }}" name="password">
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control" value="{{ old('password_confirmation') }}"
                                                placeholder="{{ translate('Confirm Password') }}" required
                                                name="password_confirmation">
                                        </div>
                                        <div class="form-group">
                                            <select name="user_type" id="user_type" class="form-control aiz-selectpicker"
                                                required>

                                                <option value="">{{ translate('Select Account type') }}</option>
                                                @if (old('user_type') == 'customer')
                                                    <option value="customer" selected>{{ translate('Customer') }}</option>
                                                    <option value="seller">{{ translate('Seller') }}</option>
                                                @elseif(old('user_type') == 'seller')
                                                    <option value="customer">{{ translate('Customer') }}</option>
                                                    <option value="seller" selected>{{ translate('Seller') }}</option>
                                                @else
                                                    <option value="customer">{{ translate('Customer') }}</option>
                                                    <option value="seller">{{ translate('Seller') }}</option>
                                                @endif
                                            </select>
                                        </div>

                                        {{-- shop info --}}
                                        <div class="form-group">
                                            <label class="col-md-6 col-from-label">{{ translate('Do you have shop ?') }}
                                            </label>
                                            <label class="aiz-switch aiz-switch-success mb-0 float-right"> <input
                                                    type="checkbox" name="is_shop" id="is_shop" value="1" checked>
                                                <span></span>
                                            </label>
                                        </div>

                                        <div>
                                            <div class="fs-15 fw-600 p-3 border-bottom">
                                                {{ translate('Basic Shop Info') }}
                                            </div>
                                            <div class="pt-3">
                                                <div class="form-group is_shop">
                                                    <input type="text" class="form-control" value="{{ old('shop_name') }}"
                                                        placeholder=" {{ translate('Outlet Name/Shop Name') }}"
                                                        name="shop_name">
                                                </div>
                                                <div class="form-group is_shop">
                                                    <div class="custom-file">
                                                        <label class="custom-file-label">
                                                            <input type="file" class="custom-file-input" name="proof1"
                                                                accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf">
                                                            <span
                                                                class="custom-file-name">{{ translate('Choose Shop image') }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group is_shop">
                                                    <input type="text" class="form-control" value="{{ old('licence_no') }}"
                                                        placeholder="{{ translate('Shop Id/Licence no.') }}"
                                                        name="licence_no">
                                                </div>

                                                <div class="form-group is_shop">
                                                    <div class="custom-file">
                                                        <label class="custom-file-label">
                                                            <input type="file" class="custom-file-input" name="proof2"
                                                                accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf">
                                                            <span
                                                                class="custom-file-name">{{ translate('Choose Shop Proof') }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group is_shop">
                                                    <input type="text" class="form-control" value="{{ old('gst_no') }}"
                                                        placeholder="{{ translate('GST Number') }}" name="gst_no">
                                                </div>

                                                <div class="form-group is_shop">
                                                    <div class="custom-file">
                                                        <label class="custom-file-label">
                                                            <input type="file" class="custom-file-input" name="proof3"
                                                                accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf">
                                                            <span
                                                                class="custom-file-name">{{ translate('Choose GST Proof') }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <select name="business_category[]" id="business_type"
                                                        class="form-control aiz-selectpicker" multiple required>
                                                        <option value="">{{ translate('Select type of business') }}
                                                        </option>
                                                        @foreach ($categories as $category)
                                                            @if (old('business_category'))
                                                                <option value="{{ $category->id }}" {{ in_array($category->id, old('business_category')) ? 'selected' : '' }}>
                                                                @else
                                                                <option value="{{ $category->id }}">
                                                            @endif
                                                            {{ $category->getTranslation('name') }}</option>
                                                            @foreach ($category->childrenCategories as $childCategory)
                                                                @include('categories.child_category', ['child_category' =>
                                                                $childCategory]) @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <select name="city" id="cityList" class="form-control aiz-selectpicker {{ $errors->has('city') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option value="">{{ translate('Select City') }}</option>
                                                        @foreach ($cities as $key => $city)
                                                            <option value="{{ $city->id }}" {{ old('business_category') ? 'selected' : '' }}>
                                                                {{ translate($city->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('city'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('city') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <select name="area" id="mainArea" class="form-control aiz-selectpicker"
                                                        required>
                                                        <option value="">{{ translate('Select Area of locate') }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <textarea class="form-control mb-3 {{ $errors->has('address') ? ' is-invalid' : '' }}"
                                                        placeholder="{{ translate('Full Address') }}" name="address"
                                                        required>{{ old('address') }}</textarea>
                                                    @if ($errors->has('address'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('address') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" class="form-control {{ $errors->has('contact_name') ? ' is-invalid' : '' }}" value="{{ old('contact_name') }}"
                                                        placeholder="{{ translate('Contact Person Name') }}"
                                                        name="contact_name" required>
                                                    @if ($errors->has('contact_name'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('contact_name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="number" class="form-control mb-3 {{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}"
                                                        placeholder="{{ translate('Contact No') }}" name="phone">
                                                    @if ($errors->has('phone'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('phone') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" class="form-control mb-3" value="{{ old('referred_by') }}"
                                                        placeholder="{{ translate('Refferal Code') }}" name="referred_by"
                                                        value="{{ $referral_code }}">
                                                </div>
                                            </div>
                                        </div>


                                        @if (\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                            <div class="form-group">
                                                <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" name="terms" required>
                                                <span
                                                    class=opacity-60>{{ translate('By signing up you agree to our terms and conditions.') }}</span>
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>

                                        <div class="mb-5">
                                            <button type="submit"
                                                class="btn btn-primary btn-block fw-600">{{ translate('Create Account') }}</button>
                                        </div>
                                    </form>
                                    @if (\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                        <div class="separator mb-3">
                                            <span class="bg-white px-3 opacity-60">{{ translate('Or Join With') }}</span>
                                        </div>
                                        <ul class="list-inline social colored text-center mb-5">
                                            @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                                        class="facebook">
                                                        <i class="lab la-facebook-f"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if (\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                                        class="google">
                                                        <i class="lab la-google"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                                        class="twitter">
                                                        <i class="lab la-twitter"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="text-muted mb-0">{{ translate('Already have an account?') }}</p>
                                    <a href="{{ route('user.login') }}">{{ translate('Log In') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('script')
    @if (\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif


    <script type="text/javascript">
        // cityList

        $('#cityList').change(function() {
            var cityId = $(this).val();
            if (cityId) {
                $.ajax({
                    type: "GET",
                    url: '{{ route('user.getAreaByCity') }}',
                    data: {
                        "city_id": cityId
                    },
                    success: function(res) {
                        if (res) {

                            $("#mainArea").empty();
                            $("#mainArea").append('<option>Select Area of locate</option>');
                            $.each(res, function(key, value) {
                                $("#mainArea").append('<option value="' + key + '">' + value +
                                    '</option>');
                            });
                        } else {
                            $("#mainArea").empty();
                        }
                    }
                });
            } else {
                $("#mainArea").empty();
            }
        });


        $('#is_shop').click(function() {
            $(".is_shop").toggle();
        });

    </script>
    <script type="text/javascript">
        @if (\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
            // making the CAPTCHA a required field for form submission
            $(document).ready(function(){
            // alert('helloman');
            $("#reg-form").on("submit", function(evt)
            {
            var response = grecaptcha.getResponse();
            if(response.length == 0)
            {
            //reCaptcha not verified
            alert("please verify you are humann!");
            evt.preventDefault();
            return false;
            }
            //captcha verified
            //do the rest of your validations here
            $("#reg-form").submit();
            });
            });
        @endif

        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            @php
            echo json_encode(
                \App\Country::where('status', 1)
                    ->pluck('code')
                    ->toArray(),
            );
            @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }

    </script>
@endsection
