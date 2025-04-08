<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class FileImage.
 *
 * @property int    $id
 * @property string $uuid
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property string $size
 * @property int    $uploaded_by
 * @property bool   $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $inactivated_at
 */
class FilesImage extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'file.image';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'name',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'inactivated_at',
    ];
}
