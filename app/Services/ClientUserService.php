<?php

namespace App\Services;

class ClientUserService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ClientUser');
    }
}