<x-filament::page>
    <div style="background: #fff; padding: 20px; border-radius: 10px;">
    @php
        $info = [
            'Applicant ID:' => $record->id,
            'Applicant Name:' => $record->applicant_name,
            'Program:' => $record->program->label,
            'Term:' => $record->term->label,
            'Total Units:' => $record->getTotalUnits(),
        ];
    @endphp
        <table>
            <tbody>
                @foreach ($info as $key => $value)
                <tr>
                    <td>{{ $key }} </td>
                    <td style="font-weight: bold;">&nbsp; {{ $value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="background: #fff; padding: 10px 20px; border-radius: 10px; font-weight: bold; font-size: 20px;">
        Logs
    </div>

    @foreach ($record->audits()->with('user')->latest()->get() as $audit)
        <div style="background: #fff; padding: 20px; border-radius: 10px;">
            <strong># {{ $audit->id }} </strong> <br>

            <strong>{{ $audit->user->name ?? '[]' }}</strong> {{ $audit->event }} this application
            last {{ $audit->created_at->format('F d, Y h:i a') }}. <br><br>

            <strong style="display: inline-block; width: 60px;">
                Before:
            </strong>
            <table>
                <tbody>
                    @if (blank($audit->old_values)) -None- @endif
                    @foreach ($audit->old_values as $key => $value)
                    <tr>
                        <td>{{ Str::of($key)->title()->replace('_', ' ') }}: </td>
                        <td>&nbsp; {{ $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <strong style="display: inline-block; width: 60px;">
                After:
            </strong>
            <table >
                <tbody>
                    @foreach ($audit->new_values as $key => $value)
                    <tr>
                        <td>{{ Str::of($key)->title()->replace('_', ' ') }}: </td>
                        <td>&nbsp; {{ $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</x-filament::page>
