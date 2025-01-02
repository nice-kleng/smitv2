<?php

namespace App\Observers;

use App\Models\Satuan;

class SatuanObserver
{
    public function created(Satuan $satuan)
    {
        activity()
            ->performedOn($satuan)
            ->by(auth()->user())
            ->withProperties(['attributes' => $satuan->getAttributes()])
            ->log('Satuan created');
    }

    public function updated(Satuan $satuan)
    {
        activity()
            ->performedOn($satuan)
            ->by(auth()->user())
            ->withProperties([
                'old' => $satuan->getOriginal(),
                'attributes' => $satuan->getAttributes()
            ])
            ->log('Satuan updated');
    }

    public function deleted(Satuan $satuan)
    {
        activity()
            ->performedOn($satuan)
            ->by(auth()->user())
            ->log('Satuan deleted');
    }
}
