<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Notifications\MessageNotification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

use function Illuminate\Log\log;

class Chat extends Component
{
    public $chats = []; // قائمة الدردشات
    public $selectedChat = null; // المحادثة المختارة
    public $message; // الرسائل
    public $messages = []; // الرسائل
    public $newMessage = false; // لتحديد إذا كانت هناك رسالة جديدة
    public $currentUserId; // معرف المستخدم الحالي

    public $paginateVar =10;

    protected $middleware = ['auth'];
    public function mount()
    {  
        emit('loadMore');
        
        $this->chats = Conversation::with(['firstUser', 'secondUser'])
        ->orderBy('updated_at', 'desc') // ترتيب المحادثات حسب آخر تحديث
        ->take(5) // جلب أول 5 محادثات
        ->get()
        ->map(function ($conversation) {
            return [
                'id' => $conversation->id,
                'name' => auth()->id() === $conversation->first_user 
                            ? $conversation->secondUser->user_name 
                            : $conversation->firstUser->user_name, // عرض اسم الشخص الآخر
                'last_message' => $conversation->last_message,
                'profile' => 'default-profile.png', // يمكنك تخصيص صورة الملف الشخصي هنا
            ];
        })            
        ->toArray();  
        $this->fetchMessages();
        $this->currentUserId = auth()->id(); // تخزين معرف المستخدم الحالي
        
    }


    protected $listeners = 
    [
        'loadMore',
    ];

    public function getListeners()
    {
        $this->currentUserId = auth()->id(); // تخزين معرف المستخدم الحالي

        return
        [
'loadMore',
"echo-private:users.{$this->currentUserId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications',
        ];
    }

  public function broadcastedNotifications($event)
  {
    dd($event);
  } 
    public function fetchMessages()
    {
        $currentMessageCount = count($this->messages);
          
        $this->messages = \App\Models\Chat::where('conversation_id', $this->selectedChat)
        ->orderBy('created_at', 'asc')
        ->get()
        ->toArray();
       
    }

    public function dismissNotification()
    {
        $this->newMessage = false; // إخفاء التنبيه
    }


   

    public function sendMessage()
{
    $this->validate([
        'message' => 'required|string|max:1000',
    ]);
    $conversation = \App\Models\Conversation::find($this->selectedChat['id']);
    $receiverId = auth()->id() === $conversation->first_user 
? $conversation->second_user // إذا كان المستخدم الحالي هو الأول، اجعل المستقبل هو الثاني
: $conversation->first_user;
    $message = \App\Models\Chat::create([
        'message' => $this->message,
        'sender_id' => auth()->id(),
        'receiver_id' => $receiverId, // إذا كان المستخدم الحالي هو الثاني، اجعل المستقبل هو الأول
        'conversation_id' => $this->selectedChat['id'],
    ]);

// dd(auth()->user() .' ' .$message.' ' . $conversation.' ' . $receiverId);

   $this->messages[] = $message->toArray();

    $this->getUserById($receiverId)->notify(new MessageNotification( auth()->user(),$message,$conversation,$receiverId));

    

   

    $this->message = '';
}


public function getUserById($receiverId)
{
    return \App\Models\User::find($receiverId); // جلب كائن User باستخدام المعرف
}



public function loadMore()
{
    dd("loadMore called"); // عرض الرسالة عند استدعاء الحدث
    $this->paginateVar += 10;
    $this->loadMessages();
}
public function loadMessages()
{
    $count = \App\Models\Chat::where('conversation_id', $this->selectedChat)->count();
    
    $this->messages = Chat::where('conversation_id', $this->selectedChat)
    ->skip($count - $this->paginateVar )
    ->take($this->paginateVar )
    ->get();

    return $this->messages;
}

public function selectChat($chatId)
{
    $this->selectedChat = collect($this->chats)->firstWhere('id', $chatId);

    // جلب الرسائل من قاعدة البيانات
    $this->messages = \App\Models\Chat::where('conversation_id', $chatId)
        ->orderBy('created_at', 'asc')
        ->get()
        ->toArray();
}


    public function render()
    {
        return view('livewire.chat');
    }
}

