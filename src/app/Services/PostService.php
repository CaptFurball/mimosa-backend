<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Story;
use App\Tag;
use App\Services\LinkInfoService;
use Illuminate\Http\UploadedFile;

class PostService 
{
    public function post(string $body, string $tag = ''): Story
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $story = $user->stories()->create([
            'body' => $body
        ]);

        if (!empty($tags)) {
            $tags = explode(',', $tags);

            foreach ($tags as $tag) {
                try {
                    $existingTag = Tag::where('name', $tag)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    $existingTag = Tag::create(['name' => $tag]);
                }

                $story->tags()->attach($existingTag->id);
            }
        }

        return $story;
    }

    public function postLink(string $body, string $url, string $tag = ''): Story
    {
        $story = $this->post($body, $tag);

        $linkInfoService = new LinkInfoService;
        $linkInfoService->get($url);

        $urlData  = parse_url($url);

        $story->link()->create([
            'user_id' => Auth::user()->id,
            'url' => $url,
            'host' => $urlData['host'],
            'title' => $linkInfoService->title?: null,
            'description' => $linkInfoService->description?: null,
            'image_url' => $linkInfoService->imageUrl?: null
        ]);

        return $story;
    }

    public function postPhoto(string $body, UploadedFile $photo, string $tag = ''): Story
    {
        $story = $this->post($body, $tag);

        $extension = $photo->getClientOriginalExtension(); 
        $filename  = time() . '.' . $extension;
        $directory = 'photo/';

        $photo->storeAs($directory, $filename, 'public');

        $story->photo()->create([
            'user_id' => Auth::user()->id,
            'path' => $directory . $filename
        ]);
        
        return $story;
    }
}