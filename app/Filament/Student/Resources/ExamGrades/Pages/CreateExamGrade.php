<?php

namespace App\Filament\Student\Resources\ExamGrades\Pages;

use App\Filament\Student\Resources\ExamGrades\ExamGradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamGrade extends CreateRecord
{
    protected static string $resource = ExamGradeResource::class;
}
