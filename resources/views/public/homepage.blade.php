@extends('layouts.public')

@section('content')

@if(!empty($template))
    @foreach($template as $section)
        @includeIf('public.section.'.$section->getType(), ['data' => $section])
    @endforeach
@endif

@endsection