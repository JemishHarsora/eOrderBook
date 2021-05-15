<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ static_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: 'Roboto';
            color: #333542;
        }

        body {
            font-size: .875rem;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .5rem .7rem;
        }

        table.padding td {
            padding: .7rem;
        }

        table.sm-padding td {
            padding: .2rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: .85rem;
        }

        .currency {}

    </style>
</head>

<body>
    <div>
        @php
            $logo = get_setting('header_logo');
        @endphp
        <div style="background: #eceff4;padding: 1.5rem;">
            <table>
                <tr>
                    <td>
                        @if ($logo != null)
                            <img loading="lazy" src="{{ uploaded_asset($logo) }}" height="40"
                                style="display:inline-block;">
                        @else
                            <img loading="lazy" src="{{ static_asset('assets/img/logo.png') }}" height="40"
                                style="display:inline-block;">
                        @endif
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="font-size: 1.2rem;" class="strong">{{ get_setting('site_name') }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="gry-color small">{{ get_setting('contact_address') }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="gry-color small">{{ translate('Email') }}: {{ get_setting('contact_email') }}</td>
                </tr>
            </table>
        </div>
        <h1>

            Welcome, {{ $details['name'] }}
        </h1>
        <br>
        <br>
        <p>Your account is create successfully. Your details is display below</p>
        <p>Email:{{ $details['email'] }}</p>
        <p>Password:{{ $details['password'] }}</p>
        <p>User Type:{{ $details['user_type'] }}</p>
        <p>Phone:{{ $details['phone'] }}</p>
