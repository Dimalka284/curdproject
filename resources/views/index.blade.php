<h1>Create</h1>
<form action="{{route('products.store')}}" method="POST">
    @csrf
    Product Name: <input type="text" name="name"><br>
    Price: <input type="number" name="price"><br>
    Description: <input type="text" name="description"><br>
    Category:
        <select name="category_id">
            @foreach ($category as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    <button type="submit">Submit</button>
</form><br><br>

<h1>Delete</h1>
<form action="{{route('products.destory')}}" method="POST">
    @csrf
    Product Id <input type="number" name="id"><br>
    <button type="submit">Delete</button>
</form><br><br>

<h1>Update</h1>
<form action="{{route('products.update')}}" method="POST">
    @csrf
    Product ID <input type="number" name="id"><br>
    New Name <input type="text" name="name"><br>
    New Price <input type="number" name="price"><br>

    <button type="submit">Update</button>
</form>



