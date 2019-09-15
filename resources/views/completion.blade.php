{!! $open !!}
// formatter:off

@foreach($namespaces as $namespace => $classes)
namespace {!! $namespace  !!} {
    @foreach($classes as $class => $doc)

    /**
    {!! $doc !!}
    */
    class {!! $class !!} {}
    @endforeach
}
@endforeach
