<?php

namespace App\Services;

use App\Services\CommentsService;

class PetBreedService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\SysPetBreed');
    }
}