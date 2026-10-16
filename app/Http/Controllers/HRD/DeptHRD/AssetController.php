<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        $assets = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('role.hrd.asset.index', compact('assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            '_form' => ['required', Rule::in(['create_asset'])],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:assets,code'],
        ]);

        Asset::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);

        return redirect()->route('asset.index', $request->query())
            ->with('success', 'Asset berhasil ditambahkan.');
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            '_form' => ['required', Rule::in(['edit_asset'])],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assets', 'code')->ignore($asset->id),
            ],
        ]);

        $asset->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);

        return redirect()->route('asset.index', $request->query())
            ->with('success', 'Asset berhasil diperbarui.');
    }
}
