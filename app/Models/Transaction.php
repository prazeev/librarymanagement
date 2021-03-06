<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use HasFactory;
    protected $fillable = ['resolved','user_id','type','expected_closure'];
    public function user() {
      return $this->belongsTo(User::class);
    }
    public function books() {
      return $this->belongsToMany(Book::class,'transaction_books','transaction_id','book_id');
    }
    public function getStatusAttribute() {
      $now = Carbon::now();
      $transaction_expire_after = $now->addDays(5);

      $row_time = Carbon::parse($this->expected_closure);
      $expiring = $row_time->isBefore($transaction_expire_after);
      $expired = $row_time->isBefore($now);
      $goodphase = $row_time->isAfter($transaction_expire_after);
      return (object) [
        "expired" => $expired,
        "expiring" => $expiring,
        "fresh" => $goodphase
      ];
    }
}
