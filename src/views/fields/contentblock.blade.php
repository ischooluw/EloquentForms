@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @if($Field->attributes->value != '')
            <div {!! $Field->attributes !!}>
                {!! $Field->attributes->value !!}
            </div>
        @endif
    @endslot

@endcomponent
