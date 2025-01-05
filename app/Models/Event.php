<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 *
 * @property int $event_id
 * @property string $event_name
 * @property Carbon $event_start_time
 * @property Carbon $event_end_time
 * @property string|null $location
 * @property string|null $description
 * @property int $event_type_id
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
class Event extends Model
{
    use ObservantTrait;

    protected $table = 'event';

    protected $primaryKey = 'event_id';

    public $timestamps = true;

    protected $casts = [
        'event_start_time' => 'datetime',
        'event_end_time' => 'datetime',
        'event_type_id' => 'int',
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'event_name',
        'event_start_time',
        'event_end_time',
        'location',
        'description',
        'event_type_id',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
