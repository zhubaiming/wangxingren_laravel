<?php

namespace App\Services;

use App\Services\CommentsService;

class PetBreedWeightService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\SysPetBreedWeight');
    }
}