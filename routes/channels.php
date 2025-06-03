<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    // Log::info('Channel Auth - conversationId:', ['conversation_id' => $conversationId]);
    $conversation = \App\Models\Conversation::find($conversationId);
    Log::info('$conversation && $conversation->isParticipant($user->id):', ['conversation' => $conversation && $conversation->isParticipant($user->id)]);
    return $conversation && $conversation->isParticipant($user->id);
});

// Broadcast::channel('secretary.{secretaryId}', function ($user, $secretaryId) {
//     return (int) $user->id === (int) $secretaryId;
// });

Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
