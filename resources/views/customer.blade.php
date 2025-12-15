@foreach($customers as $customer)
  <tr>
    <td>{{ $customer->name }}</td>
    <td>{{ $customer->id }}</td>
  </tr>
@endforeach


<h1>Add Customers</h1>
<form action="{{ route('customers.store') }}" method="POST">
     @csrf
    <input type="text" name="name" placeholder="Enter your name">
    <button type="submit">Add Customer</button>
    @if (session('success'))
      <div>
        {{ session('success')}}
      </div>
    @endif
</form>

<h1>Update Customer</h1>
<form action="{{ route('customers.update') }}" method="POST">
    @csrf
    @method('PUT')

    ID: <input type="number" name="id" placeholder="Enter customer ID"><br>
    Name: <input type="text" name="name" placeholder="Enter new name"><br>

    <button type="submit">Update</button>
</form>

<h1>Delete Customer</h1>
<form action="{{ route('customers.destroy') }}" method="POST">
@csrf
@method('DELETE')
 ID: <input type="number" name="id" placeholder="Enter customer ID"><br>
 <button type="submit">Delete</button>
</form>
  
