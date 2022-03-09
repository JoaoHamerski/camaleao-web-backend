@extends('pdf.layout')

@section('title', $title)

@section('content')

@if (!$expenses->isEmpty())
  @include('pdf.expenses.expense-types')
  @include('pdf.expenses.expenses')
@else
  @include('pdf.empty')
@endif
@endsection
