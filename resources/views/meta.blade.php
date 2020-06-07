{!! $open !!}
//formatter:off
/**
* PhpStorm Meta file, to provide autocomplete information for PhpStorm
* Generated on {!! date("Y-m-d") !!}.
*/

namespace PHPSTORM_META {
@if(isset($metas))
    @foreach($metas as $meta)
        {!! $meta !!}
    @endforeach
@else
    {!! $meta !!}
@endif
}
