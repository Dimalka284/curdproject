<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Product Table</h1>
   @foreach ($products as $product)
       <p>Product ID --> {{ $product->id}}</p>
        <p>Product Name --> {{ $product->name}}</p>
        <p>Product Price -->Rs.{{ $product->price}}</p>
   @endforeach

<a href="/index"><button>Create</button></a>
<a href="/home"><button>Home</button></a>
</body>
</html>