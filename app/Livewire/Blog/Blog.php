<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Database\Eloquent\Builder;

#[Title('N-xotech | Blog')]
#[Layout('layouts.main')]
class Blog extends Component
{
    use WithPagination;

    public $page = 1;

    #[Url]
    public $search = '';

    #[Url]
    public $sortBy = 'newest';

    #[Url]
    public $filterCategory = '';

    protected function queryString()
    {
        return [
            'page' => [
                'except' => 1
            ],
            'sortBy' => [
                'except' => 'newest',
                'as' => 'sort'
            ],
            'filterCategory' => [
                'except' => '',
                'as' => 'filter'
            ],
            'search' => [
                'except' => '',
                'as' => 'q',
            ],

        ];
    }

    public function updated($property)
    {
        if (in_array($property, ['sortBy', 'filterCategory'])) {
            $this->resetPage();
        }

        if ($property === 'search') {
            $this->resetPage();
        }
    }
    public function render()
    {

        $sanitizedSearch = trim(preg_replace('/\s+/', ' ', $this->search));

        $posts = Post::search($sanitizedSearch)
            ->query(
                fn(Builder $query) => $query->with(['user', 'media', 'category', 'tags', 'comments'])
                    ->where('is_published', true)
                    ->when($this->filterCategory, function ($q) {
                        $q->whereHas('category', function ($subQuery) {
                            $subQuery->where('slug', $this->filterCategory);
                        });
                    })
            )->orderBy($this->getSortField(), $this->getSortDirection())
            ->paginate(6);

        $categories = Category::all();

        return view('livewire.blog.blog', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    protected function getSortField()
    {
        return match ($this->sortBy) {
            'popular' => 'views_count',
            'oldest' => 'created_at',
            default => 'created_at',
        };
    }

    protected function getSortDirection()
    {
        return match ($this->sortBy) {
            'oldest' => 'asc',
            default => 'desc',
        };
    }
}
