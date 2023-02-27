<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    /**
     * Listing
     *  We must name it "index" instead on "list", but the previous developer uses "list"
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Arrayable|array $data
     */
    public function list(Request $request)
    {
        abort_if(!auth('admin')->user()->hasRole('super-admin'), 403);

        $query_param = [];
        $search = $request['search'];

        $users = new Admin();

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);

            $users = Admin::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->with('roles');
                }
            });

            // TODO: Later, not needed now
            // $users = ... // And then merge "normal users" with "admins"
            $query_param = ['search' => $request['search']];
        }

        $users = $users->orderBy('id', 'desc')->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.users.list', compact('users', 'search'));
    }

    /**
     * Create page
     * 
     * @return \Illuminate\Contracts\Support\Arrayable|array $data
     */
    public function create()
    {
        abort_if(!auth('admin')->user()->hasRole('super-admin'), 403);

        return view('admin-views.users.form');
    }

    /**
     * Store user data
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(!auth('admin')->user()->hasRole('super-admin') && auth('admin')->user()->id != $id, 403);

        // ! TODO: we should later merge the users and admins table into one table
        $isAdmin = in_array($request->role, ['super-admin', 'admin']);

        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:' . ($isAdmin ? 'admins' : 'users') . ',email',
            'phone' => 'nullable',
            'role' => 'required|in:admin',
            'password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {

            $data = [
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ];

            // Store Image
            if (!empty($request->file('image'))) {
                $data['image'] = Helpers::upload('users/', 'png', $request->file('image'));
            } else {
                $data['image'] = 'def.png';
            }

            if ($isAdmin) {
                $admin = Admin::create($data);

                // Assign role
                $admin->assignRole($request->role);

                Toastr::success(translate('Admin added successfully!'));
            } else {
                // TODO: For normal users, and we should also merge the users and admins table in one table
            }

            DB::commit();

            return redirect()->back();
        } catch (Throwable $th) {
            DB::rollBack();

            Toastr::error(translate('Something went wrong'));

            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Edit page
     * 
     * @param integer $user
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($id)
    {
        abort_if(!auth('admin')->user()->hasRole('super-admin') && auth('admin')->user()->id != $id, 403);

        // TODO: Now we are only editing admins, we must also do something later to edit normal users, but it's not needed for now
        $user = Admin::findOrFail($id);

        return view('admin-views.users.form', compact('user'));
    }

    /**
     * Update user data
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        abort_if(!auth('admin')->user()->hasRole('super-admin'), 403);

        // ! TODO: we should later merge the users and admins table into one table
        $isAdmin = in_array($request->role, ['super-admin', 'admin']);

        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:' . ($isAdmin ? 'admins' : 'users') . ',email,' . $id . ',id',
            'phone' => 'nullable',
            'role' => 'required|in:super-admin,admin', // ! TODO: Later, we need to allow only "super-admins" to add "super-admin" role, it needs validation
            'password' => 'nullable|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $user = $isAdmin ? Admin::findOrFail($id) : User::findOrFail($id);

            $data = [
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            // Store Image
            if (!empty($request->file('image'))) {
                $data['image'] = Helpers::upload('users/', 'png', $request->file('image'));

                // Delete old image
                $file_path = asset('storage/app/public/users/' . $user->image);
                if (Storage::exists($file_path)) {
                    Storage::delete($file_path);
                }
            }

            $user->update($data);

            // Update role
            $user->syncRoles([$request->role]);

            Toastr::success(translate('Updated successfully!'));

            DB::commit();

            return redirect()->back();
        } catch (Throwable $th) {
            DB::rollBack();

            Toastr::error(translate('Something went wrong'));

            return redirect()->back()->withInput($request->all());
        }
    }
}
