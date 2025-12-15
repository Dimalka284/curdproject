@foreach($students as $student)
  <tr>
    <td>{{ $student->fristname }}</td>
    <td>{{ $student->id }}</td>
  </tr>
@endforeach