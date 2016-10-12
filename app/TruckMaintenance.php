<?php

namespace App;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\TruckMaintenance
 *
 * @property integer $id
 * @property integer $truck_id
 * @property string $maintenance_type
 * @property integer $cost
 * @property integer $odometer
 * @property string $remarks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Truck $truck
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereTruckId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereMaintenanceType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereCost($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereOdometer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereRemarks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $store_id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereStoreId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereDeletedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TruckMaintenance whereDeletedAt($value)
 */
class TruckMaintenance extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'truck_maintenance';

    protected $fillable = [
        'store_id', 'truck_id', 'maintenance_type', 'cost', 'odometer', 'remarks'
    ];

    public function hId() {
        return HashIds::encode($this->attributes['id']);
    }

    public function getTruck() {
        return $this->belongsTo('App\Truck');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $user = Auth::user();
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });

        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });

        static::deleting(function($model)
        {
            $user = Auth::user();
            $model->deleted_by = $user->id;
            $model->save();
        });
    }
}
