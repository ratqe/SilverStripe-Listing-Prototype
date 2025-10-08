<?php

namespace App\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use App\NewsArticle;

class LatestNewsBlock extends BaseElement
{
    private static $table_name = 'LatestNewsBlock';

    private static $singular_name = 'Latest News Block';
    private static $plural_name = 'Latest News Blocks';

    private static $description = 'Shows the latest news articles';

    public function getType()
    {
        return 'Latest News';
    }

    /**
     * Fetch latest NewsArticle pages
     *
     * @param int $limit
     * @return \SilverStripe\ORM\DataList
     */
    public function LatestNews($limit = 3)
    {
        return NewsArticle::get()
            ->sort('Created', 'DESC')
            ->limit($limit);
    }
}