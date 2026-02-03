<?php

namespace App\Traits;

use Livewire\Attributes\On;

trait HasRefreshFunction
{
    #[On('do-refresh')]
    public function doRefresh() {}
}
