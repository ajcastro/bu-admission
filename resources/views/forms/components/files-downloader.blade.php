<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        @foreach (($getRecord()->requirements ?? []) as $requirement_path)
            <a href="{{ route('download', ['file' => $requirement_path]) }}" style="color: blue;">
                {{  $requirement_path }} <br>
            </a>
        @endforeach
    </div>
</x-forms::field-wrapper>
