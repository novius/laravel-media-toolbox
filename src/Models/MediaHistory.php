<?php

namespace Novius\MediaToolbox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class MediaHistory
 * @package Novius\MediaToolbox\Models
 *
 * @property int id
 * @property string picture
 * @property string query_md5
 * @property string url
 * @property string path
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MediaHistory extends Model
{
    protected $table = 'media_toolbox_medias';

    protected $guarded = [
      'id',
    ];
}
