@extends('layout')

@section('content')
    <div style="display: flex; justify-content: space-around; align-content: flex-start;">
        <div style="width: 45%;">
            <div class="card flat-bottom">
                <div class="head">
                    <h1>Translated strings</h1>
                </div>
            </div>
            <div class="card flat-top text-center">
                <h1>{{$stats['translated']}} of {{$stats['total']}}</h1>
            </div>
        </div>

        <div style="width: 45%;">
            <div class="card flat-bottom">
                <div class="head">
                    <h1>Translatable languages</h1>
                </div>
            </div>
            <div class="card flat-top text-center">
                @foreach($locales as $index => $locale)
                    {{$locale}}
                    @if($index < count($locales))
                        &bullet;
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
