<?php

namespace App\Http\Controllers\Admin;

use App\Model\Addon;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddonStoreRequest;
use App\Model\DeliveryCompany;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:addons,name'],
            'price' => ['required', 'numeric']
        ]);

        $addon = Addon::create([
            'name' => $request->name[array_search('en', $request->lang)],
            'price' => $request->price,
        ]);

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $data[] = array(
                    'translationable_type' => 'App\Model\Addon',
                    'translationable_id' => $addon->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
        }
        if(count($data))
        {
            Translation::insert($data);
        }

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
        $addon->load('translations');
        return view('admin-views.addon.edit', compact('addon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Addon  $addon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Addon $addon)
    {
        $request->validate([
            'name' => ['required', Rule::unique('addons')->ignore($addon->id)],
            'price' => ['required', 'numeric']
        ]);

        $addon->update([
            'name' => $request->name[array_search('en', $request->lang)],
            'price' => $request->price,
        ]);

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Model\Addon',
                        'translationable_id'    => $addon->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }

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
