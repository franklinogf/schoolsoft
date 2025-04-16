<?php

declare(strict_types=1);

namespace App\Enums;

enum PhoneCompanyEnum: string
{
    case ATT = 'AT&T';
    case T_MOBILE = 'T-Movil';
    case SPRINT = 'Sprint';
    case OPEN_M = 'Open M.';
    case CLARO = 'Claro';
    case VERIZON = 'Verizon';
    case SUNCOM = 'Suncom';
    case BOOST = 'Boost';

    public function companyHost(): string
    {
        return match ($this->value) {
            self::ATT->value => '@txt.att.net',
            self::T_MOBILE->value => '@tmomail.net',
            self::SPRINT->value => '@messaging.sprintpcs.com',
            self::OPEN_M->value => '@email.openmobilepr.com',
            self::CLARO->value => '@mms.claropr.com',
            self::VERIZON->value => '@vtext.com',
            self::SUNCOM->value => '@tms.suncom.com',
            self::BOOST->value => '@myboostmobile.com',
        };
    }

    public function createPhoneEmail(string $phone): string
    {
        return $phone.$this->companyHost();
    }
}
