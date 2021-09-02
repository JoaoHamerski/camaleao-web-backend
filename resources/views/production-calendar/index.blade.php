@extends('layout')

@section('title', 'Calendário de Produção')

@section('content')
  <the-production-calendar
    :role-id="{{ Auth::user()->role_id }}"
  ></the-production-calendar>
@endsection
