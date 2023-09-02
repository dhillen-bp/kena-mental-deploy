<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Testimonial;
use App\Models\Consultation;
use App\Models\Psychologist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MentalTest;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adminCount = Admin::count();
        $userCount = User::count();
        $psychologistCount = Psychologist::count();
        $consultationCount = Consultation::count();
        $testimonialCount = Testimonial::count();
        $testCount = MentalTest::count();
        return view('admin.dashboard', compact('adminCount', 'userCount', 'psychologistCount', 'consultationCount', 'testimonialCount', 'testCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $psychologists = Psychologist::select('id', 'name')->get();
        return view('admin.admin-add', compact('psychologists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:admins|max:255',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'psychologist_id' => 'nullable|unique:admins',
        ]);

        $request->merge(['password' => Hash::make($request->password)]);
        $request['name'] = trim($request['name']);
        if ($request->role === 'admin' && $request->psychologist_id) {
            return back()->with('error', 'Psychologist ID should not be filled for admin role.')->withInput();
        }

        $admin = Admin::create($request->all());
        return redirect('/admin/show-admin')->with('success', "Admin Added Successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $keyword = $request->keyword;

        $admins = Admin::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('email', 'LIKE', '%' . $keyword . '%')
            ->orWhere('role', 'LIKE', '%' . $keyword . '%')
            ->orWhere('psychologist_id', 'LIKE', '%' . $keyword . '%')
            ->paginate(10);
        return view('admin.admin-show', compact('admins'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $admin = Admin::find($id);
        $psychologists = Psychologist::select('id', 'name')->get();
        return view('admin.admin-edit', compact('admin', 'psychologists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:admins|max:255',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'psychologist_id' => 'nullable|unique:admins',
        ]);

        $request->merge(['password' => Hash::make($request->password)]);
        $request['name'] = trim($request['name']);
        if ($request->role === 'admin' && $request->psychologist_id) {
            return back()->with('error', 'Psychologist ID should not be filled for admin role.')->withInput();
        }

        $admin = Admin::find($id);
        $adminUpdate = $admin->update($request->all());
        return redirect('/admin/show-admin')->with('success', "Admin  Updated Successfully!")->withErrors($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);
        $admin->delete();

        return redirect('/admin/show-admin')->with('success', 'Admin Deleted Successfully!');
    }

    public function showDeletedAdmins()
    {
        $deletedAdmins = Admin::onlyTrashed()->paginate(10);
        return view('admin.admin-deleted', compact('deletedAdmins'));
    }

    public function restore($id)
    {
        $admin = Admin::onlyTrashed()->find($id);

        $admin->restore();

        return redirect('/admin/show-admin')->with('success', 'Admin Restored Successfully!');
    }

    public function destroyPermanent($id)
    {
        $deletedAdmin = Admin::onlyTrashed()->find($id);

        $deletedAdmin->forceDelete();

        return redirect('/admin/deleted-testimonials')->with('success', 'Admin Deleted Permanent Successfully!');
    }

    public function showProfile()
    {
        $loggedInAdmin = auth('admin')->user();
        $profileData = [];

        if ($loggedInAdmin) {
            $profileData['id'] = $loggedInAdmin->id;
            $profileData['name'] = $loggedInAdmin->name;
            $profileData['email'] = $loggedInAdmin->email;
            $profileData['psychologist_id'] = $loggedInAdmin->psychologist_id;
        }

        return view('admin.profile', compact('profileData'));
    }
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required',
        ]);

        $request->merge(['password' => Hash::make($request->password)]);
        $request['name'] = trim($request['name']);

        $admin = Admin::find(auth('admin')->user()->id);
        $adminUpdate = $admin->update($request->all());

        return redirect('/admin/profile')->with('success', "Profile  Updated Successfully!")->withErrors($validated);
    }
}
