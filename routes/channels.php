<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    return $conversation && $conversation->isParticipant($user->id);
});

Broadcast::channel('secretary.{secretaryId}', function ($user, $secretaryId) {
    return (int) $user->id === (int) $secretaryId;
});
