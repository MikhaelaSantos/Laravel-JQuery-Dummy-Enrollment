<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Student([
          'first_name'  => $row[1],
          'last_name'   => $row[2],
          'middle_name' => $row[3],
          'birthdate'   => $row[4],
          'age'         => $row[5],
        ]);
    }

    // /**
    //  * @return array
    //  */
    // public function rules(): array
    // {
    //     return [
    //       '1'  => 'required',
    //       '2'  => 'required',
    //       '3'  => 'required',
    //       '4'  => 'required|numeric'
    //     ];
    // }

    // /**
    //  * @return array
    //  */
    // public function customValidationMessages()
    // {
    //     return [
    //         '1.required' => 'The first name is required.',
    //         '2.required' => 'The last name is required.',
    //         '3.required' => 'The middle name is required.',
    //         '4.required' => 'The age is required.',
    //         '4.numeric'  => 'The age must be numeric.',
            
    //     ];
    // }
}
