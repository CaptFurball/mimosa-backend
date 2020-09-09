<?php

namespace App\Services;

use App\Story;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FeedService
{
    protected $relationships = [
        'tags', 
        'likes', 
        'comments.user', 
        'link', 
        'photo', 
        'video', 
        'user', 
        'sharedStory.story.user', 
        'sharedStory.story.link', 
        'sharedStory.story.photo', 
        'sharedStory.story.video'
    ];

    /**
     * Story feed can be broken down into several parts.
     * First layer:  Stories that was posted by followed users in last 3 days
     * Second layer: Stories with tags that user have commented or liked
     * 
     * @return array A list of compiled stories based on likes, comments and follow preferences 
     */
    public function getFeed(): array
    {
        $tags = $this->extractTagsFromCommentsAndLikes();

        $firstLayerStories = $this->getFirstLayerFeed();

        $excludeStoryId = Collection::make($firstLayerStories)->pluck('id')->toArray();
        $secondLayerStories = $this->getSecondLayerFeed($tags, $excludeStoryId);

        $stories = array_merge($firstLayerStories, $secondLayerStories);

        if (empty($stories)) {
            $stories = $this->getRandomFeed();
        }

        return array_values($stories);
    }

    /**
     * Get random story feed
     * 
     * @return array A list of stories
     */
    public function getRandomFeed(): array
    {
        $stories = Story::with($this->relationships)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-3 days')))
            ->orderBy('created_at', 'DESC')
            ->get()
            ->shuffle()
            ->toArray();

        return array_values($stories);
    }

    /**
     * Get feed by a single string tag
     * 
     * @param string $tag Tag name eg: 'ecommerce'
     * 
     * @return array A list of stories
     */
    public function getFeedByTag(string $tag): array
    {
        return Story::with($this->relationships)
            ->whereHas('tags', function($query) use ($tag) {
                $query->where('name', $tag);
            })
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     * Get array by a user id
     * 
     * @param int $userId ID of a user
     * 
     * @return array A list of stories
     */
    public function getFeedByUserId(int $userId): array
    {
        return Story::with($this->relationships)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     * Get feed by the most liked
     * 
     * @return array A list of stories
     */
    public function getPopularFeed(): array
    {
        $stories = Story::with($this->relationships)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->sortByDesc(function($story) {
                return $story->likes->count();
            })
            ->toArray();

        return array_values($stories);
    }

    /**
     * Get feed by the most commented
     * 
     * @return array A list of stories
     */
    public function getDiscussedFeed(): array
    {
        $stories = Story::with($this->relationships)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->sortByDesc(function($story) {
                return $story->comments->count();
            })
            ->toArray();

        return array_values($stories);
    }

    /**
     * Gets stories from followed users
     * 
     * @return array A list of stories
     */
    protected function getFirstLayerFeed(): array
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return $user->following()
            ->with(['followingUser.stories' => function($query) {
                $query->with($this->relationships)
                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-3 days')))
                ->orderBy('created_at', 'DESC');
            }])
            ->get()
            ->pluck('followingUser.stories')
            ->flatten(1)
            ->toArray();
    }

    /**
     * Gets stories from an array of tags
     * 
     * @param array $tags List of tags to search for eg: ['politics', 'business']
     * @param array $excludeId List of ID to exclude from search eg: [1, 3, 4]
     * 
     * @return array A list of stories
     */
    protected function getSecondLayerFeed(array $tags, array $excludeId): array
    {
        //TODO: figure out how to search by tag
        return Story::with($this->relationships)
            ->whereHas('tags', function($query) use ($tags) {
                $query->whereIn('name', $tags);
            })
            ->whereNotIn('id', $excludeId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     * Extracts tags from stories that requesting user has
     * commented and liked. These tags will be used to customize
     * news feed.
     * 
     * @return array An array of unique tags eg: ['politics', 'business', 'sports']
     */
    protected function extractTagsFromCommentsAndLikes(): array
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $tags = [];

        $commentedStoryTags = $user->comments()
            ->with('commentable.tags')
            ->get()
            ->pluck('commentable.tags')
            ->flatten(1)
            ->pluck('name')
            ->toArray();

        $tags = array_merge($tags, $commentedStoryTags);

        $likedStoryTags = $user->likes()
            ->with('likeable.tags')
            ->get()
            ->pluck('likeable.tags')
            ->flatten(1)
            ->pluck('name')
            ->toArray();

        $tags = array_merge($tags, $likedStoryTags);

        return array_unique($tags);
    }
}