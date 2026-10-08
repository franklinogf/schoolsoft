<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Family;
use App\Models\Store;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();

// Get store ID from query parameter
$store_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$store = Store::find($store_id);

// Include cart actions
require_once 'includes/cart_actions.php';

$status = $_GET['status'] ?? null;
$statusMessage = $_GET['message'] ?? null;
$statusOrder = $_GET['order'] ?? null;

$family = Family::find(Session::id());
$defaultMobile = $family ? ($family->cel_m ?: $family->cel_p) : '';

// Re-checks against PlacetoPay, so a resolved payment doesn't keep warning
$pendingPayment = (new PlacetoPayPaymentProcessor())->pendingFor(Session::id());
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

        <?php if ($pendingPayment): ?>
            <div class="alert alert-warning">
                <?= __('placetopay.pending.warning', ['reference' => $pendingPayment->reference]) ?>
                <a href="../placetopay/result.php?reference=<?= urlencode($pendingPayment->reference) ?>" class="alert-link"><?= __('placetopay.pending.view_detail') ?></a>
            </div>
        <?php endif; ?>

        <div class="text-right small mb-2">
            <a href="../placetopay/history.php"><?= __('placetopay.history.link') ?> &raquo;</a>
        </div>

        <div class="row" id="store">
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
                                    $options = $product->options;;

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
                                                            <select required class="form-control" id="option_<?= $product->id ?>" name="option_index">
                                                                <option value=""><?= __("Seleccionar opción") ?></option>
                                                                <?php foreach ($options as $index => $option):
                                                                    // Determine the price to display
                                                                    $option_price = !is_null($option->price) ? $option->price : $product->price;
                                                                    $display_price = "$" . number_format($option_price, 2);
                                                                ?>
                                                                    <option value="<?= $index ?>">
                                                                        <?= htmlspecialchars($option->name) ?>
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
                                <h5 id="cart-total">$<?= number_format($total, 2) ?></h5>
                            </div>

                            <button type="button" class="btn btn-success btn-block mt-3" id="checkout-btn" data-toggle="modal" data-target="#paymentModal">
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
        <div class="modal-dialog" role="document">
            <form class="modal-content" id="paymentForm" method="post" action="includes/start.php">
                <input type="hidden" name="store_id" value="<?= $store->id ?>">
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first-name"><?= __("Nombre") ?></label>
                            <input type="text" class="form-control" id="first-name" name="first_name" pattern="[\p{L} ]+" title="<?= __('placetopay.validation.first_name_chars') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last-name"><?= __("Apellidos") ?></label>
                            <input type="text" class="form-control" id="last-name" name="last_name" pattern="[\p{L} ]+" title="<?= __('placetopay.validation.last_name_chars') ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email"><?= __("Correo electrónico") ?></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile"><?= __("Celular") ?></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars((string) $defaultMobile) ?>" placeholder="7875551234" pattern="\+?[\d\s\(\)\-]{10,20}" title="<?= __('placetopay.validation.mobile') ?>" required>
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1" required>
                        <label class="custom-control-label" for="terms">
                            <?= __('placetopay.form.accept_terms') ?> <a href="../placetopay/terms.php" target="_blank" rel="noopener"><?= __('placetopay.form.terms_link') ?></a>
                        </label>
                    </div>
                    <small class="text-muted"><?= __('placetopay.form.redirect_notice') ?></small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-block" id="pay-btn"><?= __('placetopay.form.pay') ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
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

                document.getElementById('cart-total').textContent = '$' + total.toFixed(2);
            }
        });
    </script>

    <script>
        document.getElementById('checkout-btn')?.addEventListener('click', function() {
            document.getElementById('payment-amount').textContent = document.getElementById('cart-total').textContent;
        });

        // Avoid double requests while PlacetoPay takes time to answer
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            if (this.dataset.submitted) {
                event.preventDefault();
                return;
            }
            this.dataset.submitted = '1';
            const button = document.getElementById('pay-btn');
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + <?= json_encode(__('placetopay.form.processing')) ?>;
        });

        // Re-enable the form if the page is restored from the back/forward cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

    <?php if ($status === 'success'): ?>
        <script>
            Alert.fire(<?= json_encode(__("Pago Exitoso")) ?>, <?= json_encode(__("¡Su pago ha sido procesado exitosamente!") . ($statusOrder ? ' ' . __("Referencia") . ': ' . $statusOrder : '')) ?>, 'success')
        </script>
    <?php elseif ($status === 'error'): ?>
        <script>
            Alert.fire(<?= json_encode(__("Error en el Pago")) ?>, <?= json_encode($statusMessage ?? __('No se pudo procesar su pago.')) ?>, 'error')
        </script>
    <?php endif; ?>

    <?php if ($status): ?>
        <script>
            // Drop status/message/order from the URL so the alert isn't shown
            // again on reload or after a cart form posts back to this page.
            history.replaceState(null, '', <?= json_encode('store.php?id=' . $store->id) ?>);
        </script>
    <?php endif; ?>
</body>

</html>