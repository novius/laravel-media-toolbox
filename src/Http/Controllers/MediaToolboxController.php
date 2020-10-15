<?php

namespace Novius\MediaToolbox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Novius\MediaToolbox\Models\MediaHistory;
use Novius\MediaToolbox\Support\Media;
use Novius\MediaToolbox\Support\Query;

class MediaToolboxController extends Controller
{
    public function getPicture(Request $request, string $picture)
    {
        $cacheHashKey = Cache::get('mediatoolbox.media.'.$picture, '');
        if (empty($cacheHashKey)) {
            $newMediaVersion = $this->findNewMediaVersionOrFail($picture);
            if (!empty($newMediaVersion->id) && Storage::disk(config('mediatoolbox.disk', 'public'))->exists($newMediaVersion->path)) {
                return redirect($newMediaVersion->url, 301);
            }

            abort(404);
        }

        $pictureInfos = Cache::get($cacheHashKey);

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

        // Save an history version of media (for future 301 redirections when expired)
        $media->saveHistory($request, $query, $picture);

        if ($result = $this->stream($media)) {
            return $result;
        }

        abort(404);
    }

    /**
     * Find a more recent version of picture from medias history or fail (404 error)
     *
     * @param string $picture
     * @return mixed
     */
    protected function findNewMediaVersionOrFail(string $picture)
    {
        $history = MediaHistory::where('picture', $picture)->first();
        if (empty($history->id)) {
            abort(404);
        }

        // Delete older history entries
        MediaHistory::where('query_md5', $history->query_md5)
            ->where('id', '<', $history->id)->delete();

        return MediaHistory::where('query_md5', $history->query_md5)
            ->orderByDesc('id')
            ->first();
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
