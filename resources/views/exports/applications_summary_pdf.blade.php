<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Application Summary</title>
  <style>
    * {
      font-family: Arial;
    }
  </style>
</head>
<body>
  <img src="data:image/jpeg;base64,{{ base64_encode(@file_get_contents(url('/images/navbar-brand-for-pdf.png'))) }}"
    alt="Logo" width="300">

  <h3>Bicol University Open University Admission Data</h3>
  <h4>Summarized List as of {{ $asOfLabel }}</h4>

  <br>

  <h5>School Year: {{ $terms->pluck('label')->implode('; ') }}</h5>
  <h5>Program: {{ $programs->pluck('label')->implode('; ') }}</h5>
  <h5>Status: {{ $statuses->implode('; ') }}</h5>

  <br>

  <h5>No. of records in the list:</h5>
  <table width="100%">
    <thead>
    <tr>
      <th align="left"><strong>Program</strong></th>
      <th align="right"><strong>Pending</strong></th>
      <th align="right"><strong>Recommended</strong></th>
      <th align="right"><strong>Admitted</strong></th>
      <th align="right"><strong>Processed</strong></th>
      <th align="right"><strong>Rejected</strong></th>
    </tr>
    </thead>
    <tbody>
      @foreach ($recordsByStatuses as $row)
      <tr>
        <td align="left">{{ $row->program->label ?? '' }}</td>
        <td align="right">{{ $row->pending_count ?? '' }}</td>
        <td align="right">{{ $row->recommended_count ?? '' }}</td>
        <td align="right">{{ $row->admitted_count ?? '' }}</td>
        <td align="right">{{ $row->processed_count ?? '' }}</td>
        <td align="right">{{ $row->rejected_count ?? '' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table width="100%" style="margin-top: 50px;">
    <tbody>
      <tr>
        <td> Prepared by: </td>
        <td> Noted </td>
      </tr>
      <tr>
        <td style="padding-top: 20px;"> {{ \App\Models\User::registrar()?->name }} </td>
        <td style="padding-top: 20px;"> {{ \App\Models\User::dean()?->name }} </td>
      </tr>
      <tr>
        <td> Registrar </td>
        <td> Dean </td>
      </tr>
    </tbody>
  </table>

</body>
</html>
