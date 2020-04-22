<div class="row">
    <h4>{{ $widget['name'] }}</h4>
    <ul class="list-group">

        @foreach(\App\Helpers::getOrderStatuses() as $key => $item)

            <li class="list-group-item {{request()->input('type') == $key ? 'active' : ''}}">
                <a href="#">{{$item}}
                    <span class="badge ng-binding">1</span>
                </a></li>
        @endforeach
    </ul>
</div>