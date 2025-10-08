<?php

namespace App;

class NewsArticle extends ElementalPage
{
    private static $table_name = 'NewsArticle';

    private static $db = [
        'Summary' => 'Text',
    ];
}