<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $table = "roles";
    protected $fillable = [
        'name',
        'short_name'
    ];
}
