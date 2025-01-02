<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Models\Permintaan;

class PermintaanObserver
{
    /**
     * Handle the Permintaan "created" event.
     */
    public function created(Permintaan $permintaan): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($permintaan)
            ->withProperties(['kode_permintaan' => $permintaan->kode_permintaan, 'created_id' => $permintaan->created_id])
            ->log('Permintaan berhasil ditambahkan');
    }

    /**
     * Handle the Permintaan "updated" event.
     */
    public function updated(Permintaan $permintaan): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($permintaan)
            ->withProperties([
                'old' => $permintaan->getOriginal(),
                'attributes' => $permintaan->getAttributes(),
            ])
            ->log('Permintaan berhasil diubah');
    }

    /**
     * Handle the Permintaan "deleted" event.
     */
    public function deleted(Permintaan $permintaan): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($permintaan)
            ->log('Permintaan berhasil dihapus');
    }

    /**
     * Handle the Permintaan "restored" event.
     */
    public function restored(Permintaan $permintaan): void
    {
        //
    }

    /**
     * Handle the Permintaan "force deleted" event.
     */
    public function forceDeleted(Permintaan $permintaan): void
    {
        //
    }

    /**
     * Handle the Permintaan "approved" event.
     */
    public function approved(Permintaan $permintaan): void
    {
        activity('approve permintaan')
            ->causedBy(auth()->user())
            ->performedOn($permintaan)
            ->withProperties(['kode_permintaan' => $permintaan->kode_permintaan, 'approved_id' => $permintaan->approved_id])
            ->log('Permintaan berhasil disetujui');
    }
}
