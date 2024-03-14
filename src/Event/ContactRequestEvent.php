<?php

namespace App\Event;

use App\Entity\User;
use App\DTO\ContactDTO;

class ContactRequestEvent
{


    public function __construct(public ContactDTO $data, public User $user)
    {
    }
}
