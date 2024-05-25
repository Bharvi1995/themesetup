<?php

namespace App\Imports;

use App\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;

// class AdminUsersImport implements ToModel,WithHeadingRow
class AdminUsersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return User|null
    */
	private $rows = 0;

	// public function model(array $row) {
	// 	$admin = new Admin([
	// 		'name'  => $row['name'],
	// 		'email' => $row['email'],
	// 		'password' => bcrypt($row['password']),
	// 		'is_active' => $row['status'],
	// 		'is_otp_required' => $row['otp'],
	// 	]);
	// 	return $admin->assignRole($row['role_id']);
	// }


	public function collection(Collection $rows)
    {
		$validator = Validator::make($rows->toArray(), [
			'*.name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
			'*.email' => 'required|string|email|max:255|unique:admins'
		]);
		 if ($validator->fails()) {
			$error_per_field = "";
			$all_errors = json_decode(json_encode($validator->errors()), true);
			foreach ($all_errors as $error_field => $field_errors) {
				$error_per_field = implode(' ', $field_errors);
			}
            \Session::put('error', $error_per_field);
		 } else {
			 foreach ($rows->toArray() as $row) {
				 $admin = Admin::create([
					 'name'  => $row['name'],
					 'email' => $row['email'],
					 'password' => bcrypt($row['password']),
					 'is_active' => strval($row['status']),
					 'is_otp_required' => strval($row['otp']),
				 ]);
				 ++$this->rows;
				 $admin->assignRole($row['role_id']);
			}
		}
	}

	public function getRowCount(): int
    {
        return $this->rows;
    }
}
