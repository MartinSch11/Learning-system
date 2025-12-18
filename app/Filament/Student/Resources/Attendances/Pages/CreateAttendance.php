<?php

namespace App\Filament\Student\Resources\Attendances\Pages;

use App\Filament\Student\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
}
