<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Checkout example · Bootstrap v5.0</title>
    <link rel="canonical" href="#">
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Favicons -->
</head>

<body class="bg-light">
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h2>Checkout form</h2>
            </div>
            <form>
                <div class="row">                
                    <div class="col-md-7">
                        <h4 class="mb-3">Billing Details</h4>                    
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" id="firstName" placeholder="" name="first_name" value="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="lastName" placeholder="" name="last_name" value="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com">
                            </div>                            
                            <div class="col-sm-6">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="US">United States</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" placeholder="">
                            </div>
                            <div class="col-sm-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="">
                            </div> 
                            <div class="col-sm-6">
                                <label for="zip" class="form-label">Zip</label>
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="phone_no" class="form-label">Phone No.</label>
                                <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="">
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h4 class="mb-3">Card Details</h4>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="CAD">CAD</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="card_no" class="form-label">Card No.</label>
                                <input type="text" class="form-control" id="card_no" name="card_no" placeholder="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="ccExpiryMonth" class="form-label">Exp. Month</label>
                                <select class="form-select" id="ccExpiryMonth" name="ccExpiryMonth" required>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="ccExpiryYear" class="form-label">Exp. Year</label>
                                <select class="form-select" id="ccExpiryYear" name="ccExpiryYear" required>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                    <option value="2031">2031</option>
                                    <option value="2032">2032</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="cvvNumber" class="form-label">CVV No.</label>
                                <input type="text" class="form-control" id="cvvNumber" name="cvvNumber" placeholder="" required>
                            </div>
                        </div>                  
                    </div>
                    <hr class="my-4">
                    <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
                </div>
            </form>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>