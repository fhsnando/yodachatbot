@extends('layouts.app')
{{--@php var_dump($messages) @endphp--}}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <yodachatbot session_messages="{{json_encode($messages??[])}}"></yodachatbot>
        </div>
    </div>
@endsection
