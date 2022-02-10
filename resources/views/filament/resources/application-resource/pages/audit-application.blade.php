<x-filament::page>
    <p style="background: #fff; padding: 20px; border-radius: 10px;">
        Applicant Name: <strong>{{ $record->applicant_name }}</strong> <br>
        Program: <strong>{{ $record->program->label }}</strong> <br>
        Term: <strong>{{ $record->term->label }}</strong> <br>
        Total Units: <strong>{{ $record->getTotalUnits() }}</strong> <br>
    </p>

    @foreach ($record->audits()->with('user')->latest()->get() as $audit)
        <div style="background: #fff; padding: 20px; border-radius: 10px;">
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
                        <td>{{ $key }}: </td>
                        <td>{{ $value }}</td>
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
                        <td>{{ $key }}: </td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</x-filament::page>
