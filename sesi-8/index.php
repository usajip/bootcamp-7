<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <h1 class="text-center mt-5">Form Input Product</h1>
                <!-- Form Input Product -->
                <form action="form_process.php" method="post" id="productForm" novalidate enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter product name" name="name" required>
                        <div class="invalid-feedback">Please enter product name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" placeholder="Enter product price" name="price" required>
                        <div class="invalid-feedback">Price must be greater than 0.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Enter product description" name="description" required></textarea>
                        <div class="invalid-feedback">Please enter product description.</div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select category</option>
                            <option value="electronics">Electronics</option>
                            <option value="fashion">Fashion</option>
                            <option value="home">Home</option>
                            <option value="beauty">Beauty</option>
                        </select>
                        <div class="invalid-feedback">Please select category.</div>
                    </div>
                    <!-- Image upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="invalid-feedback">Please upload a valid image file.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        const productForm = document.getElementById('productForm');
        const priceInput = document.getElementById('price');
        const stockInput = document.getElementById('stock');
        const imageInput = document.getElementById('image');

        productForm.addEventListener('submit', function (event) {
            priceInput.setCustomValidity('');
            stockInput.setCustomValidity('');
            imageInput.setCustomValidity('');

            if (Number(priceInput.value) <= 0) {
                priceInput.setCustomValidity('Price must be greater than 0');
            }

            if (Number(stockInput.value) < 0) {
                stockInput.setCustomValidity('Stock must be 0 or more');
            }

            if (!productForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            productForm.classList.add('was-validated');
        });

        [priceInput, stockInput].forEach(function (input) {
            input.addEventListener('input', function () {
                input.setCustomValidity('');
            });
        });
    </script>
</body>
</html>