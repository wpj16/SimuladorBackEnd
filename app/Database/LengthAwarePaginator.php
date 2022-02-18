<?php

namespace App\Database;

use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorBase;

class LengthAwarePaginator extends LengthAwarePaginatorBase
{

    public function toArrayDefault()
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'links' => $this->linkCollection()->toArray(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }

    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'total' => $this->total(),
            'pagina' => $this->currentPage(),
        ];
    }
}
