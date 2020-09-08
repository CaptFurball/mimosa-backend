<?php

namespace App\Services;

use Goutte\Client;

class LinkInfoService
{
    public $url;
    public $title;
    public $description;
    public $imageUrl;

    protected $crawler;

    public function get($url)
    {
        $this->url = $url;

        if(!empty($url)) {
            $client = new Client;
            $this->crawler = $client->request('GET', $url);
        
            $this->title = $this->getTitle();
            $this->description = $this->getDescription();
            $this->imageUrl = $this->getImageUrl();
        }
    }

    protected function getTitle(): string
    {
        $title = $this->filterTag('@name="title"');

        if (empty($title)) {
            $title = $this->filterTag('@property="og:title"');
        } 

        if (empty($title)) {
            $title = $this->filterTag('@name="twitter:title"');
        }

        if (empty($title)) {
            try {
                $title = $this->crawler->filter('title')->text();
            } catch (\Exception $e) {
                return '';
            }
        }

        return ucfirst($title)?: '';
    }

    protected function getDescription(): string
    {
        $description = $this->filterTag('@name="description"');

        if (empty($description)) {
            $description = $this->filterTag('@property="og:description"');
        } 

        if (empty($description)) {
            $description = $this->filterTag('@name="twitter:description"');
        }

        return ucfirst($description)?: '';
    }

    protected function getImageUrl(): string
    {
        $imageUrl = $this->filterTag('@property="og:image"');

        if (empty($imageUrl)) {
            $imageUrl = $this->filterTag('@property="og:image:src"');
        } 

        if (empty($imageUrl)) {
            $imageUrl = $this->filterTag('@name="twitter:image"');
        }

        if (empty($imageUrl)) {
            $imageUrl = $this->filterTag('@name="twitter:image:src"');
        }

        if (preg_match('/(\.png?|\.jpeg?|\.gif|\.jpg)/', $this->url)) {
            $imageUrl = $this->url;
        }

        return $imageUrl?: '';
    }

    protected function filterTag(string $tag)
    {
        try {
            return $this->crawler->filterXpath('//meta[' . $tag . ']')->attr('content');
        } catch (\Exception $e) {
            return null;
        }
    }
}