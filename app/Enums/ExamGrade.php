<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExamGrade: string implements HasLabel, HasColor
{
    case A_PLUS = 'A+';
    case A      = 'A';
    case B      = 'B';
    case C      = 'C';
    case D      = 'D';
    case E      = 'E'; // En UK a veces la E es la nota de paso más baja
    case U      = 'U'; // Unclassified (Reprobado / Sin calificar)
    case ABSENT = 'Ausente';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::A_PLUS => 'A+ (Excelente)',
            self::A      => 'A (Muy Bueno)',
            self::B      => 'B (Bueno)',
            self::C      => 'C (Satisfactorio)',
            self::D      => 'D (Necesita Mejorar)',
            self::E      => 'E (Pase Mínimo)',
            self::U      => 'U (No Clasificado/Reprobado)',
            self::ABSENT => 'Ausente',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::A_PLUS, self::A => 'success', // Verde
            self::B, self::C      => 'info',    // Azul
            self::D               => 'warning', // Naranja
            self::E, self::ABSENT => 'danger',  // Rojo
        };
    }
}
