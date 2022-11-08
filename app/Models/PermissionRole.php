<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRole extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'role_id', 'permission_id', 'is_permission'];
}
