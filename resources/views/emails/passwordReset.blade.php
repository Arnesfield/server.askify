@extends('layouts.email')

@section('title', 'Reset Password')
@section('content', 'It looks like you forgot your password. To proceed to retrieving your account, please click on the link provided below.')

@section('append')
<a
  {{ $attrDate }}
  style="color: {{ $config['color_accent'] }}"
  href="{{ url('auth/reset?c=' . $code) }}"
>Reset your password</a>
@endsection
