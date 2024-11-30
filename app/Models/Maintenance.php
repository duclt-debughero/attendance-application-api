<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Maintenance
 *
 * @property int $maintenance_id
 * @property string|null $body
 * @property Carbon|null $start_time
 * @property Carbon|null $end_time
 * @property int $disable_flg
 * @property int|null $del_flg
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @package App\Models
 */
class Maintenance extends Model
{
    use ObservantTrait;

    public $timestamps = false;

    protected $table = 'maintenance';

    protected $primaryKey = 'maintenance_id';

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'disable_flg' => 'int',
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'body',
        'start_time',
        'end_time',
        'disable_flg',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
