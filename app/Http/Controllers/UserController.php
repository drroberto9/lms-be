<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportUserData;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\LeaveCredit;

class UserController extends Controller
{

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'string|max:255',
            'employee_id' => 'required|string|unique:users',
            'password' => 'required|string',
            'employment_status' => 'required|in:Contract of Service,Permanent',
            'job_title' => 'required|in:Faculty,Office Staff,Utility Personnel,Watchman,Driver',
            'department_name' => 'required|string|max:255',
            'date_of_employment' => 'required|date',
            'role' => 'required|string',
            'leave_credit' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 403);
        }

        $newRecord = User::create([
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'password' => bcrypt($request->input('password')),
            'employee_id' => $request->input('employee_id'),
        ]);

        Department::create([
            'user_id' => $newRecord['id'],
            'employment_status' => $request->input('employment_status'),
            'job_title' => $request->input('job_title'),
            'department_name' => $request->input('department_name'),
            'date_of_employment' => $request->input('date_of_employment'),
        ]);

        Role::create([
            'user_id' => $newRecord['id'],
            'role' => $request->input('role'),
        ]);

        LeaveCredit::create([
            'user_id' => $newRecord['id'],
            'leave_credit' => $request->input('leave_credit'),
        ]);

        $user = User::with('departments', 'roles', 'leaveCredits')->where('id', $newRecord['id'])->first();

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    public function import(Request $request)
    {
        set_time_limit(120);

        // Validate the file (optional)
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');

        try {
            // Use the Excel facade to import data
            Excel::import(new ImportUserData, $file);

            return response()->json(['message' => 'Data imported successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request) {
        $query = User::query()->with('departments', 'roles', 'leaveCredits');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->employment_status) {
            $query->where('employment_status', $request->employment_status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate($request->per_page));
    }

    public function show($id) {
        $user = User::with('departments', 'roles', 'leaveCredits')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'string|max:255',
            'employee_id' => 'required|string|unique:users',
            'password' => 'required|string',
            'employment_status' => 'required|in:Contract of Service,Permanent',
            'job_title' => 'required|in:Faculty,Office Staff,Utility Personnel,Watchman,Driver',
            'department_name' => 'required|string|max:255',
            'date_of_employment' => 'required|date',
            'role' => 'required|string',
            'leave_credit' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'employment_status' => 'required|in:Contract of Service,Permanent',
            'job_title' => 'required|in:Faculty,Office Staff,Utility Personnel,Watchman,Driver',
            'department' => 'required|string|max:255',
            'date_of_employment' => 'date',
        ]);

        $user->update($request->all());
            
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    

    public function remove($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete(); // Soft delete

        return response()->json(['message' => 'User soft deleted successfully']);
    }
}
