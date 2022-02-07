<x-filament::page>
    <p style="background: #fff; padding: 20px; border-radius: 10px;">
        Applicant Name: <strong>{{ $record->applicant_name }}</strong> <br>
        Program: <strong>{{ $record->program->label }}</strong> <br>
        Term: <strong>{{ $record->term->label }}</strong> <br>
        Total Units: <strong>{{ $record->getTotalUnits() }}</strong> <br>
    </p>

    @foreach ($record->audits()->with('user')->latest()->get() as $audit)
        <p style="background: #fff; padding: 20px; border-radius: 10px;">
            <strong>{{ $audit->user->name }}</strong> {{ $audit->event }} this application
            last {{ $audit->created_at->format('F d, Y h:i a') }}. <br><br>

            <strong style="display: inline-block; width: 60px;">
                Before:
            </strong>
            <code>
                {{ json_encode($audit->old_values) }}
            </code> <br>
            <strong style="display: inline-block; width: 60px;">
                After:
            </strong>
            <code>
                {{ json_encode($audit->new_values) }}
            </code>
        </p>
    @endforeach
</x-filament::page>
