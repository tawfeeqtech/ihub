<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use App\Http\Resources\MessageResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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

        if ($conversation->user_id !== auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }
        $validated = $request->validate([
            'body' => 'nullable|string',
            'attachment' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $data = [
            'conversation_id' => $conversationId,
            'sender_id' => auth()->id(),
            'body' => $validated['body'] ?? null,

        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            if ($file->isValid()) {
                $path = 'uploads/' . $conversationId . "/" . auth()->id();
                $data['attachment'] = $this->uploadImage($path, $request, 'attachment');
            }
        }

        $message = Message::create($data);
        return $this->apiResponse(new MessageResource($message), __('messages.success'), 200);
    }
}
