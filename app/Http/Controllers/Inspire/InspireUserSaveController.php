<?php

namespace App\Http\Controllers\Inspire;

use App\Models\Inspire\InspireUserSave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class InspireUserSaveController extends Controller
{
    /**
     * Display a listing of saved Inspire posts for a user.
     *
     * @group iNspire User Save
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saves = InspireUserSave::with(['user', 'user.image'])->where('user_id', Auth::id())->get();
        return response()->json($saves);
    }

    /**
     * Store a newly created save in storage.
     *
     * @group iNspire User Save
     * @param Request $request
     * @param int $inspire_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $inspire_id)
    {
        $save = InspireUserSave::create([
            'inspire_id' => $inspire_id,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Post saved successfully',
            'save' => $save
        ], 201);
    }

    /**
     * Remove the specified save from storage.
     *
     * @group iNspire User Save
     * @urlParam id int required The ID of the saved post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $save = InspireUserSave::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $save->delete();

        return response()->json(['message' => 'Post unsaved successfully']);
    }
}
