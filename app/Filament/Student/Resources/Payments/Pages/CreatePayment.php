<?php

namespace App\Filament\Student\Resources\Payments\Pages;

use App\Filament\Student\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
