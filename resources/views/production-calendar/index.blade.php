@extends('layout')

@section('title', 'Calendário de Produção')

@section('content')
<production-calendar :is-production="{{ Auth::user()->isProduction() ? 'true' : 'false' }}"></production-calendar>
@endsection
