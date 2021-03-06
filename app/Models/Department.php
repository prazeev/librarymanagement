<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
  use HasFactory;

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'department_users');
  }
}
