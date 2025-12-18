<?php

namespace App\Filament\Student\Resources\ExamGrades\Pages;

use App\Filament\Student\Resources\ExamGrades\ExamGradeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamGrade extends EditRecord
{
    protected static string $resource = ExamGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
