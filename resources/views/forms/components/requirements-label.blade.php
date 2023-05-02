<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        {!! \App\Models\Setting::value('requirements') !!}
        {{-- <p>
            <strong>
                Please be reminded to submit authenticated/signed electronic or scanned copy all necessary documentary requirements for admission to avoid disqualification.
            </strong>
        </p>
        <br>
        <p>
            For the needed forms and attachments pls see: <br>
            <a href="https://drive.google.com/drive/folders/1z1yrEhX23tQ3uqBrRmUU9dPGtpqnGjuC?usp=sharing" target="_blank" style="color: blue;">Required Forms - Google Drive</a>
        </p> --}}
    </div>
</x-dynamic-component>
