<?php

namespace App\Pages;

use SilverStripe\View\Requirements;
use SilverStripe\CMS\Controllers\ContentController;
use App\NewsArticle;

class HomePageController extends ContentController
{
    private static $allowed_actions = [];

    protected function init(): void
    {
        parent::init();
        // Add CSS/JS included if needed using Requirements::themedCSS / JS
        Requirements::themedCSS('layout');
        Requirements::themedCSS('typography');
        Requirements::themedCSS('form');

        Requirements::themedCSS('style');
    }
}
