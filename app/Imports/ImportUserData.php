<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Role;
use App\Models\LeaveCredit;
use App\Models\Department;

class ImportUserData implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $columns = ['employee_id', 'first_name', 'last_name', 'middle_name', 'email', 'employment_status', 'job_title', 'department', 'date_of_employment', 'role', 'forced', 'vacation', 'sick', 'maternity', 'paternity', 'special_privilege', 'solo_parent', 'study', 'vaws', 'rehabilitation', 'special_leave_benefits_for_women', 'calamity', 'exit_pass'];
        
        $allBlank = true;
        foreach ($columns as $column) {
            // If _any_ column has a value, update `$allBlank` to `false`
            if ($row[$column] !== null) {
                $allBlank = false;
            }
        }
        
        // If `$allBlank` is `true`, you hit a phantom row; return `null`
        if ($allBlank) {
            return null;
        }
        
        $user = User::create([
            'employee_id' => $row['employee_id'],
            'first_name' => $row['first_name'],
            'last_name'=> $row['last_name'],
            'middle_name'=> $row['middle_name'],
            'email'=> $row['email'],
            'password'=> $row['employee_id'],
        ]);

        Department::create([
            'user_id'=> $user['id'],
            'employment_status'=> $row['employment_status'],
            'job_title'=> $row['job_title'],
            'department_name'=> $row['department'],
            'date_of_employment'=> \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_employment']),
        ]);

        Role::create([
            'user_id'=> $user['id'],
            'role'=> $row['role'],
        ]);

        if ($row['role'] != 'Employee') {
            Role::create([
                'user_id'=> $user['id'],
                'role'=> 'Employee',
            ]);
        }

        $types = ['Forced', 'Vacation', 'Sick', 'Maternity', 'Paternity', 'Special Privilege', 'Solo Parent', 'Study', 'VAWC', 'Rehabilitation', 'Special Leave Benefits For Women', 'Calamity', 'Exit Pass'];

        foreach ($types as $index => $type) {
            LeaveCredit::create([
                'user_id' => $user['id'],
                'type' => $type,
                'leave_credit'=> $row[$columns[($index + 10)]],
            ]);
        }
        

        return $user;
    }
}