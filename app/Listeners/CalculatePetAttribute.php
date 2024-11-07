<?php

namespace App\Listeners;

use App\Events\CreateUserPet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculatePetAttribute
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateUserPet $event): void
    {
        $pet = $event->pet;

        $pet->age = calculateAge($pet->birth, 'Y-m');
        $pet->weight_type = $pet->weight;

        $pet->save();
    }
}
