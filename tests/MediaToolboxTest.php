<?php

namespace Novius\MediaToolbox\Test;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Novius\MediaToolbox\Models\MediaHistory;
use Novius\MediaToolbox\Support\MediaToolbox;

class MediaToolboxTest extends TestCase
{
    public function test_invalid_call()
    {
        $this->clearAllCache();

        $queryBuilder = MediaToolbox::asset('');
        $this->assertEquals('mediatoolbox.query.invalid', $queryBuilder->cacheKey());
    }

    public function test_not_found_media()
    {
        $this->clearAllCache();

        $url = MediaToolbox::asset('unknow_file.jpg')->toHtml();
        $response = $this->get($url);

        $response->assertStatus(404);
    }

    public function test_found_media()
    {
        $this->clearAllCache();

        $path = Storage::disk(config('mediatoolbox.disk'))
            ->path('blank.jpg');
        $queryBuilder = MediaToolbox::asset($path);
        $url = $queryBuilder->toHtml();

        $this->assertTrue(Cache::has($queryBuilder->cacheKey()));
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertHeader('cache-control', 'max-age=604800, public');
    }

    public function test_redirect_old_media()
    {
        $this->clearAllCache();

        $filename = 'blank.jpg';
        $path = Storage::disk(config('mediatoolbox.disk'))
            ->path($filename);
        $queryBuilderHistory = MediaToolbox::asset($path);
        $historyUrl = $queryBuilderHistory->toHtml();

        // Clear cache key previously created
        $this->clearAllCache();

        $queryBuilder = MediaToolbox::asset($path);
        $url = $queryBuilder->toHtml();

        $fakeHistory = new MediaHistory();
        $fakeHistory->picture = pathinfo($historyUrl, PATHINFO_BASENAME);
        $fakeHistory->url = $historyUrl;
        $fakeHistory->query_md5 = md5((string) $queryBuilderHistory);
        $fakeHistory->path = $filename;
        $fakeHistory->save();

        $recentHistory = new MediaHistory();
        $recentHistory->picture = pathinfo($url, PATHINFO_BASENAME);
        $recentHistory->url = $url;
        $recentHistory->query_md5 = md5((string) $queryBuilder);
        $recentHistory->path = $filename;
        $recentHistory->save();

        $response = $this->get($historyUrl);

        $response->assertRedirect($url);
        $response->assertStatus(301);
    }

    public function test_different_cache_keys()
    {
        $this->clearAllCache();

        $path = Storage::disk(config('mediatoolbox.disk'))
            ->path('blank.jpg');

        $queryBuilder = MediaToolbox::asset($path);
        $secondQueryBuilder = MediaToolbox::asset($path)->quality(50);
        $thirdQueryBuilder = MediaToolbox::asset($path)->quality(30);

        $this->assertNotEquals($queryBuilder->cacheKey(), $secondQueryBuilder->cacheKey());
        $this->assertNotEquals($secondQueryBuilder->cacheKey(), $thirdQueryBuilder->cacheKey());
    }

    public function test_same_cache_keys_with_same_calls()
    {
        $this->clearAllCache();

        $path = Storage::disk(config('mediatoolbox.disk'))
            ->path('blank.jpg');

        $queryBuilder = MediaToolbox::asset($path);
        $secondQueryBuilder = MediaToolbox::asset($path);

        $this->assertEquals($queryBuilder->cacheKey(), $secondQueryBuilder->cacheKey());
    }

    public function test_same_cache_keys_with_same_calls_but_different_params_order()
    {
        $this->clearAllCache();

        $path = Storage::disk(config('mediatoolbox.disk'))
            ->path('blank.jpg');

        $queryBuilder = MediaToolbox::asset($path)->quality(50)->width(30)->height(30);
        $secondQueryBuilder = MediaToolbox::asset($path)->width(30)->height(30)->quality(50);

        $this->assertEquals($queryBuilder->cacheKey(), $secondQueryBuilder->cacheKey());
    }

    protected function clearAllCache()
    {
        Artisan::call('cache:clear');
    }
}
