@php
$attr3 = 'baz';
$attr4 = 99_999;
$attr5 = new stdClass;
$attrWtf = 'wtf';
@endphp

<x-my-component
    attr1="foo"           {{-- Component receives string --}}
    attr2="{{ 'bar' }}"   {{-- Component receives string --}}
    :attr3="$attr3"       {{-- Component receives string --}}
    :attr4="$attr4"       {{-- Component receives int --}}
    :attr5="$attr5"       {{-- Component receives object --}}
    attr6="{{ 10_000 }}"  {{-- Component receives a string --}}
    
    {{-- This syntax doesn't work --}}
    {{-- :attrWtf="{{ $attrWtf }}" --}}
/>