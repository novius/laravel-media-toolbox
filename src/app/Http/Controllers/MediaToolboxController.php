<?php

namespace Novius\MediaToolbox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Novius\MediaToolbox\Support\Media;
use Novius\MediaToolbox\Support\Query;

class MediaToolboxController extends Controller
{
    public function getPicture(Request $request)
    {
        $placeholder = config('mediatoolbox.placeholder');

        if ($result = $this->stream($request->all())) {
            return $result;
        }
        if ($result = $this->stream(['o' => $placeholder] + $request->all())) {
            return $result;
        }

        return redirect($placeholder);
    }

    private function stream($parameters)
    {
        try {
            $query = new Query($parameters);
            $media = new Media($query);

            return $media->stream();
        } catch (\Exception $e) {
            return null;
        }
    }
}
