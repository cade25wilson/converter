<?php

namespace App\Http\Controllers;

use App\Events\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'username' => 'required|string',
        ]);

        $message = $request->input('message');
        $username = $request->input('username');

        $sanitizedMessage = DB::connection('pimpostgres')->getPdo()->quote($message);
        $sanitizedUsername = DB::connection('pimpostgres')->getPdo()->quote($username);

        DB::connection('pimpostgres')->statement("CALL insert_message($sanitizedMessage, $sanitizedUsername)");

        Messages::dispatch($message, $username);

        return response()->json(['message' => $message, 'username' => $username]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $recentMessages = DB::connection('pimpostgres')->select('SELECT * FROM getrecentmessages()');
        return response()->json($recentMessages);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
