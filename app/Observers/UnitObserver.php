<?php

namespace App\Observers;

use App\Models\Unit;

class UnitObserver
{
    public function created(Unit $unit)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($unit)
            ->withProperties(['kode_unit' => $unit->kode_unit, 'nama_unit' => $unit->nama_unit])
            ->log('Unit berhasil ditambahkan');
    }

    public function updated(Unit $unit)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($unit)
            ->withProperties([
                'old' => $unit->getOriginal(),
                'attributes' => $unit->getAttributes(),
            ])
            ->log('Unit berhasil diubah');
    }

    public function deleted(Unit $unit)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($unit)
            ->log('Unit berhasil dihapus');
    }
}
