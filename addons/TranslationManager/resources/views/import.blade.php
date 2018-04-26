@extends('layout')

@section('content')

    <div class="card flat-bottom">
        <div class="head">
            <h1>Import translations</h1>
        </div>
    </div>
    <div class="card flat-top">
        <form method="post" action="{{$actionUrl}}" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group select-fieldtype width-100 ">
                <div class="field-inner">
                    <label class="block">File</label>

                    <small class="help-block">
                        <p>Should be a .xlf or .xliff file.</p>
                    </small>

                    <input type="file" name="file">

                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Import" style="margin-top:20px;">
        </form>
    </div>
@endsection
