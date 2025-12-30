<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Contact\StoreContactRequest;
use App\Service\ContactService;
use App\Traits\ApiResponse;

class ContactController extends Controller
{
    use ApiResponse;
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function store(StoreContactRequest $request)
    {
        $contact = $this->contactService->submitInquiry($request->validated());
        return $this->created($contact, __('api.contact_submitted'));
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $contacts = $this->contactService->getSellerMessages($request->user());

        if ($contacts->isEmpty()) {
            return $this->success([], __('api.no_messages_yet'));
        }

        return $this->paginated(\App\Http\Resources\ContactResource::class, $contacts);
    }
}
