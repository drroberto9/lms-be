<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Leave;


class LeaveController extends Controller
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'end_date' => 'required|date',
            'start_date' => 'required|date',
            'type' => 'required',
            'status' => 'required',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 403);
        }

        $file = $request->file('file');
        $size = $file->getSize();
        $extension = $file->getClientOriginalExtension();
        $fileName = time().'.'.$extension;
        $file->move(storage_path('/uploadedFiles'), $fileName);

        $newRecord = Leave::create([
            'user_id' => Auth::user()->id,
            'assigned_to' => $request->input('assigned_to'),
            'approved_by' => $request->input('approved_by'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'type' => $request->input('type'),
            'status' => $request->input('status'),
            'remarks' => $request->input('remarks'),
            'attachment_name' => $fileName,
            'attachment_type' => $extension,
            'attachment_size' =>  $size
        ]);
        
            
        return response()->json(['message' => 'Leave created successfully', 'leave' => $newRecord]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'end_date' => 'required|date',
            'start_date' => 'required|date',
            'type' => 'required',
            'status' => 'required',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 403);
        }

        $leave = Leave::find($id);

        if (file_exists(storage_path("uploadedFiles/{$leave['attachment_name']}"))) {
            unlink(storage_path("uploadedFiles/{$leave['attachment_name']}"));
        }

        $file = $request->file('file');
        $size = $file->getSize();
        $extension = $file->getClientOriginalExtension();
        $fileName = time().'.'.$extension;
        $file->move(storage_path('/uploadedFiles'), $fileName);

        if (!$leave) {
            return response()->json(['error' => 'Leave not found'], 404);
        }

        $leave->update([
            ...$request->all(),
            'attachment_name' => $fileName,
            'attachment_type' => $extension,
            'attachment_size' =>  $size
        ]);
            
        return response()->json(['message' => 'Leave updated successfully', 'leave' => $leave]);
    }

    public function show($id) {
        return response()->json(Leave::find($id));
    }

    public function index(Request $request) {
        $query = Leave::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate($request->per_page));
    }

    public function download($id) {
        $leave = Leave::find($id);

        return response()->download(storage_path("uploadedFiles/{$leave['attachment_name']}"));
    }
}
