<?php
require_once __DIR__ . '/../../../../app.php';

use App\Enums\PaymentType;
use App\Models\Store;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$storeId = $_GET['store_id'] ?? null;

if (!$storeId) Route::redirect('/access/stores/index.php');

$store = Store::with('items')->find($storeId);

if (!$store) Route::redirect('/access/stores/index.php');

$students = Student::orderBy('apellidos')->orderBy('nombre')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Nueva Orden') . ' - ' . $store->name;
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
    <style>
        .item-row {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }

        .item-row .form-group {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5">
        <h1 class="text-center mb-4 mt-5"><?= __('Nueva Orden') ?> - <?= $store->name ?></h1>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <a class="btn btn-outline-secondary mb-3" href="./index.php?store_id=<?= $store->id ?>">
                    <?= __('Volver a Órdenes') ?>
                </a>

                <form id="orderForm" method="POST" action="./includes/store.php">
                    <input type="hidden" name="store_id" value="<?= $store->id ?>">
                    <input type="hidden" name="store_prefix" value="<?= $store->prefix_code ?>">
                    <input type="hidden" name="customerEmail" id="finalEmail">

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><?= __('Información del Cliente') ?></h5>
                        </div>
                        <div class="card-body">
                            <!-- Student Selector -->
                            <div class="form-group">
                                <label for="studentSelect">
                                    <?= __('Estudiante') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control selectpicker w-100"
                                    id="studentSelect"
                                    name="accountID"
                                    data-live-search="true"
                                    required>
                                    <option value=""><?= __('Seleccionar estudiante') ?></option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student->id ?>"
                                            data-ss="<?= $student->ss ?>">
                                            <?= "{$student->apellidos} {$student->nombre} ({$student->id})" ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="form-text text-muted">
                                    <?= __('Busque por nombre, apellido o ID') ?>
                                </small>
                            </div>

                            <!-- Customer Name Selector -->
                            <div class="form-group" id="nameSection" style="display:none;">
                                <label><?= __('Nombre del Cliente') ?> <span class="text-danger">*</span></label>

                                <div id="nameOptions">
                                    <!-- Radio buttons will be populated by JavaScript -->
                                </div>

                                <div class="form-check mt-2">
                                    <input type="radio" class="form-check-input" name="nameOption"
                                        id="nameOther" value="custom">
                                    <label class="form-check-label" for="nameOther">
                                        <?= __('Otro nombre') ?>
                                    </label>
                                </div>

                                <input type="text"
                                    class="form-control mt-2"
                                    name="customName"
                                    id="customName"
                                    style="display:none;"
                                    placeholder="<?= __('Ingrese nombre del cliente') ?>">

                                <!-- Hidden field for final name value -->
                                <input type="hidden" name="customerName" id="finalName">
                            </div>

                            <!-- Email Selector (populated dynamically) -->
                            <div class="form-group" id="emailSection" style="display:none;">
                                <label><?= __('Correo electrónico') ?> <span class="text-danger">*</span></label>

                                <div id="emailOptions">
                                    <!-- Radio buttons will be populated by JavaScript -->
                                </div>

                                <div class="form-check mt-2">
                                    <input type="radio" class="form-check-input" name="emailOption"
                                        id="emailOther" value="custom">
                                    <label class="form-check-label" for="emailOther">
                                        <?= __('Otro correo') ?>
                                    </label>
                                </div>

                                <input type="email"
                                    class="form-control mt-2"
                                    name="customEmail"
                                    id="customEmail"
                                    style="display:none;"
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
                                    placeholder="correo@ejemplo.com">
                            </div>

                            <!-- Delivery To (Student Selector) -->
                            <div class="form-group" id="deliverySection" style="display:none;">
                                <label for="deliveryTo"><?= __('Entregar a') ?></label>
                                <select class="form-control selectpicker w-100"
                                    id="deliveryTo"
                                    name="deliveryTo"
                                    data-live-search="true">
                                    <option value=""><?= __('Seleccionar estudiante (opcional)') ?></option>
                                </select>
                                <small class="form-text text-muted">
                                    <?= __('Opcional - Seleccione el estudiante a quien se le entregará el pedido') ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><?= __('Artículos') ?></h5>
                        </div>
                        <div class="card-body">
                            <div id="itemsContainer">
                                <!-- Items will be added here -->
                            </div>

                            <button type="button" class="btn btn-outline-success btn-sm" id="addItemBtn">
                                <i class="fa fa-plus"></i> <?= __('Agregar Artículo') ?>
                            </button>
                        </div>
                    </div>

                    <!-- Payment & Totals -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><?= __('Pago y Totales') ?></h5>
                        </div>
                        <div class="card-body">
                            <!-- Payment Type -->
                            <div class="form-group">
                                <label for="payment_type">
                                    <?= __('Método de Pago') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="payment_type" id="payment_type" required>
                                    <option value=""><?= __('Seleccionar método') ?></option>
                                    <?php foreach (PaymentType::cases() as $paymentType): ?>
                                        <option value="<?= $paymentType->value ?>">
                                            <?= $paymentType->label() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- IVU (Optional) -->
                            <div class="form-group">
                                <label for="ivu"><?= __('IVU (Impuesto)') ?></label>
                                <input type="number"
                                    class="form-control"
                                    id="ivu"
                                    name="ivu"
                                    step="0.01"
                                    min="0"
                                    value="0.00"
                                    placeholder="0.00">
                                <small class="form-text text-muted">
                                    <?= __('Opcional - dejar en 0 si no aplica') ?>
                                </small>
                            </div>

                            <!-- Order Summary -->
                            <div class="mt-4 p-3 border rounded bg-light">
                                <h5><?= __('Resumen de Orden') ?></h5>
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th><?= __('Subtotal') ?>:</th>
                                        <td class="text-right" id="displaySubtotal">$0.00</td>
                                    </tr>
                                    <tr id="ivuRow" style="display:none;">
                                        <th><?= __('IVU') ?>:</th>
                                        <td class="text-right" id="displayIvu">$0.00</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <th><?= __('Total') ?>:</th>
                                        <td class="text-right" id="displayTotal">$0.00</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-5">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> <?= __('Crear Orden') ?>
                        </button>
                        <a href="./index.php?store_id=<?= $store->id ?>" class="btn btn-secondary btn-lg">
                            <?= __('Cancelar') ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>

    <script>
        const storeItems = <?= json_encode($store->items) ?>;
        let itemCounter = 0;

        $(document).ready(function() {
            // Initialize Bootstrap-Select
            $('.selectpicker').selectpicker();

            // Student selection handler
            $('#studentSelect').on('changed.bs.select', function() {
                const studentId = $(this).val();

                if (studentId) {
                    // Fetch student info
                    $.ajax({
                        url: './includes/get-student-info.php',
                        method: 'GET',
                        data: {
                            student_id: studentId
                        },
                        dataType: 'json',
                        success: function(data) {
                            // Populate name options
                            let nameHTML = '';
                            let firstName = true;

                            if (data.family.madre && data.family.madre !== '') {
                                nameHTML += `
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="nameOption" 
                                           id="nameMadre" value="predefined_m" ${firstName ? 'checked' : ''}>
                                    <label class="form-check-label" for="nameMadre">
                                        <?= __("Madre") ?>: ${data.family.madre}
                                    </label>
                                </div>
                            `;
                                firstName = false;
                            }

                            if (data.family.padre && data.family.padre !== '') {
                                nameHTML += `
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="nameOption" 
                                           id="namePadre" value="predefined_p" ${firstName ? 'checked' : ''}>
                                    <label class="form-check-label" for="namePadre">
                                        <?= __("Padre") ?>: ${data.family.padre}
                                    </label>
                                </div>
                            `;
                                firstName = false;
                            }

                            if (data.studentName && data.studentName !== '') {
                                nameHTML += `
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="nameOption" 
                                           id="nameStudent" value="predefined_s" ${firstName ? 'checked' : ''}>
                                    <label class="form-check-label" for="nameStudent">
                                        <?= __("Estudiante") ?>: ${data.studentName}
                                    </label>
                                </div>
                            `;
                                firstName = false;
                            }

                            $('#nameOptions').html(nameHTML);
                            $('#nameSection').show();

                            // If no names, force custom
                            if (firstName) {
                                $('#nameOther').prop('checked', true);
                                $('#customName').show().prop('required', true);
                            }

                            // Update final name
                            updateFinalName();

                            // Populate email options
                            let emailHTML = '';
                            let firstEmail = true;

                            if (data.family.email_m && data.family.email_m !== '') {
                                emailHTML += `
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="emailOption" 
                                           id="emailM" value="predefined_m" ${firstEmail ? 'checked' : ''}>
                                    <label class="form-check-label" for="emailM">
                                        ${data.family.madre ? data.family.madre + ': ' : '<?= __("Madre") ?>: '}${data.family.email_m}
                                    </label>
                                </div>
                            `;
                                firstEmail = false;
                            }

                            if (data.family.email_p && data.family.email_p !== '') {
                                emailHTML += `
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="emailOption" 
                                           id="emailP" value="predefined_p" ${firstEmail ? 'checked' : ''}>
                                    <label class="form-check-label" for="emailP">
                                        ${data.family.padre ? data.family.padre + ': ' : '<?= __("Padre") ?>: '}${data.family.email_p}
                                    </label>
                                </div>
                            `;
                                firstEmail = false;
                            }

                            $('#emailOptions').html(emailHTML);
                            $('#emailSection').show();

                            // If no emails, force custom
                            if (firstEmail) {
                                $('#emailOther').prop('checked', true);
                                $('#customEmail').show().prop('required', true);
                            }

                            // Update final email
                            updateFinalEmail();

                            // Populate delivery students (siblings)
                            if (data.siblings && data.siblings.length > 0) {
                                let deliveryHTML = '<option value=""><?= __("Seleccionar estudiante (opcional)") ?></option>';
                                data.siblings.forEach(sibling => {
                                    deliveryHTML += `<option value="${sibling.ss}">${sibling.apellidos} ${sibling.nombre} (${sibling.grado})</option>`;
                                });
                                $('#deliveryTo').html(deliveryHTML);
                                $('#deliveryTo').selectpicker('refresh');
                                $('#deliverySection').show();
                            } else {
                                $('#deliverySection').hide();
                            }
                        },
                        error: function() {
                            alert('<?= __("Error al cargar información del estudiante") ?>');
                        }
                    });
                } else {
                    $('#nameSection').hide();
                    $('#emailSection').hide();
                    $('#deliverySection').hide();
                }
            });

            // Name option change handler
            $(document).on('change', 'input[name="nameOption"]', function() {
                if ($(this).val() === 'custom') {
                    $('#customName').show().prop('required', true);
                } else {
                    $('#customName').hide().prop('required', false).val('');
                }
                updateFinalName();
            });

            // Custom name input handler
            $('#customName').on('input', updateFinalName);

            // Update hidden name field
            function updateFinalName() {
                const option = $('input[name="nameOption"]:checked').val();
                let name = '';

                const studentId = $('#studentSelect').val();
                if (!studentId) return;

                if (option === 'custom') {
                    name = $('#customName').val();
                } else {
                    const labelText = $('input[name="nameOption"]:checked').next('label').text();
                    // Extract name after colon
                    const nameParts = labelText.split(': ');
                    if (nameParts.length > 1) {
                        name = nameParts[1].trim();
                    }
                }

                $('#finalName').val(name);
            }

            // Email option change handler
            $(document).on('change', 'input[name="emailOption"]', function() {
                if ($(this).val() === 'custom') {
                    $('#customEmail').show().prop('required', true);
                } else {
                    $('#customEmail').hide().prop('required', false).val('');
                }
                updateFinalEmail();
            });

            // Custom email input handler
            $('#customEmail').on('input', updateFinalEmail);

            // Update hidden email field
            function updateFinalEmail() {
                const option = $('input[name="emailOption"]:checked').val();
                let email = '';

                const studentId = $('#studentSelect').val();
                if (!studentId) return;

                if (option === 'custom') {
                    email = $('#customEmail').val();
                } else {
                    const labelText = $('input[name="emailOption"]:checked').next('label').text();
                    const emailMatch = labelText.match(/[\w.-]+@[\w.-]+\.\w+/);
                    if (emailMatch) {
                        email = emailMatch[0];
                    }
                }

                $('#finalEmail').val(email);
            }

            // Add item button
            $('#addItemBtn').click(function() {
                addItemRow();
            });

            // Add item row
            function addItemRow(itemData = null) {
                itemCounter++;
                const rowId = `item-${itemCounter}`;

                let optionsHTML = '<option value=""><?= __("Seleccionar artículo") ?></option>';
                storeItems.forEach(item => {
                    optionsHTML += `<option value="${item.id}" 
                    data-price="${item.price}" 
                    data-buy-multiple="${item.buy_multiple}"
                    data-options='${JSON.stringify(item.options || [])}'>${item.name}</option>`;
                });

                const rowHTML = `
                <div class="item-row" id="${rowId}">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label><?= __('Artículo') ?></label>
                                <select class="form-control item-select" name="items[${itemCounter}][item_id]" required>
                                    ${optionsHTML}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __('Opción') ?></label>
                                <select class="form-control option-select" name="items[${itemCounter}][option]" disabled>
                                    <option value=""><?= __('Sin opciones') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?= __('Cantidad') ?></label>
                                <input type="number" class="form-control quantity-input" 
                                       name="items[${itemCounter}][quantity]" 
                                       min="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?= __('Precio') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="text" class="form-control item-price" readonly value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fa fa-trash"></i> <?= __('Eliminar') ?>
                    </button>
                </div>
            `;

                $('#itemsContainer').append(rowHTML);
            }

            // Item selection change
            $(document).on('change', '.item-select', function() {
                const $row = $(this).closest('.item-row');
                const $option = $(this).find('option:selected');
                const itemId = $(this).val();

                if (!itemId) {
                    $row.find('.option-select').prop('disabled', true).html('<option value=""><?= __("Sin opciones") ?></option>');
                    $row.find('.quantity-input').val(1).prop('max', '');
                    $row.find('.item-price').val('0.00');
                    return;
                }

                const price = parseFloat($option.data('price'));
                const buyMultiple = $option.data('buy-multiple');
                const options = $option.data('options') || [];

                // Handle options
                if (options.length > 0) {
                    let optHTML = '<option value=""><?= __("Seleccionar opción") ?></option>';
                    options.forEach(opt => {
                        const optPrice = opt.price !== null ? opt.price : price;
                        optHTML += `<option value="${opt.name}" data-price="${optPrice}">${opt.name} - $${parseFloat(optPrice).toFixed(2)}</option>`;
                    });
                    $row.find('.option-select').prop('disabled', false).html(optHTML).prop('required', true);
                    $row.find('.item-price').val('0.00');
                } else {
                    $row.find('.option-select').prop('disabled', true).html('<option value=""><?= __("Sin opciones") ?></option>').prop('required', false);
                    $row.find('.item-price').val(price.toFixed(2));
                    updateTotals();
                }

                // Handle quantity
                if (!buyMultiple) {
                    $row.find('.quantity-input').val(1).prop('max', 1);
                } else {
                    $row.find('.quantity-input').prop('max', '');
                }
            });

            // Option selection change
            $(document).on('change', '.option-select', function() {
                const $row = $(this).closest('.item-row');
                const $option = $(this).find('option:selected');
                const price = parseFloat($option.data('price')) || 0;

                $row.find('.item-price').val(price.toFixed(2));
                updateTotals();
            });

            // Quantity change
            $(document).on('input', '.quantity-input', function() {
                updateTotals();
            });

            // IVU change
            $('#ivu').on('input', function() {
                updateTotals();
            });

            // Remove item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                updateTotals();
            });

            // Update totals
            function updateTotals() {
                let subtotal = 0;

                $('.item-row').each(function() {
                    const price = parseFloat($(this).find('.item-price').val()) || 0;
                    const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
                    subtotal += price * quantity;
                });

                const ivu = parseFloat($('#ivu').val()) || 0;
                const total = subtotal + ivu;

                $('#displaySubtotal').text('$' + subtotal.toFixed(2));
                $('#displayIvu').text('$' + ivu.toFixed(2));
                $('#displayTotal').text('$' + total.toFixed(2));

                if (ivu > 0) {
                    $('#ivuRow').show();
                } else {
                    $('#ivuRow').hide();
                }
            }

            // Form validation
            $('#orderForm').on('submit', function(e) {
                if ($('.item-row').length === 0) {
                    e.preventDefault();
                    alert('<?= __("Debe agregar al menos un artículo") ?>');
                    return false;
                }

                const name = $('#finalName').val();
                if (!name || name.trim() === '') {
                    e.preventDefault();
                    alert('<?= __("Debe seleccionar o ingresar un nombre del cliente") ?>');
                    return false;
                }

                const email = $('#finalEmail').val();
                if (!email || email.trim() === '') {
                    e.preventDefault();
                    alert('<?= __("Debe seleccionar o ingresar un correo electrónico") ?>');
                    return false;
                }

                return true;
            });
        });
    </script>
</body>

</html>