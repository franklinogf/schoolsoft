<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\Family;
use App\Models\Infirmary;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class ContactsReportPdf extends PDF implements PdfInterface
{
    private Student $student;
    private ?Family $family;
    private ?Infirmary $infirmary;

    public function __construct(Student $student)
    {
        parent::__construct('P', 'mm', 'Letter');

        $this->student = $student;
        $this->family = $student->family;
        $this->infirmary = $student->infirmary;

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Contactos de Enfermería') . ' - ' . $student->fullName, true);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderStudentInfo();
        $this->renderParentContacts();
        $this->renderEmergencyContacts();
        $this->renderMedicalInfo();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('CONTACTOS DE ENFERMERÍA'), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderStudentInfo(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información del Estudiante'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Nombre') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->fullName, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Grado') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->grado, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Fecha de Nacimiento') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $birthDate = $this->student->fecha ? $this->formatDate($this->student->fecha) : '';
        $this->Cell(90, 5, $birthDate, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Género') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->genero ?? '', 0, 1);

        $this->Ln(3);
    }

    private function renderParentContacts(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Contactos de los Padres'), 0, 1, 'L', true);
        $this->Ln(1);

        // Mother
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('Madre'), 0, 1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Nombre') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->madre ?? '', 0, 1);

        $this->Cell(50, 5, __('Teléfono del Trabajo') . ':', 0, 0);
        $this->Cell(50, 5, $this->family->tel_trabajo_m ?? '', 0, 0);
        $this->Cell(30, 5, __('Celular') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->celular_m ?? '', 0, 1);

        $this->Cell(50, 5, __('Email') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->email_m ?? '', 0, 1);
        $this->Ln(2);

        // Father
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('Padre'), 0, 1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Nombre') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->padre ?? '', 0, 1);

        $this->Cell(50, 5, __('Teléfono del Trabajo') . ':', 0, 0);
        $this->Cell(50, 5, $this->family->tel_trabajo_p ?? '', 0, 0);
        $this->Cell(30, 5, __('Celular') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->celular_p ?? '', 0, 1);

        $this->Cell(50, 5, __('Email') . ':', 0, 0);
        $this->Cell(0, 5, $this->family->email_p ?? '', 0, 1);

        $this->Ln(3);
    }

    private function renderEmergencyContacts(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Contactos de Emergencia'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);

        // Emergency contact 1
        if (!empty($this->family->emer1)) {
            $this->Cell(50, 5, __('Contacto 1') . ':', 0, 0);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 5, $this->family->emer1, 0, 1);
            $this->SetFont('Arial', '', 9);

            $this->Cell(50, 5, __('Teléfono') . ':', 0, 0);
            $this->Cell(50, 5, $this->family->tel_emer1 ?? '', 0, 0);
            $this->Cell(30, 5, __('Relación') . ':', 0, 0);
            $this->Cell(0, 5, $this->family->rel_emer1 ?? '', 0, 1);
            $this->Ln(2);
        }

        // Emergency contact 2
        if (!empty($this->family->emer2)) {
            $this->Cell(50, 5, __('Contacto 2') . ':', 0, 0);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 5, $this->family->emer2, 0, 1);
            $this->SetFont('Arial', '', 9);

            $this->Cell(50, 5, __('Teléfono') . ':', 0, 0);
            $this->Cell(50, 5, $this->family->tel_emer2 ?? '', 0, 0);
            $this->Cell(30, 5, __('Relación') . ':', 0, 0);
            $this->Cell(0, 5, $this->family->rel_emer2 ?? '', 0, 1);
            $this->Ln(2);
        }

        // Emergency contact 3
        if (!empty($this->family->emer3)) {
            $this->Cell(50, 5, __('Contacto 3') . ':', 0, 0);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 5, $this->family->emer3, 0, 1);
            $this->SetFont('Arial', '', 9);

            $this->Cell(50, 5, __('Teléfono') . ':', 0, 0);
            $this->Cell(50, 5, $this->family->tel_emer3 ?? '', 0, 0);
            $this->Cell(30, 5, __('Relación') . ':', 0, 0);
            $this->Cell(0, 5, $this->family->rel_emer3 ?? '', 0, 1);
        }

        $this->Ln(3);
    }

    private function renderMedicalInfo(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información Médica Importante'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);

        // Health conditions
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Condiciones de Salud') . ':', 0, 1);
        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->cond_salud)) {
            $this->MultiCell(0, 4, $this->infirmary->cond_salud, 0, 'L');
        } else {
            $this->Cell(0, 4, __('Ninguna'), 0, 1);
        }
        $this->Ln(2);

        // Daily medication
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Medicamento de uso diario') . ':', 0, 1);
        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->med_usodi)) {
            $this->MultiCell(0, 4, $this->infirmary->med_usodi, 0, 'L');
            if (!empty($this->infirmary->dosis)) {
                $this->Cell(30, 4, __('Dosis') . ':', 0, 0);
                $this->Cell(0, 4, $this->infirmary->dosis, 0, 1);
            }
            if (!empty($this->infirmary->frec)) {
                $this->Cell(30, 4, __('Frecuencia') . ':', 0, 0);
                $this->Cell(0, 4, $this->infirmary->frec, 0, 1);
            }
        } else {
            $this->Cell(0, 4, __('Ninguno'), 0, 1);
        }
        $this->Ln(2);

        // Asthma
        $this->Cell(50, 5, __('Asma') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->asma ?? __('No'), 0, 1);
    }
}
