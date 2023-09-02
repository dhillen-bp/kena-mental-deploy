<?php

namespace App\Http\Controllers\Admin;


use App\Models\Question;
use App\Helpers\GCSHelper;
use App\Models\MentalTest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\ValidationException;

class TestMentalController extends Controller
{
    public function index()
    {

        $tests = MentalTest::get();

        return view('admin.mental-test', compact('tests'));
    }

    public function create()
    {
        $test_id = IdGenerator::generate(['table' => 'mental_tests', 'length' => 5, 'prefix' => 'TS']);
        return view('admin.mental-test-add', compact('test_id'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|unique:mental_tests',
                'title' => 'required|max:255',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'desc' => 'max:255',
            ]);

            $bucketName = 'kenamental_bucket';
            $folderName = 'images/test_thumbnail';
            $imageName = '';
            $columnName = 'thumbnail';

            if ($request->hasFile('thumbnail')) {
                // Upload to Google Cloud Storage
                $imageName = GCSHelper::uploadFile($bucketName, $folderName, $request, $columnName, $validated['id']);
            }

            $validated['thumbnail'] = $imageName;
            $test = MentalTest::create($validated);

            return redirect('/admin/mental-test')->with('success', "MentalTest $test->title Added Successfully!");
        } catch (ValidationException $e) {
            return back()->with('error', 'Error at: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $test = MentalTest::find($id);
        return view('admin.mental-test-edit', compact('test'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'desc' => 'max:255',
            ]);

            $test = MentalTest::find($id);
            $oldPhoto = $test->thumbnail;
            $bucketName = 'kenamental_bucket';
            $folderName = 'images/test_thumbnail';
            $columnName = 'thumbnail';

            if ($request->hasFile('thumbnail')) {
                GCSHelper::deleteFile($bucketName, $folderName, $oldPhoto);

                $imageName = GCSHelper::uploadFile($bucketName, $folderName, $request,  $columnName, $test['id']);
                $validated['thumbnail'] = $imageName;
            } else {
                $validated['thumbnail'] = $oldPhoto;
            }

            $testUpdate = $test->update($validated);
            return redirect('/admin/mental-test')->with('success', "MentalTest $test->title Updated Successfully!")->withErrors($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Error at: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $test = MentalTest::find($id);
        $test->delete();

        // $psychologistDetail = PsychologistDetail::where('psychologist_id', $id)->first();
        // if ($psychologistDetail) {
        //     $psychologistDetail->delete();
        // }

        return redirect('/admin/mental-test')->with('success', 'Mental Test Deleted Successfully!');
    }

    public function showDeletedTest()
    {
        $deletedTests = MentalTest::onlyTrashed()->paginate(5);
        return view('admin.mental-test-deleted', ['deletedTests' => $deletedTests]);
    }

    public function destroyPermanent($id)
    {
        try {
            $deletedTests = MentalTest::onlyTrashed()->find($id);

            $deletedTests->forceDelete();

            $bucketName = 'kenamental_bucket';
            $folderName = 'images/test_thumbnail';
            $imageName = $deletedTests->thumbnail;

            // Hapus file dari folder di GCS menggunakan helper deleteFile
            GCSHelper::deleteFile($bucketName, $folderName, $imageName);

            return redirect('/admin/deleted-mental-test')->with('success', 'MentalTest Deleted Permanent Successfully!');
        } catch (\Exception $e) {
            return redirect('/admin/deleted-mental-test')->with('error', 'An error occurred while deleting the MentalTest permanently.');
        }
    }


    public function restore($id)
    {
        $test = MentalTest::onlyTrashed()->find($id);

        $test->restore();

        return redirect('/admin/mental-test')->with('success', 'MentalTest Restored Successfully!');
    }
}
