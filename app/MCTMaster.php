<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class MCTMaster extends Model
{
  protected $table='MCTMaster';
  public $timestamps=false;
  public $dates=['MCTDate'];
  protected $dateFormat = 'M d, Y';
  public $increments=false;
  protected $primaryKey='MCTNo';
  public $incrementing = false;
  public function users()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
  }
  public function ReceiverMCT()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType')->wherePivot('SignatureType', 'ReceivedBy');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s.u', $date)->diffForHumans();
  }
}
