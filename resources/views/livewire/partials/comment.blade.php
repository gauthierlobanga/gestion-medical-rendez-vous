<article x-data="{
    replyOpen: false,
    editOpen: false,
    init() {
        this.$watch('$wire.replyingTo', value => {
            this.replyOpen = value === {{ $comment->id }};
        });
        this.$watch('$wire.editingComment', value => {
            this.editOpen = value === {{ $comment->id }};
        });
    }
}" wire:key="comment-{{ $comment->id }}"
    class="p-3 bg-white dark:bg-gray-900 transition duration-300 ease-in-out" x-cloak>

    <!-- Auteur & Date -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <img class="w-10 h-10 rounded-full"
                src="{{ $comment->author->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->author->name) }}"
                alt="{{ $comment->author->name }}">
            <span class="font-semibold text-gray-800 dark:text-gray-200">
                {{ $comment->author->name ?? $comment->guest_name }}
            </span>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $comment->created_at->diffForHumans() }}
        </span>
    </div>

    <!-- Corps ou édition -->
    <div>
        @if ($editingComment === $comment->id)
            <div x-show="editOpen">
                <flux:textarea wire:model.live.debounce.250ms="editBody" placeholder="Votre réponse..." />
                <div class="flex gap-2 mt-2">
                    <flux:button icon="paper-airplane" size="sm" wire:click="updateComment" :loading="false" />
                    <flux:button icon="x-mark" size="sm" wire:click="cancelEdit" :loading="false" />
                </div>
            </div>
        @else
            <p>{{ $comment->body }}</p>
        @endif
    </div>

    <!-- Actions (masquer si edit ou reply est actif) -->
    <div class="flex gap-3 mt-2" x-show="!replyOpen && !editOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90" >
        <livewire:like-button :model="$comment"
        :key="'comment-'.$comment->id" />

    <flux:button icon="arrow-uturn-left" size="sm" @click="replyOpen = true"
        wire:click="startReply({{ $comment->id }})" :loading="false" />

    @if (auth()->check() && auth()->id() === $comment->user_id)
        <flux:button icon="pencil-square" size="sm" @click="editOpen = true"
            wire:click="startEdit({{ $comment->id }})" :loading="false" />
        <flux:button icon="trash" size="sm" wire:click="deleteComment({{ $comment->id }})" :loading="false" />
    @endif
    </div>

    <!-- Réponse -->
    <div x-show="replyOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90 class="mt-3">
        <flux:textarea wire:model.defer="newComment" class="w-full" placeholder="Votre réponse..." />
        <div class="flex gap-2 mt-2">
            <flux:button icon="paper-airplane" size="sm" wire:click="postReply({{ $comment->id }})" :loading="false" />
            <flux:button icon="x-mark" size="sm" @click="replyOpen = false" wire:click="cancelReply" :loading="false" />
        </div>
    </div>

    <!-- Réponses -->
    @if ($comment->replies->isNotEmpty())
        @foreach ($comment->replies as $reply)
            @include('livewire.partials.comment', ['comment' => $reply, 'depth' => $depth + 1])
        @endforeach
    @endif
</article>
