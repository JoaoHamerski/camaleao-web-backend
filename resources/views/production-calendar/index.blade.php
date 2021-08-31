@extends('layout')

@section('title', 'Calendário de Produção')

@section('content')
  <production-calendar
    :role-id="{{ Auth::user()->role_id }}"
  ></production-calendar>
@endsection
