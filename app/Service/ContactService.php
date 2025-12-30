<?php

namespace App\Service;

use App\Models\Contact;

class ContactService
{
    public function submitInquiry(array $data)
    {
        return Contact::create($data);
    }

    public function getSellerMessages($user)
    {
        return Contact::with('unit')->where('seller_id', $user->id)->latest()->paginate(10);
    }
}
