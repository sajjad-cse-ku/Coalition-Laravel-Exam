<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container mt-5">
<h1>Product Management</h1>
<form id="productForm">
    @csrf
    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Quantity in Stock</label>
        <input type="number" name="quantity_in_stock" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Price per Item</label>
        <input type="number" step="0.01" name="price_per_item" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<h2 class="mt-5">Submitted Products</h2>
<table class="table">
    <thead>
    <tr>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Date Submitted</th>
        <th>Total Value</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody id="productTable"></tbody>
    <tfoot>
    <tr>
        <th colspan="4">Total</th>
        <th id="totalValue"></th>
    </tr>
    </tfoot>
</table>

<script>
    document.addEventListener('DOMContentLoaded', fetchProducts);

    async function fetchProducts() {
        const response = await fetch('/fetch');
        const data = await response.json();

        let rows = '';
        data.products.forEach(product => {
            rows += `<tr>
                    <td>${product.product_name}</td>
                    <td>${product.quantity_in_stock}</td>
                    <td>${product.price_per_item}</td>
                    <td>${new Date(product.created_at).toLocaleString()}</td>
                    <td>${product.quantity_in_stock * product.price_per_item}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id})">Edit</button>
                    </td>
                </tr>`;
        });

        document.getElementById('productTable').innerHTML = rows;
        document.getElementById('totalValue').innerText = data.total;
    }

    document.getElementById('productForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        await fetch('/store', { method: 'POST', body: formData });
        fetchProducts();
    });

    async function editProduct(id) {
        const newName = prompt('Enter new product name:');
        const newQty = prompt('Enter new quantity:');
        const newPrice = prompt('Enter new price per item:');

        if (newName && newQty && newPrice) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();
            formData.append('_method', 'PUT'); // Method override
            formData.append('product_name', newName);
            formData.append('quantity_in_stock', newQty);
            formData.append('price_per_item', newPrice);

            await fetch(`/update/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            fetchProducts();
        }
    }

</script>
</body>
</html>
