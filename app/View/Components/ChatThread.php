<?php

namespace App\View\Components;

use App\Models\Conversation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ChatThread extends Component
{
    public function __construct(
        public Conversation $conversation,
        public Collection|array $messages,
        public string $sendRouteName,
        public string $pollRouteName
    ) {}

    public function render(): View
    {
        return view('components.chat-thread');
    }
}
