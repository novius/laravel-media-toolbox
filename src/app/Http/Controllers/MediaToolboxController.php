<?php

namespace Novius\MediaToolbox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Novius\MediaToolbox\Support\Media;
use Novius\MediaToolbox\Support\Query;

class MediaToolboxController extends Controller
{
    public function getPicture(Request $request, string $picture)
    {
        $cache = Cache::store(config('imagetoolbox.cache', 'file'));
        $cacheHashKey = $cache->get('medias-'.$picture);
        $pictureInfos = $cache->get($cacheHashKey);

        if (empty($pictureInfos['query']['o'])) {
            abort(404);
        }

        $query = new Query($pictureInfos['query']);

        try {
            $media = new Media($query);
        } catch (\Exception $e) {
            abort(404);
        }

        $media->persists($picture);

        if ($result = $this->stream($media)) {
            return $result;
        }

        abort(404);
    }

    protected function stream(Media $media)
    {
        try {
            return $media->stream();
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }
}
