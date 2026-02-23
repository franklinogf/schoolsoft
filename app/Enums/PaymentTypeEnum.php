<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case CASH = '1';
    case CHECK = '2';
    case ATH = '3';
    case CREDIT_CARD = '4';
    case GIRO = '5';
    case NOMINA = '6';
    case BANK = '7';
    case DIRECT_PAYMENT = '8';
    case TELE_PAGO = '9';
    case PAYPAL = '10';
    case BECA = '11';
    case ATH_MOVIL = '12';
    case ACCOUNT_CREDIT = '13';
    case VIRTUAL_TERMINAL = '14';
    case ACUDEN_CONTIGO = '15';
    case ACUDEN_VALES = '16';
    case VA_PROG = '17';
    case SCHOOL = '18';
    case PAGO_APP = '19';


    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::CHECK => 'Cheque',
            self::ATH => 'ATH',
            self::CREDIT_CARD => 'Tarjeta Crédito',
            self::GIRO => 'Giro',
            self::NOMINA => 'Nomina',
            self::BANK => 'Banco',
            self::DIRECT_PAYMENT => 'Pago Directo',
            self::TELE_PAGO => 'Tele Pago',
            self::PAYPAL => 'Paypal',
            self::BECA => 'Beca',
            self::ATH_MOVIL => 'ATH Movil',
            self::ACCOUNT_CREDIT => 'Credito a Cuenta',
            self::VIRTUAL_TERMINAL => 'Virtual Terminal',
            self::ACUDEN_CONTIGO => 'Acuden-Contigo',
            self::ACUDEN_VALES => 'Acuden-Vales',
            self::VA_PROG => 'VA Prog',
            self::SCHOOL => 'Colegio',
            self::PAGO_APP => 'Pago APP',
        };
    }
}
