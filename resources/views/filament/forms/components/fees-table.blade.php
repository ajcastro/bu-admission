<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        @php
            $rows = $getFees();
        @endphp
        <table width="40%">
            <thead>
                <tr>
                    <th width="50%" align="left">
                        Description
                    </th>
                    <th align="right">
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $label => $amount)
                <tr>
                    <td>
                        {{ $label }}
                    </td>
                    <td align="right">
                        {{ number_format($amount, 2) }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td>
                        <strong>TOTAL</strong>
                    </td>
                    <td align="right">
                        <strong>
                            {{ number_format(collect($rows)->sum(), 2) }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-forms::field-wrapper>
