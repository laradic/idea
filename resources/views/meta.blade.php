{!! $open !!}
/**
* PhpStorm Meta file, to provide autocomplete information for PhpStorm
* Generated on {!! date("Y-m-d") !!}.
*/

namespace PHPSTORM_META {

/**
* @param callable $callable Class, Method or function call
* @param mixed $method one of
* @see map()
* @see type()
* @see elementType()
* @return mixed override pair object
*/
function override($callable, $override) {
return "override $callable $override";
}
/**
* map argument with #$argNum Literal value to one of expressions
* @param mixed $argNum ignored, for now its always 0
* @param mixed $map Key-value pairs: string_literal|const|class_const => class_name::class|pattern_literal
* where pattern literal can contain @ char to be replaced with argument literal value
* @return mixed overrides map object
*/
function map($map) {
return "map $argNum $map";
}
/**
* type of argument #$argNum
* @param mixed $argNum ignored, for now its always 0
* @return mixed
*/
function type($argNum) {
return "type $argNum";
}
/**
* element type of argument #$argNum
* @param mixed $argNum
* @return mixed
*/
function elementType($argNum) {
return "elementType $argNum";
}

function expectedArguments($functionReference, $argumentIndex, $values) {
return "expectedArguments " . $functionReference . "at " . $argumentIndex . ": " . $values;
}
function registerArgumentsSet($setName, $values) {
return "registerArgumentsSet " . $setName . ": "  . $values;
}
function argumentsSet($setName) {
return "argumentsSet " . $setName;
}
@if(isset($metas))
    @foreach($metas as $meta)
        {!! $meta !!}
    @endforeach
@else
    {!! $meta !!}
@endif
}
