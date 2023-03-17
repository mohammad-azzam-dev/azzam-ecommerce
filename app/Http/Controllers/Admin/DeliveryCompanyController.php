<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\CountryProvince;
use App\Model\DeliveryCompany;
use App\Model\DeliveryCompanyProvince;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DeliveryCompanyController extends Controller
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
            $query = DeliveryCompany::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $query_param = ['search' => $request['search']];
        } else {
            $query = DeliveryCompany::latest();
        }

        $deliveryCompanies = $query->with('countryProvinces')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.delivery-companies.index', compact('deliveryCompanies', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $deliveryCompanies = DeliveryCompany::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.delivery-companies.partials._table', compact('deliveryCompanies'))->render()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = CountryProvince::all();
        return view('admin-views.delivery-companies.create', compact('provinces'));
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
            'name' => 'required|unique:delivery_companies',
            'phone_number' => 'required',
            'provinces' => 'required|array'
        ]);

        $deliveryCompany = DeliveryCompany::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        foreach ($request->provinces as $province) {
            $deliveryCompany->provinces()->create([
                'province_id' => $province
            ]);
        }

        Toastr::success(translate('Delivery Company added successfully!'));
        return redirect()->route('admin.delivery-company.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\DeliveryCompany  $deliveryCompany
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryCompany $deliveryCompany)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\DeliveryCompany  $deliveryCompany
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryCompany $deliveryCompany)
    {
        $provinces = CountryProvince::all();

        return view('admin-views.delivery-companies.edit', compact('deliveryCompany', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\DeliveryCompany  $deliveryCompany
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryCompany $deliveryCompany)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'provinces' => 'required|array'
        ]);

        $deliveryCompany->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        $newProvincesIds = [];
        foreach ($request->provinces as $province) {
            $newProvince = DeliveryCompanyProvince::updateOrCreate([
                'delivery_company_id' => $deliveryCompany->id,
                'province_id' => $province,
            ],[
                'province_id' => $province,
            ]);

            $newProvincesIds[] = $newProvince->id;
        }

        DeliveryCompanyProvince::where('delivery_company_id', $deliveryCompany->id)->whereNotIn('id', $newProvincesIds)->delete();

        Toastr::success(translate('Delivery Company updated successfully!'));
        return redirect()->route('admin.delivery-company.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\DeliveryCompany  $deliveryCompany
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryCompany $deliveryCompany)
    {
        $deliveryCompany->delete();

        Toastr::success(translate('Delivery Company removed!'));
        return back();
    }
}
