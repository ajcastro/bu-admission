<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Application Summary</title>
</head>
<body>
  <h3>BUOUO Admission Data</h3>
  <h4>Summarized List</h4>

  <br>

  <h5>School Year: {{ $terms->pluck('label')->implode('; ') }}</h5>
  <h5>Program: {{ $programs->pluck('label')->implode('; ') }}</h5>
  <h5>Status: {{ $statuses->implode('; ') }}</h5>

  <br>

  <h5>No. of records in the list:</h5>
  <table>
    <thead>
    <tr>
      <th><strong>Program</strong></th>
      <th><strong>Pending</strong></th>
      <th><strong>Recommended</strong></th>
      <th><strong>Admitted</strong></th>
      <th><strong>Processed</strong></th>
      <th><strong>Rejected</strong></th>
    </tr>
    </thead>
    <tbody>
      @foreach ($recordsByStatuses as $row)
      <tr>
        <td>{{ $row->program->label ?? '' }}</td>
        <td>{{ $row->pending_count ?? '' }}</td>
        <td>{{ $row->recommended_count ?? '' }}</td>
        <td>{{ $row->admitted_count ?? '' }}</td>
        <td>{{ $row->processed_count ?? '' }}</td>
        <td>{{ $row->rejected_count ?? '' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table>
    <thead>
    <tr>
      <th><strong>#</strong></th>
      <th><strong>Application ID</strong></th>
      <th><strong>Application Name</strong></th>
      <th><strong>Program</strong></th>
      <th><strong>Total Units</strong></th>
      <th><strong>Term</strong></th>
      <th><strong>Status</strong></th>
      <th><strong>Created At</strong></th>
      <th><strong>Updated At</strong></th>
    </tr>
    </thead>
    <tbody>
      @foreach ($applications as $index => $application)
        <tr>
          <td> {{ $index+1 }}</td>
          <td> {{ $application->id }}</td>
          <td> {{ $application->applicant_name }}</td>
          <td> {{ $application->program->label }}</td>
          <td> {{ $application->total_units }}</td>
          <td> {{ $application->term->label }}</td>
          <td> {{ ucfirst($application->status) }}</td>
          <td> {{ $application->created_at->format('m/d/Y H:i a') }}</td>
          <td> {{ $application->updated_at->format('m/d/Y H:i a') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>
