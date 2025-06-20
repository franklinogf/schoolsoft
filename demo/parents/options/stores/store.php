<?php
require_once '../../../app.php';

use App\Models\Store;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();

// Get store ID from query parameter
$store_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$store = Store::find($store_id);

// Include cart actions
require_once 'includes/cart_actions.php';
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Tiendas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __("Tiendas") ?></h1>
        <?php if (!$store): ?>
            <div class="alert alert-danger"><?= __("Tienda no encontrada") ?></div>
        <?php exit;
        endif; ?>
        <?php
        // Get products for this store
        $products = $store->items;
        ?>

        <div class="row" id="store" data-store-prefix="<?= $store->prefix_code ?>">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><?= htmlspecialchars($store->name) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($store->description ?? '') ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (empty($products)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info"><?= __("No hay productos disponibles") ?></div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $product):
                                    // Decode options JSON
                                    $options = [];
                                    if (!empty($product->options)) {
                                        $options = json_decode($product->options, true);
                                    }
                                ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100">
                                            <?php if (!empty($product->picture_url)): ?>
                                                <img src="<?= htmlspecialchars($product->picture_url) ?>" class="card-img-top" alt="<?= htmlspecialchars($product->name) ?>">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                                                <p class="card-text"><?= htmlspecialchars($product->description ?? '') ?></p>

                                                <form method="post">
                                                    <input type="hidden" name="product_id" value="<?= $product->id ?>">

                                                    <?php if (!empty($options)): ?>
                                                        <div class="form-group mb-3">
                                                            <label for="option_<?= $product->id ?>"><?= __("Opciones") ?>:</label>
                                                            <select class="form-control" id="option_<?= $product->id ?>" name="option_index">
                                                                <?php foreach ($options as $index => $option):
                                                                    // Determine the price to display
                                                                    $option_price = !is_null($option['price']) ? $option['price'] : $product->price;
                                                                    $display_price = "$" . number_format($option_price, 2);
                                                                ?>
                                                                    <option value="<?= $index ?>">
                                                                        <?= htmlspecialchars($option['name']) ?>
                                                                        (<?= $display_price ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="card-text font-weight-bold">$<?= number_format($product->price, 2) ?></p>
                                                        <input type="hidden" name="option_index" value="-1">
                                                    <?php endif; ?>

                                                    <?php if ($product->buy_multiple): ?>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><?= __("Cantidad") ?></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="quantity" value="1" min="1">
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="hidden" name="quantity" value="1">
                                                    <?php endif; ?>
                                                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">
                                                        <?= __("Añadir al carrito") ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px">
                    <div class="card-header bg-primary text-white">
                        <h4><?= __("Carrito de compras") ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <div class="alert alert-info"><?= __("Su carrito está vacío") ?></div>
                        <?php else: ?>
                            <?php
                            $total = 0;
                            $cart_items = [];
                            foreach ($_SESSION['cart'] as $cart_key => $cart_item) {
                                $product_id = $cart_item['product_id'];
                                $option_index = $cart_item['option_index'];
                                $quantity = $cart_item['quantity'];

                                $product = DB::table('store_items')->where("id", $product_id)->first();

                                if ($product) {
                                    $price = $product->price;
                                    $option_name = '';

                                    // Get option details if exists
                                    if ($option_index >= 0 && !empty($product->options)) {
                                        $options = json_decode($product->options, true);
                                        if (isset($options[$option_index])) {
                                            $option = $options[$option_index];
                                            $option_name = $option['name'];
                                            // Replace price with option price if not null
                                            if (!is_null($option['price'])) {
                                                $price = $option['price'];
                                            }
                                        }
                                    }

                                    $subtotal = $price * $quantity;
                                    $total += $subtotal;

                                    $cart_items[] = [
                                        'cart_key' => $cart_key,
                                        'id' => $product_id,
                                        'name' => $product->name,
                                        'option_name' => $option_name,
                                        'price' => $price,
                                        'quantity' => $quantity,
                                        'subtotal' => $subtotal,
                                        'buy_multiple' => $product->buy_multiple
                                    ];
                                }
                            }

                            foreach ($cart_items as $item):
                            ?>
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                <?php if (!empty($item['option_name'])): ?>
                                                    <small class="text-muted"><?= __("Opción") ?>: <?= htmlspecialchars($item['option_name']) ?></small><br>
                                                <?php endif; ?>
                                                <small class="text-muted price-display">$<?= number_format($item['price'], 2) ?> x <span class="quantity-value"><?= $item['quantity'] ?></span></small>
                                            </div>
                                            <span class="subtotal-display">$<?= number_format($item['subtotal'], 2) ?></span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <?php if ($item['buy_multiple']): ?>
                                                <div class="quantity-controls">
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-outline-secondary btn-quantity" data-action="decrease" data-cart-key="<?= $item['cart_key'] ?>">-</button>
                                                        </div>
                                                        <input type="number" class="form-control text-center quantity-input" value="<?= $item['quantity'] ?>" min="1"
                                                            data-cart-key="<?= $item['cart_key'] ?>" data-price="<?= $item['price'] ?>">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary btn-quantity" data-action="increase" data-cart-key="<?= $item['cart_key'] ?>">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div></div> <!-- Empty div for spacing -->
                                            <?php endif; ?>

                                            <form method="post">
                                                <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                                <button type="submit" name="remove_from_cart" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="d-flex justify-content-between mt-3">
                                <h5><?= __("Total") ?>:</h5>
                                <h5>$<?= number_format($total, 2) ?></h5>
                            </div>

                            <button type="button" class="btn btn-success btn-block mt-3" id="checkout-btn" data-toggle="modal" data-target="#paymentModal" data-amount="<?= $total ?>">
                                <?= __("Proceder al pago") ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel"><?= __("Realizar Pago") ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <?= __("Monto a pagar") ?>: <strong id="payment-amount">$0.00</strong>
                    </div>

                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="credit-card-tab" data-toggle="tab" href="#credit-card" role="tab" aria-controls="credit-card" aria-selected="true"><?= __("Tarjeta de Crédito") ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ach-tab" data-toggle="tab" href="#ach" role="tab" aria-controls="ach" aria-selected="false"><?= __("ACH") ?></a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="paymentTabsContent">
                        <!-- Credit Card Form -->
                        <div class="tab-pane fade show active" id="credit-card" role="tabpanel" aria-labelledby="credit-card-tab">
                            <form id="credit-card-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cc-name"><?= __("Nombre en la tarjeta") ?></label>
                                        <input type="text" class="form-control" id="cc-name" name="customerName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cc-email"><?= __("Correo electrónico") ?></label>
                                        <input type="email" class="form-control" id="cc-email" name="customerEmail" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="cc-number"><?= __("Número de tarjeta") ?></label>
                                    <input type="text" class="form-control" id="cc-number" name="cardNumber" required placeholder="XXXX XXXX XXXX XXXX">
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="cc-exp-month"><?= __("Mes de expiración") ?></label>
                                        <select class="form-control" id="cc-exp-month" name="expMonth" required>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cc-exp-year"><?= __("Año de expiración") ?></label>
                                        <select class="form-control" id="cc-exp-year" name="expYear" required>
                                            <?php $currentYear = (int) date('Y'); ?>
                                            <?php for ($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                                                <option value="<?= substr((string) $i, -2) ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cc-cvv"><?= __("CVV") ?></label>
                                        <input type="text" class="form-control" id="cc-cvv" name="cvv" required placeholder="XXX">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cc-zipcode"><?= __("Código postal") ?></label>
                                        <input type="text" class="form-control" id="cc-zipcode" name="zipcode">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-3"><?= __("Pagar con Tarjeta de Crédito") ?></button>
                            </form>
                        </div>

                        <!-- ACH Form -->
                        <div class="tab-pane fade" id="ach" role="tabpanel" aria-labelledby="ach-tab">
                            <form id="ach-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-name"><?= __("Nombre completo") ?></label>
                                        <input type="text" class="form-control" id="ach-name" name="customerName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-email"><?= __("Correo electrónico") ?></label>
                                        <input type="email" class="form-control" id="ach-email" name="customerEmail" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-routing"><?= __("Número de ruta") ?></label>
                                        <input type="text" class="form-control" id="ach-routing" name="routing" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-account"><?= __("Número de cuenta") ?></label>
                                        <input type="text" class="form-control" id="ach-account" name="bankAccount" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-account-type"><?= __("Tipo de cuenta") ?></label>
                                        <select class="form-control" id="ach-account-type" name="accType" required>
                                            <option value="w"><?= __("Cuenta corriente") ?></option>
                                            <option value="s"><?= __("Cuenta de ahorros") ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ach-zipcode"><?= __("Código postal") ?></label>
                                        <input type="text" class="form-control" id="ach-zipcode" name="zipcode">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3"><?= __("Pagar con ACH") ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div class="modal fade" id="processingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only"><?= __("Procesando...") ?></span>
                    </div>
                    <h4><?= __("Procesando su pago...") ?></h4>
                    <p><?= __("Por favor no cierre esta ventana.") ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Result Modal -->
    <div class="modal fade" id="paymentResultModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="result-title"><?= __("Resultado del Pago") ?></h5>
                </div>
                <div class="modal-body" id="payment-result-content">
                    <!-- Content will be inserted via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="result-ok-btn"><?= __("Aceptar") ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle quantity buttons
            document.querySelectorAll('.btn-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    const cartKey = this.dataset.cartKey;
                    const inputElement = document.querySelector(`.quantity-input[data-cart-key="${cartKey}"]`);
                    let quantity = parseInt(inputElement.value);

                    if (action === 'increase') {
                        quantity += 1;
                    } else if (action === 'decrease' && quantity > 1) {
                        quantity -= 1;
                    }

                    inputElement.value = quantity;
                    updateCartQuantity(cartKey, quantity);
                });
            });

            // Handle direct input changes
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cartKey = this.dataset.cartKey;
                    let quantity = parseInt(this.value);

                    // Ensure minimum quantity is 1
                    if (isNaN(quantity) || quantity < 1) {
                        quantity = 1;
                        this.value = 1;
                    }

                    updateCartQuantity(cartKey, quantity);
                });
            });

            // Function to update cart quantity via AJAX
            function updateCartQuantity(cartKey, quantity) {
                const formData = new FormData();
                formData.append('update_quantity', '1');
                formData.append('cart_key', cartKey);
                formData.append('quantity', quantity);

                fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.success) {
                            // Update UI elements
                            const container = document.querySelector(`.quantity-input[data-cart-key="${cartKey}"]`).closest('.card');
                            container.querySelector('.quantity-value').textContent = data.quantity;
                            container.querySelector('.subtotal-display').textContent = data.subtotal_formatted;

                            // Recalculate total
                            updateCartTotal();
                        }
                    })
                    .catch(error => console.error('Error updating quantity:', error));
            }

            // Function to recalculate cart total
            function updateCartTotal() {
                let total = 0;
                document.querySelectorAll('.subtotal-display').forEach(element => {
                    // Extract numeric value from the formatted price
                    const subtotal = parseFloat(element.textContent.replace('$', '').replace(',', ''));
                    total += subtotal;
                });

                document.querySelector('.justify-content-between h5:last-child').textContent = '$' + total.toFixed(2);
            }
        });
    </script>

    <script>
        // Payment functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set payment amount in modal
            document.getElementById('checkout-btn')?.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                document.getElementById('payment-amount').textContent = '$' + parseFloat(amount).toFixed(2);
            });

            // Credit Card Form Submission
            document.getElementById('credit-card-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const paymentData = {};

                // Convert FormData to object
                formData.forEach((value, key) => {
                    paymentData[key] = value;
                });

                // Add payment method and amount
                paymentData.paymentMethod = 'creditCard';
                paymentData.amount = document.getElementById('checkout-btn').getAttribute('data-amount');
                paymentData.evertecPrefix = document.getElementById('store').getAttribute('data-store-prefix');

                // Send payment request
                processPayment(paymentData);
            });

            // ACH Form Submission
            document.getElementById('ach-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const paymentData = {};

                // Convert FormData to object
                formData.forEach((value, key) => {
                    paymentData[key] = value;
                });

                // Add payment method and amount
                paymentData.paymentMethod = 'ach';
                paymentData.amount = document.getElementById('checkout-btn').getAttribute('data-amount');
                paymentData.evertecPrefix = document.getElementById('store').getAttribute('data-store-prefix');

                // Send payment request
                processPayment(paymentData);
            });

            // Function to process payment
            function processPayment(paymentData) {
                // Hide payment modal and show processing modal
                $('#paymentModal').modal('hide');
                $('#processingModal').modal('show');

                // Send payment request to server
                fetch('includes/process_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(paymentData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide processing modal
                        $('#processingModal').modal('hide');

                        // Prepare result content
                        let resultContent = '';
                        let resultTitle = '';

                        if (data.success) {
                            resultTitle = '<?= __("Pago Exitoso") ?>';
                            resultContent = `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="mb-3"><?= __("¡Su pago ha sido procesado exitosamente!") ?></h4>
                                <p>Número de autorización: ${data.authNumber}</p>
                                <p>ID de transacción: ${data.trxID}</p>
                                <p class="mt-4"><?= __("Gracias por su compra.") ?></p>
                            </div>
                        `;

                            // Clear forms
                            document.getElementById('credit-card-form').reset();
                            document.getElementById('ach-form').reset();
                        } else {
                            resultTitle = '<?= __("Error en el Pago") ?>';
                            resultContent = `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="mb-3"><?= __("Lo sentimos, ha ocurrido un error") ?></h4>
                                <p>${data.rMsg || data.error || '<?= __('No se pudo procesar su pago.') ?>'}</p>
                                <p class="mt-4"><?= __("Por favor intente nuevamente.") ?></p>
                            </div>
                        `;
                        }

                        // Update and show result modal
                        document.getElementById('result-title').textContent = resultTitle;
                        document.getElementById('payment-result-content').innerHTML = resultContent;
                        $('#paymentResultModal').modal('show');
                    })
                    .catch(error => {
                        // Hide processing modal
                        $('#processingModal').modal('hide');

                        // Show error in result modal
                        document.getElementById('result-title').textContent = 'Error';
                        document.getElementById('payment-result-content').innerHTML = `
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="mb-3"><?= __("Lo sentimos, ha ocurrido un error") ?></h4>
                            <p><?= __("No se pudo conectar con el servidor de pagos.") ?></p>
                            <p class="mt-4"><?= __("Por favor intente nuevamente.") ?></p>
                        </div>
                    `;
                        $('#paymentResultModal').modal('show');
                    });
            }

            // Result modal handler
            document.getElementById('result-ok-btn').addEventListener('click', function() {
                $('#paymentResultModal').modal('hide');

                // Reload page if payment was successful
                if (document.getElementById('result-title').textContent === 'Pago Exitoso') {
                    window.location.reload();
                }
            });
        });
    </script>
</body>

</html>