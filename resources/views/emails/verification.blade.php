@extends('layouts.email')

@section('title', 'Email Verification')
@section('content', 'We need you to confirm this email address in order to get started with exploring Askify. Email confirmation is simple and fast, just click on the link below to complete this process.')

@section('append')
<a
  {{ $attrDate }}
  style="color: {{ $config['color_accent'] }}"
  href="{{ url('auth/verify?c=' . $code) }}"
>Verify email address</a>
@endsection
