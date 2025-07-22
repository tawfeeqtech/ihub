<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $conversations = Conversation::where('user_id', auth()->id())->latest()->get();
        return $this->apiResponse(ConversationResource::collection($conversations), __('messages.success'), 200);
    }

    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_id !== auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }

        $conversation->delete();
        return $this->apiResponse(null, __('messages.success'), 200);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'secretary_id' => 'required|exists:users,id',
        ]);

        // التحقق من وجود محادثة بين المستخدم والسكرتير
        $conversation = Conversation::where('user_id', auth()->id())
            ->where('secretary_id', $validated['secretary_id'])
            ->first();

        if ($conversation) {
            // إذا وُجدت محادثة، استرجع الرسائل
            if ($conversation->user_id != auth()->id()) {
                return $this->apiResponse(null, __('messages.not_authorized'), 403);
            }

            return $this->apiResponse(
                MessageResource::collection($conversation->messages()->latest()->get()),
                __('messages.success'),
                200
            );
        }

        // إذا لم توجد محادثة، أنشئ واحدة جديدة
        $conversation = Conversation::create([
            'user_id' => auth()->id(),
            'secretary_id' => $validated['secretary_id'],
        ]);

        $data = [
            'conversation_id' => $conversation->id,
        ];

        return $this->apiResponse($data, __('messages.success'), 200);
    }
}
