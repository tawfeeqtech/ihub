<?php

namespace App\Models;

use App\Traits\HasUnreadMessages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasUnreadMessages;
    use HasFactory;

    protected $fillable = ['user_id', 'secretary_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function isParticipant($userId): bool
    {
        return $this->user_id === $userId || $this->secretary_id === $userId;
    }

    public function getUnreadMessagesCountForAuth(): int
    {
        $auth = auth()->user();

        if (!$auth) {
            return 0;
        }

        // احسب الرسائل غير المقروءة في هذه المحادثة فقط
        return $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $auth->id) // فقط الرسائل من الطرف الآخر
            ->count();
    }
}
