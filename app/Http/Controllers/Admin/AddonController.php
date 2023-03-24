<?php

namespace App\Http\Controllers\Admin;

use App\Model\Addon;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddonStoreRequest;
use App\Model\DeliveryCompany;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Addon::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $query_param = ['search' => $request['search']];
        } else {
            $query = Addon::latest();
        }

        $addons = $query->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.addon.index', compact('addons', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $addons = Addon::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.addon.partials._table', compact('addons'))->render()
        ]);
    }

    public function update_status(Addon $addon, $status)
    {
        $addon->update([
           'is_active' => $status
        ]);

        Toastr::success(translate('Addon status updated!'));
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin-views.addon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddonStoreRequest $request)
    {
        Addon::create($request->validated());

        Toastr::success(translate('Addon added successfully!'));
        return redirect()->route('admin.addon.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function edit(Addon $addon)
    {
        return view('admin-views.addon.edit', compact('addon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Addon  $addon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AddonStoreRequest $request, Addon $addon)
    {
        $addon->update($request->validated());

        Toastr::success(translate('Addon updated successfully!'));
        return redirect()->route('admin.addon.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Addon $addon)
    {
        $addon->delete();

        Toastr::success(translate('Addon removed!'));
        return back();
    }
}
