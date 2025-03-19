<?php

namespace Boundsoff\BrandNews\Model;

enum ConfigEnableOptions: string
{
    case NotificationsEnabled = 'bf_brand_news/notifications/enabled';
    case BlogFeed = 'bf_brand_news/blog_feed/enabled';
}
