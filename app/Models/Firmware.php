<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firmware extends Model
{
    use HasFactory;
    protected $table = "device";
    protected $primaryKey = "id";
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'id_device','nama_device','token','gps','email'
    ];
    protected $hidden = [
        // 'password',
    ];
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];
}