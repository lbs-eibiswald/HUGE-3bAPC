<?php
    // restore formdata from previous error or set empty
    $f = $this->formData ?? [];

    // Helper: safely output a pre-filled field value
    $val = fn(string $key) => htmlspecialchars($f[$key] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="container">
    <h1>Checkout</h1>
    <div class="box">

        <?php $this->renderFeedbackMessages(); ?>

        <a href="<?php echo Config::get('URL'); ?>shop/index">&#8592; Back to Cart</a>

        <div class="row">

            <!-- ===== Billing Address + Payment ===== -->
            <div class="col-75">
                <div class="container">
                    <form action="<?php echo Config::get('URL'); ?>shop/placeOrder" method="post">
                        <div class="row">

                            <!-- ===== BILLING ADDRESS ===== -->
                            <div class="col-50">
                                <h3>Billing Address</h3>

                                <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                                <input type="text" id="fname" name="firstname"
                                    placeholder="John M. Doe"
                                    value="<?= $val('firstname') ?>" required>

                                <label for="email"><i class="fa fa-envelope"></i> Email</label>
                                <input type="text" id="email" name="email"
                                    placeholder="john@example.com"
                                    value="<?= $val('email') ?>" required>

                                <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
                                <input type="text" id="adr" name="address"
                                    placeholder="Eibiswald 1"
                                    value="<?= $val('address') ?>" required>

                                <label for="city"><i class="fa fa-institution"></i> City</label>
                                <input type="text" id="city" name="city"
                                    placeholder="Eibiswald"
                                    value="<?= $val('city') ?>" required>

                                <div class="row">
                                    <div class="col-50">
                                        <label for="state">State</label>
                                        <input type="text" id="state" name="state"
                                            placeholder="Steiermark"
                                            value="<?= $val('state') ?>" required>
                                    </div>
                                    <div class="col-50">
                                        <label for="zip">Zip</label>
                                        <input type="text" id="zip" name="zip"
                                            placeholder="8552"
                                            value="<?= $val('zip') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== PAYMENT DETAILS ===== -->
                            <div class="col-50">
                                <h3>Payment</h3>
                                <label>Accepted Cards</label>

                                <div class="icon-container">
                                    <i class="fa fa-cc-visa"       style="color:navy;"></i>
                                    <i class="fa fa-cc-amex"       style="color:blue;"></i>
                                    <i class="fa fa-cc-mastercard" style="color:red;"></i>
                                    <i class="fa fa-cc-discover"   style="color:orange;"></i>
                                </div>

                                <label for="cname">Name on Card</label>
                                <input type="text" id="cname" name="cardname"
                                    placeholder="John More Doe"
                                    value="<?= $val('cardname') ?>" required>

                                <label for="ccnum">Credit card number</label>
                                <input type="text" id="ccnum" name="cardnumber"
                                    placeholder="1111 2222 3333 4444"
                                    minlength="16" maxlength="19"
                                    value="<?= $val('cardnumber') ?>" required>

                                <label for="expmonth">Exp Month</label>
                                <input type="text" id="expmonth" name="expmonth"
                                    placeholder="September"
                                    value="<?= $val('expmonth') ?>" required>

                                <div class="row">
                                    <div class="col-50">
                                        <label for="expyear">Exp Year</label>
                                        <input type="text" id="expyear" name="expyear"
                                            placeholder="2030"
                                            minlength="4" maxlength="4"
                                            value="<?= $val('expyear') ?>" required>
                                    </div>
                                    <div class="col-50">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" name="cvv"
                                            placeholder="123"
                                            minlength="3" maxlength="4"
                                            value="<?= $val('cvv') ?>" required>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <input type="submit" value="Place Order" class="btn">
                    </form>
                </div>
            </div>

            <!-- ===== Cart Summary ===== -->
            <div class="col-25">
                <div class="container">
                    <h4>Cart
                        <span class="price" style="color:black">
                            <i class="fa fa-shopping-cart"></i>
                            <b><?php echo count($this->shoppingCart); ?></b>
                        </span>
                    </h4>

                    <?php
                        // ===== CALCULATE TOTAL AND LIST ITEMS =====
                        $total = 0.0;
                        foreach ($this->shoppingCart as $cartItem):
                            $lineTotal = $cartItem->price * $cartItem->product_amount;
                            $total    += $lineTotal;
                    ?>
                        <p>
                            <b><?= $cartItem->product_amount ?>x</b> <?= htmlspecialchars($cartItem->name, ENT_QUOTES, 'UTF-8') ?>
                            <span class="price">$<?= number_format($lineTotal, 2) ?></span>
                        </p>
                    <?php endforeach; ?>

                    <hr>
                    <p>Total <span class="price" style="color:black"><b>$<?= number_format($total, 2) ?></b></span></p>
                </div>
            </div>

        </div>
    </div>
</div>
