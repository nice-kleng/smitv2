<?php

namespace App\Observers;

use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;

class RuanganObserver
{
    public function created(Ruangan $ruangan)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($ruangan)
            ->withProperties(['attributes' => $ruangan->getAttributes()])
            ->log('Ruangan created');
    }

    public function updated(Ruangan $ruangan)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($ruangan)
            ->withProperties([
                'old' => $ruangan->getOriginal(),
                'attributes' => $ruangan->getAttributes()
            ])
            ->log('Ruangan updated');
    }

    public function deleted(Ruangan $ruangan)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($ruangan)
            ->log('Ruangan deleted');
    }
}
