<?php

return [
    'email' => [
        'reminder_subject' => 'Aviso de pago automático - :school',
        'reminder_title' => 'Su pago automático se realizará pronto',
        'reminder_intro' => 'El :date se cobrará automáticamente a su tarjeta :card.',
        'reminder_note' => 'El monto final puede variar si cambian sus balances antes de esa fecha.',
        'failed_subject' => 'No pudimos procesar su pago automático - :school',
        'failed_title' => 'No pudimos procesar su pago automático',
        'failed_intro' => 'El cobro a su tarjeta :card no se pudo completar.',
        'failed_retry' => 'Volveremos a intentarlo el :date.',
        'failed_final' => 'El pago automático quedó detenido. Registre una tarjeta nueva para reactivarlo.',
        'reason' => 'Motivo',
        'student' => 'Estudiante',
        'concept' => 'Concepto',
        'amount' => 'Monto',
        'estimated_total' => 'Total estimado',
        'manage' => 'Administrar pago automático',
        'footer' => 'Este es un mensaje automático de :school.',
    ],
];
