<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCompany;
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

        $deliveryCompanies = $query->with('provinces')->paginate(Helpers::pagination_limit())->appends($query_param);

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
        $provinces = [
            'الكويت',
            'حولي',
            'مبارك الكبير',
            'الجهراء',
            'الأحمدي',
        ];
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

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                toastr::error(\App\CentralLogics\translate('Name is too long!'));
                return back();
            }
        }

        $deliveryCompany = DeliveryCompany::create([
            'name' => $request->name[array_search('en', $request->lang)],
            'phone_number' => $request->phone_number,
        ]);

        foreach ($request->provinces as $province) {
            $deliveryCompany->provinces()->create([
                'province_id' => $province
            ]);
        }

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $data[] = array(
                    'translationable_type' => 'App\Model\DeliveryCompany',
                    'translationable_id' => $deliveryCompany->id,
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\DeliveryCompany  $deliveryCompany
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryCompany $deliveryCompany)
    {
        //
    }
}
