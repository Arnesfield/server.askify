@extends('layouts.email')

@section('title', 'Reset Password')
@section('content', 'It looks like you forgot your password. To proceed to retrieving your account, here is your new password provided below. You may change it once you have logged in.')

@section('append')
New password: <u><strong>{{ $code }}</strong></u>
@endsection
