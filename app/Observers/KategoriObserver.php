<?php

namespace App\Observers;

use App\Models\KategoriBarang;
use Illuminate\Support\Facades\Auth;

class KategoriObserver
{
    public function created(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user()->id ?? 1)
            ->withProperties(['attributes' => $kategori->getAttributes()])
            ->log('Kategori dibuat');
    }

    public function updated(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user()->id ?? 1)
            ->withProperties([
                'old' => $kategori->getOriginal(),
                'attributes' => $kategori->getAttributes(),
            ])
            ->log('Satuan Updated');
    }

    public function deleted(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user()->id ?? 1)
            ->log('Satuan dihapus');
    }
}
