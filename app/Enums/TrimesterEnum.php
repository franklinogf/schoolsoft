<?php

namespace App\Enums;

enum TrimesterEnum: string
{
    case FIRST = 'Trimestre-1';
    case SECOND = 'Trimestre-2';
    case THIRD = 'Trimestre-3';
    case FOURTH = 'Trimestre-4';
    case SUMMER = 'Verano';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIRST => 'Trimestre 1',
            self::SECOND => 'Trimestre 2',
            self::THIRD => 'Trimestre 3',
            self::FOURTH => 'Trimestre 4',
            self::SUMMER => 'Verano',
        };
    }

    public function getNumber(): int
    {
        return match ($this) {
            self::FIRST => 1,
            self::SECOND => 2,
            self::THIRD => 3,
            self::FOURTH => 4,
            self::SUMMER => 5,
        };
    }

    /**
     * @return list<int>
     */
    public function getGradesNumbersColumn(GradePageEnum $report, bool $cppd = false): array
    {
        if ($report === GradePageEnum::CONDUCT_ATTENDANCE || $report === GradePageEnum::FINAL_EXAM) {
            return [];
        }
        return match ($this) {
            self::FIRST => $cppd ? [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] : [1, 2, 3, 4, 5, 6, 7, 8, 9],
            self::SECOND => $cppd ? [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24] : [11, 12, 13, 14, 15, 16, 17, 18, 19],
            self::THIRD => $cppd ? [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36] : [21, 22, 23, 24, 25, 26, 27, 28, 29],
            self::FOURTH => $cppd ? [37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48] : [31, 32, 33, 34, 35, 36, 37, 38, 39],
            self::SUMMER => [1, 2, 3, 4, 5, 6, 7],
        };
    }

    /**
     * @return array<int, string>    
     */
    public function getDateColumns(): array
    {
        return match ($this) {
            self::FIRST => ['ft1', 'ft2'],
            self::SECOND => ['ft3', 'ft4'],
            self::THIRD => ['ft5', 'ft6'],
            self::FOURTH => ['ft7', 'ft8'],
            self::SUMMER => ['fechav1', 'fechav2'],
        };
    }

    public function getEndColumn(): ?string
    {
        return match ($this) {
            self::FIRST => 'sie1',
            self::SECOND => 'sie2',
            self::THIRD => 'sie3',
            self::FOURTH => 'sie4',
            self::SUMMER => null,
        };
    }

    public function getColumns(GradePageEnum $report): array
    {
        $colNumber = $this->getNumber();
        return match ($report) {
            GradePageEnum::GRADES, GradePageEnum::GRADES_2 => $this->generateGradeColumns((string)$colNumber),
            GradePageEnum::DAILY_WORKS, GradePageEnum::DAILY_WORKS_2, GradePageEnum::NOTEBOOK_WORKS, GradePageEnum::NOTEBOOK_WORKS_2, GradePageEnum::SHORT_TESTS => $this->generateDHColumns((string)$colNumber),
            GradePageEnum::CONDUCT_ATTENDANCE => $this->generateAttendanceColumns((string)$colNumber),
            GradePageEnum::FINAL_EXAM => \in_array($colNumber, [2, 4]) ? [$this->generateColumn(__('Nota Ex.Final'), 'ex' . $colNumber)] : [],
            default => [],
        };
    }
    private function generateColumn(string $header, string $col, bool $readonly = false, ?string $class = null, ?string $text = null): array
    {
        return [
            'header' => $header,
            'column' => $col,
            'readonly' => $readonly,
            'class' => $class,
            'text' => $text,
        ];
    }

    private function generateAttendanceColumns(string $colNumber): array
    {
        return [
            $this->generateColumn(__('Conducta'), 'con' . $colNumber),
            $this->generateColumn(__('Ausencias'), 'aus' . $colNumber),
            $this->generateColumn(__('Tardanzas'), 'tar' . $colNumber),
            $this->generateColumn(__('Deméritos'), 'de' . $colNumber),
        ];
    }

    private function generateGradeColumns(string $colNumber, ?string $bonusCol = null): array
    {
        $bonus = $bonusCol ?? 'not' . $colNumber . '0';
        $cols = [
            $this->generateColumn(__('Bono'), $bonus, class: 'bonus'),
        ];

        if (__ONLY_CBTM__) {
            $cols[] = $this->generateColumn(__('Promedio'), 'average' . $colNumber, true, 'average', '60%');
        }

        return [
            ...$cols,
            $this->generateColumn(__('T-Diario'), 'td' . $colNumber, true, 'tdia', __ONLY_CBTM__ ? '10%' : null),
            $this->generateColumn(__('T-Libreta'), 'tl' . $colNumber, true, 'tlib', __ONLY_CBTM__ ? '10%' : null),
            $this->generateColumn(__('P-Cor'), 'pc' . $colNumber, true, 'pcor', __ONLY_CBTM__ ? '20%' : null),
            $this->generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
            $this->generateColumn(__('TDP'), 'por' . $colNumber, true, 'tdp'),
            $this->generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
        ];
    }

    private function generateDHColumns(string $colNumber): array
    {
        $bonus = 'not' . $colNumber . '0';
        return [
            $this->generateColumn(__('Nota') . ' 10', $bonus, false, 'grade'),
            $this->generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
            $this->generateColumn(__('TDP'), 'por' . $colNumber, true, 'tdp'),
            $this->generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
        ];
    }
}
