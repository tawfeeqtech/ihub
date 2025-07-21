<?php

namespace App\Http\Controllers\API\V1;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use App\Http\Resources\MessageResource;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    use ApiResponseTrait;

    // جلب جميع رسائل محادثة
    public function index($conversationId)
    {

        try {
            $conversation = Conversation::findOrFail($conversationId);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(null, __('messages.not_found'), 404);
        }

        if ($conversation->user_id !== auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }

        return $this->apiResponse(MessageResource::collection(
            $conversation->messages()->latest()->get()
        ), __('messages.success'), 200);
    }

    // إرسال رسالة جديدة
    public function store(Request $request, $conversationId)
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(null, __('messages.not_found'), 404);
        }

        if ((int) $conversation->user_id !== (int) auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }
        $validated = $request->validate([
            'body' => 'nullable|string',
            'attachment' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $sender = auth()->user(); // App user
        $recipient = User::find($conversation->secretary_id);
        // dd($recipient);
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id' => $sender->id,
            'receiver_id' => $recipient->id,
            'body' => $validated['body'] ?? null,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            if ($file->isValid()) {
                $path = 'uploads/' . $conversationId . "/" . auth()->id();
                $messageData['attachment'] = $this->uploadImage($path, $request, 'attachment');
            }
        }


        $message = Message::create($messageData);

        // Send notification to the secretary
        $notificationService = app(NotificationService::class);
        $notificationService->sendUnreadMessageNotification(
            $recipient, // Secretary
            $sender,    // App user
            true        // Secretary notification
        );

        $message->load('sender');
        // broadcast(new MessageSent($message))->toOthers();

        Log::info('Attempting to broadcast MessageSent event for conversation: ' . $conversation->id);
        broadcast(new MessageSent($message))->toOthers();
        Log::info('MessageSent event broadcasted.');


        return $this->apiResponse(new MessageResource($message), __('messages.success'), 200);
    }
}
