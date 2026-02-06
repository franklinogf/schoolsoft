<?php

namespace App\Enums;

use App\Models\Admin;
use App\Services\SchoolService;

enum GradePageEnum: string
{
    case GRADES = 'Notas';
    case SHORT_TESTS = 'Pruebas-Cortas';
    case DAILY_WORKS = 'Trab-Diarios';
    case NOTEBOOK_WORKS = 'Trab-Libreta';
    case CONDUCT_ATTENDANCE = 'Cond-Asis';
    case FINAL_EXAM = 'Ex-Final';
    case SUMMER_GRADES = 'V-Nota';
    case GRADES_2 = 'Notas2';
    case DAILY_WORKS_2 = 'Trab-Diarios2';
    case NOTEBOOK_WORKS_2 = 'Trab-Libreta2';

    public function getLabel(): string
    {
        return match ($this) {
            self::GRADES => __('Notas'),
            self::SHORT_TESTS => __('Pruebas Cortas'),
            self::DAILY_WORKS => __('Trabajos Diarios'),
            self::NOTEBOOK_WORKS => __('Trabajos de Libreta'),
            self::CONDUCT_ATTENDANCE => __('Conducta y Asistencia'),
            self::FINAL_EXAM => __('Examen Final'),
            self::SUMMER_GRADES => __('Notas de Verano'),
            self::GRADES_2 => __('Notas 2'),
            self::DAILY_WORKS_2 => __('Trabajos Diarios 2'),
            self::NOTEBOOK_WORKS_2 => __('Trabajos de Libreta 2'),
        };
    }

    public function getTableName(): string
    {
        return match ($this) {
            self::GRADES => 'padres',
            self::SHORT_TESTS => 'padres4',
            self::DAILY_WORKS => 'padres2',
            self::NOTEBOOK_WORKS => 'padres3',
            self::CONDUCT_ATTENDANCE => 'padres',
            self::FINAL_EXAM => 'padres',
            self::SUMMER_GRADES => 'padres',
            self::GRADES_2 => 'padres7',
            self::DAILY_WORKS_2 => 'padres5',
            self::NOTEBOOK_WORKS_2 => 'padres6',
        };
    }

    public function getShortColumn(int|TrimesterEnum $number): string
    {
        $num = $number instanceof TrimesterEnum ? $number->getNumber() : $number;
        return match ($this) {
            self::GRADES => 'not' . $num,
            self::SHORT_TESTS => 'pc' . $num,
            self::DAILY_WORKS => 'td' . $num,
            self::NOTEBOOK_WORKS => 'tl' . $num,
            self::CONDUCT_ATTENDANCE => 'ConAsis',
            self::FINAL_EXAM => 'ExF',
            self::SUMMER_GRADES => 'VNot',
            self::GRADES_2 => 'Not2',
            self::DAILY_WORKS_2 => 'NotD2',
            self::NOTEBOOK_WORKS_2 => 'NotL2',
        };
    }

    /**
     * Get array of pages based on school configuration
     */
    public static function getPages(): array
    {
        $school = Admin::primaryAdmin();
        if ($school->cppd === 'Si') {
            return [
                self::GRADES,
                self::SUMMER_GRADES,
            ];
        }

        $pages = [
            self::GRADES,
            self::SHORT_TESTS,
            self::DAILY_WORKS,
            self::NOTEBOOK_WORKS,
            self::CONDUCT_ATTENDANCE,
            self::FINAL_EXAM,
            self::SUMMER_GRADES,
        ];

        if ($school->etd === 'SI' && __ONLY_CBTM__) {
            $pages = [
                ...$pages,
                self::GRADES_2,
                self::DAILY_WORKS_2,
                self::NOTEBOOK_WORKS_2,
            ];
        } elseif (__ONLY_CBTM__) {
            $pages = [
                ...$pages,
                self::GRADES_2,
            ];
        } elseif ($school->etd === 'SI') {
            $pages = [
                ...$pages,
                self::DAILY_WORKS_2,
                self::NOTEBOOK_WORKS_2,
            ];
        }

        if (school_is('cdls')) {
            $pages = array_filter(
                $pages,
                fn($page) =>
                $page !== self::SHORT_TESTS && $page !== self::FINAL_EXAM
            );
        }

        return $pages;
    }
}
