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

    /**
     * Creates new crawler instance based on url
     * 
     * @param string $url The url of the designated site to be scrape
     */
    public function get(string $url)
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

    /**
     * Scrape for title. This method includes strategies as to where 
     * to look for a title in the site.
     * 
     * @return string The title or empty string if not found.
     */
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

    /**
     * Scrape for description. This method includes strategies as to where 
     * to look for a description in the site.
     * 
     * @return string The description or empty string if not found.
     */
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

    /**
     * Scrape for image url. This method includes strategies as to where 
     * to look for a image url in the site. If none is found and url ends 
     * with .png .jpsg .gif or .jpg, will use the url as image url
     * 
     * @return string The  or empty string if not found.
     */
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

    /**
     * Helper method for crawler to filter
     * 
     * @param string $tag The tag to attribute to filter in format @AttrName="Tag"
     *                    eg: @name="description"
     * 
     * @return mixed Returns a string if found otherwise null is given
     */
    protected function filterTag(string $tag)
    {
        try {
            return $this->crawler->filterXpath('//meta[' . $tag . ']')->attr('content');
        } catch (\Exception $e) {
            return null;
        }
    }
}