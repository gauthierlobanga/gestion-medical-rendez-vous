<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
class ShowBlog extends Component
{
    public Post $post;

    public function mount(Post $post)
    {
        $this->post = $post->load(['category', 'user', 'tags', 'media', 'comments']);
    }

    public function render()
    {
        return view('livewire.blog.show-blog', [
            'post' => $this->post,
            'relatedPosts' => Post::query()
                ->where('category_id', $this->post->category_id)
                ->where('id', '!=', $this->post->id)
                ->with(['category', 'media'])
                ->limit(6)
                ->latest()
                ->get()
        ])
            ->title($this->post->title . ' | Nxotech');
    }
}
