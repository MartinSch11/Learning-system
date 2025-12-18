<?php

namespace App\Filament\Student\Resources\ExamGrades\Pages;

use App\Filament\Student\Resources\ExamGrades\ExamGradeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamGrades extends ListRecords
{
    protected static string $resource = ExamGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
