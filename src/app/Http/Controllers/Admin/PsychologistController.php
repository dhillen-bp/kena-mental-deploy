<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Helpers\GCSHelper;
use Illuminate\Support\Str;
use App\Models\Psychologist;
use Illuminate\Http\Request;
use App\Models\PsychologistDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\ValidationException;

class PsychologistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $psychologists = Psychologist::with('psychologistDetail')
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhereHas('psychologistDetail', function ($query) use ($keyword) {
                $query->where('topics', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('university', 'LIKE', '%' . $keyword . '%');
            })
            ->paginate(10);

        return view('admin.psychologists', compact('psychologists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $psychologist_id = IdGenerator::generate(['table' => 'psychologists', 'length' => 4, 'prefix' => 'P']);
        return view('admin.psychologist-add', compact('psychologist_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|unique:psychologists',
                'name' => 'required|max:255',
                'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                'biography' => 'max:255',
            ]);

            $bucketName = 'kenamental_bucket';
            $folderName = 'images/psychologist_photo';
            $imageName = '';
            $columnName = 'photo';

            if ($request->hasFile('photo')) {
                $randomString = Str::uuid()->toString();
                $imageName = GCSHelper::uploadFile($bucketName, $folderName, $request, $columnName, $randomString);
                $validated['photo'] = $imageName;
            }

            $psychologist = Psychologist::create($validated);
            return redirect('/admin/psychologists')->with('success', "Psychologist $psychologist->name Added Successfully!")->withErrors($validated);
        } catch (ValidationException $e) {
            return back()->with('error', 'Error at: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $psychologist = Psychologist::with(['psychologistDetail',])
            ->where('id', $id)
            ->firstOrFail();
        return view('admin.psychologist-show', ['psychologist' => $psychologist]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $psychologist = Psychologist::where('id', $id)->firstOrFail();
        return view('admin.psychologist-edit', ['psychologist' => $psychologist]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                'biography' => 'max:255',
            ]);

            $psychologist = Psychologist::where('id', $id)->firstOrFail();
            $bucketName = 'kenamental_bucket';
            $folderName = 'images/psychologist_photo';
            $imageName = '';
            $columnName = 'photo';
            $oldPhoto = $psychologist->photo;

            if ($request->hasFile('photo')) {
                GCSHelper::deleteFile($bucketName, $folderName, $oldPhoto);

                $imageName = GCSHelper::uploadFile($bucketName, $folderName, $request,  $columnName, $psychologist['id']);
                $validated['photo'] = $imageName;
            } else {
                $validated['photo'] = $imageName;
            }

            $psychologistUpdate = $psychologist->update($validated);
            return redirect('/admin/psychologists')->with('success', "Psychologist $psychologist->name Updated Successfully!")->withErrors($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Error at: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $psychologist = Psychologist::find($id);
        $psychologist->delete();

        $psychologistDetail = PsychologistDetail::where('psychologist_id', $id)->first();
        if ($psychologistDetail) {
            $psychologistDetail->delete();
        }

        return redirect('/admin/psychologists')->with('success', 'Psychologist Deleted Successfully!');
    }

    public function showDeletedPsychologists()
    {
        $deletedPsychologists = Psychologist::with(['psychologistDetail' => function ($query) {
            $query->withTrashed();
        }])->onlyTrashed()->paginate(10);
        return view('admin.psychologist-deleted', ['deletedPsychologists' => $deletedPsychologists]);
    }

    public function destroyPermanent($id)
    {
        try {
            $deletedPsychologist = Psychologist::with(['psychologistDetail' => function ($query) {
                $query->withTrashed();
            }])->onlyTrashed()->first();
            $deletedPsychologist->forceDelete();

            if (optional($deletedPsychologist->psychologistDetail)) {
                optional($deletedPsychologist->psychologistDetail)->forceDelete();
            }

            $bucketName = 'kenamental_bucket';
            $folderName = 'images/psychologist_photo';
            $imageName = $deletedPsychologist->photo;
            GCSHelper::deleteFile($bucketName, $folderName, $imageName);

            return redirect('/admin/deleted-psychologist')->with('success', 'Psychologist Deleted Permanent Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error at: ' . $e->getMessage())->withInput();
        }
    }


    public function restore($id)
    {
        $psychologist = Psychologist::with(['psychologistDetail' => function ($query) {
            $query->withTrashed();
        }])->onlyTrashed()->where('id', $id)->firstOrFail();

        if ($psychologist->psychologistDetail) {
            $psychologist->psychologistDetail->restore();
        }
        $psychologist->restore();

        return redirect('/admin/psychologists')->with('success', 'Psychologist Restored Successfully!');
    }
}
