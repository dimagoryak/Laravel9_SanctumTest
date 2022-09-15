<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Events\UserPingMessage;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = User::find($request->id)->name . ": " . $request->message ?? '';
        $users = User::where('id', '!=', $request->id)->get();

        foreach ($users as $user) {
            UserPingMessage::dispatch($user->id, $message);
        }
    }
}
