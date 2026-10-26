@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;color:#1f4e79;">{{ __('autopay.email.reminder_title') }}</h2>
    <p>{{ __('autopay.email.reminder_intro', ['date' => $chargeDate, 'card' => $card]) }}</p>

    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;margin:16px 0;">
        <tr style="background:#f0f3f6;text-align:left;">
            <th>{{ __('autopay.email.student') }}</th>
            <th>{{ __('autopay.email.concept') }}</th>
            <th style="text-align:right;">{{ __('autopay.email.amount') }}</th>
        </tr>
        @foreach ($items as $item)
            <tr style="border-bottom:1px solid #e5e8eb;">
                <td>{{ $item['student'] }}</td>
                <td>{{ $item['concept'] }}</td>
                <td style="text-align:right;">${{ number_format($item['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align:right;font-weight:bold;">{{ __('autopay.email.estimated_total') }}</td>
            <td style="text-align:right;font-weight:bold;">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <p style="color:#666;font-size:13px;">{{ __('autopay.email.reminder_note') }}</p>
    <p><a href="{{ $url }}" style="display:inline-block;background:#1f4e79;color:#fff;padding:10px 18px;border-radius:4px;text-decoration:none;">{{ __('autopay.email.manage') }}</a></p>
@endsection
