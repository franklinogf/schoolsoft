<?php

return [
    'status' => [
        'approved' => 'Aprobado',
        'failed' => 'Falló',
        'approved_partial' => 'Aprobado parcialmente',
        'rejected' => 'Rechazado',
        'pending' => 'Pendiente',
        'pending_validation' => 'Pendiente de validación',
        'refunded' => 'Reembolsado',
        'unknown' => 'Desconocido',
    ],
    'form' => [
        'last_name' => 'Apellido',
        'accept_terms' => 'Acepto los',
        'terms_link' => 'términos y condiciones',
        'pay_with_placetopay' => 'Pagar con PlacetoPay',
        'pay' => 'Pagar',
        'processing' => 'Procesando...',
        'redirect_notice' => 'Será redirigido a PlacetoPay para completar el pago de forma segura.',
    ],
    'validation' => [
        'terms' => 'Debe aceptar los términos y condiciones para continuar.',
        'first_name' => 'El nombre es obligatorio.',
        'last_name' => 'El apellido es obligatorio.',
        'first_name_chars' => 'El nombre solo puede contener letras y espacios.',
        'last_name_chars' => 'El apellido solo puede contener letras y espacios.',
        'email' => 'Por favor introduzca un correo electrónico válido.',
        'mobile' => 'Por favor introduzca un número de celular válido (10 dígitos).',
    ],
    'errors' => [
        'session_not_found' => 'No se encontró la sesión de pago.',
        'start_failed' => 'No se pudo iniciar el pago.',
        'invalid_deposit' => 'Datos de depósito inválidos.',
        'invalid_payment' => 'Datos de pago inválidos.',
        'pending_blocked' => 'Tiene una transacción pendiente (referencia :reference). Espere a que se resuelva antes de realizar un nuevo pago.',
    ],
    'pending' => [
        'warning' => 'Tiene una transacción pendiente (referencia :reference). Espere a que se resuelva antes de realizar un nuevo pago para evitar cobros dobles.',
        'view_detail' => 'Ver detalle',
    ],
    'deposit' => [
        'title' => 'Depósito Cafetería',
        'select_student' => 'Seleccionar el estudiante al que se le quiere hacer el depósito',
        'select_student_error' => 'Debe seleccionar un estudiante para realizar el depósito.',
        'details' => 'Detalles del depósito',
        'history_link' => 'Ver historial de depósitos',
        'amount' => 'Cantidad a depositar',
        'amount_to' => 'a :name',
        'min_amount' => 'La cantidad mínima es de :amount',
        'min_amount_invalid' => 'Por favor introduzca una cantidad igual o mayor a :amount',
        'success' => '¡Depósito exitoso!',
        'new_balance' => 'El nuevo saldo es :amount',
        'failed' => 'No se pudo procesar el depósito.',
    ],
    'result' => [
        'title' => 'Resumen del pago',
        'transaction' => 'Transacción',
        'concept' => 'Concepto',
        'amount' => 'Monto',
        'pending_notice' => 'Su pago está pendiente de confirmación por la entidad financiera. Le notificaremos cuando cambie de estado; no realice el pago nuevamente.',
        'resume_notice' => 'Su pago aún no se ha completado. Si salió de la página de pago por error, puede volver a ella y terminar el pago.',
        'resume' => 'Continuar con el pago',
    ],
    'history' => [
        'title' => 'Historial de pagos',
        'link' => 'Ver historial de pagos',
        'subtitle' => 'Pagos realizados en línea',
        'empty' => 'No tienes pagos registrados.',
    ],
    'terms' => [
        'title' => 'Términos y condiciones',
        'intro' => 'Estos términos regulan los pagos en línea (depósitos de cafetería y compras en tiendas) que se realizan a :school a través de este portal.',
        'processing' => [
            'title' => '1. Procesamiento del pago',
            'body' => 'Los pagos son procesados por PlacetoPay (Evertec). Al continuar será redirigido a su plataforma segura; la escuela no recibe ni almacena los datos de su tarjeta o cuenta bancaria.',
        ],
        'buyer' => [
            'title' => '2. Datos del comprador',
            'body' => 'Para procesar el pago se envían a PlacetoPay su nombre, apellido, correo electrónico y número de celular. Usted es responsable de que esta información sea correcta.',
        ],
        'expiration' => [
            'title' => '3. Tiempo para completar el pago',
            'body' => 'La sesión de pago expira a los :minutes minutos. Si no completa el pago en ese tiempo deberá iniciarlo nuevamente.',
        ],
        'pending' => [
            'title' => '4. Pagos pendientes',
            'body' => 'Algunos pagos (por ejemplo ACH) pueden quedar pendientes de confirmación por la entidad financiera. Mientras tenga un pago pendiente no podrá iniciar uno nuevo, para evitar cobros dobles. El estado se actualiza automáticamente.',
        ],
        'crediting' => [
            'title' => '5. Acreditación',
            'body' => 'Los depósitos de cafetería se acreditan al balance del estudiante y las compras se marcan como pagadas solamente cuando el pago es aprobado. Puede consultar el estado de todos sus pagos en el historial de pagos.',
        ],
        'refunds' => [
            'title' => '6. Reembolsos y reversos',
            'body' => 'Las solicitudes de reembolso deben dirigirse a la administración de la escuela. Cuando un pago es reembolsado o reversado, el monto se descuenta del balance de cafetería del estudiante o la compra se marca como no pagada.',
        ],
        'privacy' => [
            'title' => '7. Privacidad',
            'body' => 'La información suministrada se utiliza únicamente para procesar y registrar sus pagos, y no se comparte con terceros distintos al procesador de pagos.',
        ],
        'contact' => [
            'title' => '8. Contacto',
            'body' => 'Para preguntas sobre sus pagos escriba a :email.',
        ],
    ],
];
