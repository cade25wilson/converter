<?php

namespace App\Http\Controllers;

use App\Mail\NewContactReceivedEmail;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'nullable|string|regex:/^[\d\-\(\)]+$/',
                'message' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => 'Contact Creation Failed!'], 422);
        }

        DB::beginTransaction();

        try {
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ]);

            DB::commit();
            Mail::send(new NewContactReceivedEmail($contact));
            return response()->json(['message' => 'Message received, we will get back to you asap!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['errors' => 'Contact Creation Failed!'], 409);
        }
    }
}
