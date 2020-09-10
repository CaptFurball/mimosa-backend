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
    /**
     * To post a story
     * 
     * @param string $body The text content of a story
     * @param string $tags A string of comma seperated tags eg: 'startup,vimigo'
     * 
     * @return Story The created story Eloquent object
     */
    public function post(string $body, string $tags = ''): Story
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

    /**
     * To post a link type story. This will trigger web scraping as well
     * 
     * @param string $body The text content of a story
     * @param string $url The URL to be shared as a story, has to be a fully qualified URL
     * @param string $tags A string of comma seperated tags eg: 'startup,vimigo'
     * 
     * @return Story The created story Eloquent object
     */
    public function postLink(string $body, string $url, string $tags = ''): Story
    {
        $story = $this->post($body, $tags);

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

    /**
     * To post a Photo type story. The logic on how the photo file is to be processed,
     * renamed and storing location is dictated here. In future, image post processing 
     * should be trigger here as well.
     * 
     * @param string $body The text content of a story
     * @param UploadedFile $photo The photo of the story to be posted
     * @param string $tags A string of comma seperated tags eg: 'startup,vimigo'
     * 
     * @return Story The created story Eloquent object
     */
    public function postPhoto(string $body, UploadedFile $photo, string $tags = ''): Story
    {
        $story = $this->post($body, $tags);

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

    /**
     * To post a Video type story. The logic on how the video file is to be processed,
     * renamed and storing location is dictated here. In future, video post processing 
     * or any transcoding should be trigger here as well.
     * 
     * @param string $body The text content of a story
     * @param UploadedFile $video The video of the story to be posted
     * @param string $tags A string of comma seperated tags eg: 'startup,vimigo'
     * 
     * @return Story The created story Eloquent object
     */
    public function postVideo(string $body, UploadedFile $video, string $tags = ''): Story
    {
        $story = $this->post($body, $tags);

        $extension = $video->getClientOriginalExtension(); 
        $filename  = time() . '.' . $extension;
        $directory = 'video/';

        $video->storeAs($directory, $filename, 'public');

        $story->video()->create([
            'user_id' => Auth::user()->id,
            'path' => $directory . $filename
        ]);
        
        return $story;
    }

    /**
     * To post a Share type story. This is a special case where user wants to share
     * stories from another user or from himself. No extra input other than story id
     * as the body content of this new post will be auto-generated
     * 
     * @param int $storyId The ID of the story to be posted
     * 
     * @return Story The created story Eloquent object
     */
    public function sharePost(int $storyId)
    {
        $sharedStory = Story::with(['user', 'tags'])->find($storyId);

        $body = Auth::user()->name . ' has shared a post from ' . $sharedStory->user->name;
        $tags = '';

        if ($sharedStory->tags && count($sharedStory->tags) > 0) {
            foreach ($sharedStory->tags as $key => $tag) {
                $tags .= $tag->name;
                
                if ($key + 1 < count($sharedStory->tags)) {
                    $tags .= ',';
                }
            }
        }

        $story = $this->post($body, $tags);

        $story->sharedStory()->create([
            'user_id' => Auth::user()->id,
            'shared_story_id' => $storyId
        ]);

        return $story;
    }
}