@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;color:#a94442;">{{ __('autopay.email.failed_title') }}</h2>
    <p>{{ __('autopay.email.failed_intro', ['card' => $card]) }}</p>

    @if ($reason)
        <p><strong>{{ __('autopay.email.reason') }}:</strong> {{ $reason }}</p>
    @endif

    <p>
        @if ($nextTry)
            {{ __('autopay.email.failed_retry', ['date' => $nextTry]) }}
        @else
            {{ __('autopay.email.failed_final') }}
        @endif
    </p>

    <p><a href="{{ $url }}" style="display:inline-block;background:#1f4e79;color:#fff;padding:10px 18px;border-radius:4px;text-decoration:none;">{{ __('autopay.email.manage') }}</a></p>
@endsection
