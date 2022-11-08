<?php

use Carbon\Carbon;

return [

    'server_timezone' => env('SERVER_TZ', 'UTC'),    
    'token_expiry' => env('TOKEN_EXPIRY',(60 * 60 * 24)),// Default 24 hours
    'tinify_key' => env('TINIFY_API_KEY'),
    'google_recaptcha_secret' => env('GOOGLE_RECAPTCHA_SECRET'),

    'allowed_ip_addresses' => [
        'telescope' => env('TELESCOPE_ALLOWED_IP_ADDRESSES'),
    ],    

    'custom_validations' => [
        'double' => 'regex:/^\d+(\.\d{1,2})?$/'
    ],

    'paginate' => '10',
    'system_user_id' => 1,
    'system_role_id' => 1,
    'default_sort_field' => 'id',   
     

    'broadcasting' => [
        'operation' => [
            'add' => 1,
            'edit' => 2,
            'delete' => 3,
            'delete_multiple' => 4,
        ],
    ],

    'activity_log' => [

        'response_type' => [
            0 => 'Create/Delete response',
            1 => 'Update response',
            2 => 'Index response',
            3 => 'Import/Export response'
        ],
        'response_type_enum' => [ '0','1','2','3' ],

    ],

    'calender' => [
        'date' => Carbon::now()->toDateString(),
        'date_format' => Carbon::now()->format('Y-m-d'),
        'time' => Carbon::now()->toTimeString(),
        'date_time' => Carbon::now()->toDateTimeString(),
    ],

    'file' => [
        'name' => Carbon::now('Asia/Kolkata')->format('d_m_Y') . '_' . Carbon::now('Asia/Kolkata')->format('g_i_a'),
    ],    

    'messages' => [

         'user' => [
             'invalid' => 'Invalid credentials.',
         ],

        'forgotpassword_success'=>'Password reset instructions has been sent to your email. Please check your inbox/spam.',
        'forgotpassword_error'=>'Invalid email.',
        'something_wrong' => 'Something went wrong.',
        'login' => [
            'success' => 'Login is successful.',
            'unverified_account' => 'Your account is not verified yet.',
            'wrong_credentials' => 'Invalid combination of email and password.',
            'login_token_failed' => 'Could not create login token.',
            'unauthorized_access' => 'You are not able to access this system.',
        ],
        'password_changed' => "Password has been changed.",
        'invalid_old_password' => "Invalid old password.",
        'similar_password' => "Please enter a password which is not similar then current password.",
        'not_match_confirm_password' => "New password is not match to confirm password.",
        'delete_multiple_error'=>'Please select records.',
        'file_csv_error'=>'please upload csv file.',
        'error_log_not_available'=>'Error logs are not available.',
        'delete_success'=>'Deleted successfully.',
        'create_success'=>'Created successfully.',
        'update_success'=>'Updated successfully.',
        'change_success'=>'Changed successfully.',
        'import_success'=>'Imported successfully.',
        'import_fail'=>'Import failed.',
        'upload_zip_success'=>'Upload successfully.',
        'admin_role_delete_error' => 'Administrator role can not be delete. ',
        'admin_user_delete_error' => 'Super-admin user can not be delete. ',
        'not_found' => 'Not Found'

    ],

    'validation_codes' => [
        'unauthorized' => 401,
        'forbidden' => 403,
        'unprocessable_entity' => 422,
        'unassigned' => 427,
        'ok' => 200,
    ],

    'import_csv_log'=> [
        'status' => [
            '0' => 'Fail',
            '1' => 'Success',
        ],
        'status_enum' => ['0','1'],
    ],

    'image' => [
         'dir_path' => '/storage/',
         'default_types' => 'gif|jpg|png|jpeg',
         'default_img' => 'images/default.png',
    ],

    'permission' => [
         'has_permission' => '1',
         'has_not_permission' => '0',
         'role_guard_name' => 'web',
         'user_has_not_permission' => "You don\'t have permission to this functionality . ",
         'user_already_has_permission' => "Given permission already exists . ",
         'user_clinic_mapping_error' => "User clinic is not mapped yet . ",
         'module_error' => "Module not in request . ",
         'invalid_module_error' => "Invalid module . ",
         'validation_error_status_code' => 422,
         'permission_assign_success' => 'Permission assign successfully.',
         'permission_revert_success' => 'Permission reverted successfully.',
         'permission_revert_failure' => 'Permission revert failed.',
         'permission_not_found' => 'Permission not found.',
         'role_not_found' => 'Role not found.',
    ],
    'user'=>[
            
         'gender' => [ 0 => 'Female', 1 => 'Male' ],
         'gender_enum' => [ 'female'=>"0",'male'=>"1" ],
            
         'status' => [ 0 => 'Inactive', 1 => 'Active' ],
         'status_enum' => [ 'inactive'=>"0",'active'=>"1" ],
            
    ],
];