<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * ¿Quién puede ver la lista de pagos en el menú?
     */
    public function viewAny(User $user): bool
    {
        // SOLO el Admin
        return $user->hasRole('admin');
    }

    /**
     * ¿Quién puede ver el detalle de un pago?
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * ¿Quién puede crear pagos?
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * ¿Quién puede editar?
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * ¿Quién puede borrar?
     */
    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }
}