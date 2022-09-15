@extends('layouts.app')

@section('content')
    <example :id="{{ Auth::user()->id }}"></example>
@endsection
