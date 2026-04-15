<?php

namespace App\Http\Controllers;

use App\Models\PortalLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class PortalLinkController extends Controller
{
    /**
     * Display a listing of portal links (DataTable).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PortalLink::orderBy('category')->orderBy('order')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('preview', function ($row) {
                    if ($row->image) {
                        return '<img src="' . $row->image_url . '" alt="' . $row->title . '" class="img-thumbnail" style="max-height:40px;">';
                    }
                    return '<i class="' . $row->display_icon . ' fa-2x text-primary"></i>';
                })
                ->addColumn('category_badge', function ($row) {
                    return '<span class="badge ' . $row->category_badge . '">' . $row->category_label . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->is_active) {
                        return '<span class="badge badge-success">Aktif</span>';
                    }
                    return '<span class="badge badge-secondary">Nonaktif</span>';
                })
                ->addColumn('short_url', function ($row) {
                    $url = $row->url;
                    if (strlen($url) > 50) {
                        $url = substr($url, 0, 50) . '...';
                    }
                    return '<a href="' . $row->url . '" target="_blank" class="text-primary">' . $url . '</a>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-sm btn-primary btn-edit mr-1" data-id="' . $row->id . '" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['preview', 'category_badge', 'status_badge', 'short_url', 'action'])
                ->make(true);
        }

        return view('settings.portal-links.index');
    }

    /**
     * Store a newly created portal link.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2000',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'category' => 'required|in:internal,external',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portal-links', 'public');
        }

        PortalLink::create($data);

        return response()->json(['success' => true, 'message' => 'Portal link berhasil ditambahkan']);
    }

    /**
     * Get portal link data for editing.
     */
    public function edit($id)
    {
        $portalLink = PortalLink::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $portalLink->id,
                'title' => $portalLink->title,
                'url' => $portalLink->url,
                'description' => $portalLink->description,
                'icon' => $portalLink->icon,
                'image' => $portalLink->image,
                'image_url' => $portalLink->image_url,
                'category' => $portalLink->category,
                'order' => $portalLink->order,
                'is_active' => $portalLink->is_active,
            ]
        ]);
    }

    /**
     * Update the specified portal link.
     */
    public function update(Request $request, $id)
    {
        $portalLink = PortalLink::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2000',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'category' => 'required|in:internal,external',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except(['image', '_method', '_token']);
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($portalLink->image) {
                Storage::disk('public')->delete($portalLink->image);
            }
            $data['image'] = $request->file('image')->store('portal-links', 'public');
        }

        // If remove_image flag is set
        if ($request->has('remove_image') && $request->remove_image) {
            if ($portalLink->image) {
                Storage::disk('public')->delete($portalLink->image);
            }
            $data['image'] = null;
        }

        $portalLink->update($data);

        return response()->json(['success' => true, 'message' => 'Portal link berhasil diperbarui']);
    }

    /**
     * Remove the specified portal link.
     */
    public function destroy($id)
    {
        $portalLink = PortalLink::findOrFail($id);

        // Delete image if exists
        if ($portalLink->image) {
            Storage::disk('public')->delete($portalLink->image);
        }

        $portalLink->delete();

        return response()->json(['success' => true, 'message' => 'Portal link berhasil dihapus']);
    }
}
