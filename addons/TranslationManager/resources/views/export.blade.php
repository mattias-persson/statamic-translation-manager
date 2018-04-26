@extends('layout')

@section('content')

    <div class="card flat-bottom">
        <div class="head">
            <h1>Export translations</h1>
        </div>
    </div>
    <div class="card flat-top">
        <form method="post" action="{{$actionUrl}}">
            {{ csrf_field() }}

            <div class="form-group select-fieldtype width-100">
                <div class="field-inner">
                    <label class="block">Language</label>

                    <small class="help-block">
                        <p>Select which language, if one specific, you wish to export.</p>
                    </small>

                    <div class="select select-full"
                        data-content="All languages"
                        id="locale-wrapper">

                        <select name="locale"
                            id="locale-selector"
                            class="form-control"
                            onchange="setSelectedValue('locale')">
                            <option value="all" selected>All languages</option>
                            @foreach($locales as $locale)
                                <option value="{{$locale}}">{{$locale}}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>

            <div class="form-group select-fieldtype width-100">
                <div class="field-inner">
                    <div class="radio-fieldtype-wrapper">
                        <ul class="list-unstyled">
                            <li>
                                <input type="radio" value="everything" name="content" id="everything" checked onclick="$('#content-selection').css('display', 'none');">
                                <label for="everything">Export everything</label>
                            </li>
                            <li>
                                <input type="radio" value="specific" name="content" id="specific" onclick="$('#content-selection').css('display', 'block');">
                                <label for="specific">Select what to export</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="content-selection" style="display: none">
                <hr>
                <div class="form-group select-fieldtype width-100">
                    <div class="field-inner">
                        <label class="block">Page</label>

                        <small class="help-block">
                            <p>Select which page you wish to export.</p>
                        </small>

                        <div class="select select-full"
                            data-content="All pages"
                            id="page-wrapper">

                            <select name="page"
                                id="page-selector"
                                class="form-control"
                                onchange="setSelectedValue('page')">
                                <option value="all" selected>All pages</option>
                                <option value="no">No pages</option>
                                <option disabled>------</option>
                                @foreach($pages as $page)
                                    <option value="{{$page->id()}}">{{$page->get('title')}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="form-group select-fieldtype width-100">
                    <div class="field-inner">
                        <label class="block">Global</label>

                        <small class="help-block">
                            <p>Select which global you wish to export.</p>
                        </small>

                        <div class="select select-full"
                            data-content="All globals"
                            id="global-wrapper">

                            <select name="global"
                                id="global-selector"
                                class="form-control"
                                onchange="setSelectedValue('global')">
                                <option value="all" selected>All globals</option>
                                <option value="no">No globals</option>
                                <option disabled>------</option>
                                @foreach($globals as $global)
                                    <option value="{{$global->id()}}">{{$global->get('title')}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="form-group select-fieldtype width-100">
                    <div class="field-inner">
                        <label class="block">Collection</label>

                        <small class="help-block">
                            <p>Select which collection you wish to export.</p>
                        </small>

                        <div class="select select-full"
                            data-content="All collections"
                            id="collection-wrapper">

                            <select name="collection"
                                id="collection-selector"
                                class="form-control"
                                onchange="setSelectedValue('collection')">
                                <option value="all" selected>All collections</option>
                                <option value="no">No collection</option>
                                <option disabled>------</option>
                                @foreach($collections as $collection)
                                    <option value="{{$collection->path()}}">{{$collection->get('title')}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="form-group select-fieldtype width-100">
                    <div class="field-inner">
                        <label class="block">Taxonomies</label>

                        <small class="help-block">
                            <p>Select which taxonomy you wish to export.</p>
                        </small>

                        <div class="select select-full"
                            data-content="All taxonomies"
                            id="taxonomy-wrapper">

                            <select name="taxonomy"
                                id="taxonomy-selector"
                                class="form-control"
                                onchange="setSelectedValue('taxonomy')">
                                <option value="all" selected>All taxonomies</option>
                                <option value="no">No taxonomies</option>
                                <option disabled>------</option>
                                @foreach($taxonomies as $taxonomy)
                                    <option value="{{$taxonomy->path()}}">{{$taxonomy->get('title')}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

            </div>

            <input type="submit" class="btn btn-primary" value="Export">
        </form>
    </div>

    <script>
        function setSelectedValue(element){
            var wrapper = document.getElementById(element + '-wrapper');
            var field = document.getElementById(element + '-selector');
            var value = field.options[field.selectedIndex].innerHTML;

            if(!value){
                value = field.options[0].value;
            }

            wrapper.setAttribute('data-content', value);
        }
    </script>

@endsection
