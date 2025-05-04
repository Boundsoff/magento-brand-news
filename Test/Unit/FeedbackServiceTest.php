<?php

namespace Boundsoff\BrandNews\Test\Unit;

use Boundsoff\BrandNews\Helper\Data as Helper;
use Boundsoff\BrandNews\Model\FeedbackService;
use DateTime;
use Laminas\Feed\Reader\Reader;
use Laminas\Feed\Writer\Entry;
use Laminas\Feed\Writer\Feed;
use Laminas\Http\Client as HttpClient;
use Magento\AdminNotification\Model\InboxFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;

class FeedbackServiceTest extends TestCase
{
    /** @var array|Entry[] */
    protected array $data;
    /** @var Feed */
    protected Feed $feed;

    protected function setUp(): void
    {
        $data = [
            [
                'title' => 'All Your Base Are Belong To Us',
                'link' => 'http://127.0.0.1/all-your-base-are-belong-to-us',
                'description' => 'Exposing the difficulty of porting games to English.',
                'pubDate' => '-1 hour',
            ],
            [
                'title' => 'The Cake is a Lie',
                'link' => 'http://127.0.0.1/the-cake-is-a-lie',
                'description' => 'A reference from Portal that became an internet meme.',
                'pubDate' => '-2 hours',
            ],
            [
                'title' => 'Do a Barrel Roll!',
                'link' => 'http://127.0.0.1/do-a-barrel-roll',
                'description' => 'A classic Star Fox 64 quote.',
                'pubDate' => '-3 hours',
            ],
            [
                'title' => 'I Used to Be an Adventurer Like You',
                'link' => 'http://127.0.0.1/i-used-to-be-an-adventurer-like-you',
                'description' => 'A meme from Skyrim, referencing knee injuries.',
                'pubDate' => '-4 hours',
            ],
            [
                'title' => 'It’s Dangerous to Go Alone! Take This.',
                'link' => 'http://127.0.0.1/its-dangerous-to-go-alone-take-this',
                'description' => 'A famous line from The Legend of Zelda.',
                'pubDate' => '-5 hours',
            ],
            [
                'title' => 'Press F to Pay Respects',
                'link' => 'http://127.0.0.1/press-f-to-pay-respects',
                'description' => 'A viral gaming moment from Call of Duty.',
                'pubDate' => '-6 hours',
            ],
            [
                'title' => 'Would You Kindly?',
                'link' => 'http://127.0.0.1/would-you-kindly',
                'description' => 'A famous plot twist phrase from BioShock.',
                'pubDate' => '-7 hours',
            ],
            [
                'title' => 'Finish Him!',
                'link' => 'http://127.0.0.1/finish-him',
                'description' => 'The classic Mortal Kombat finishing move cue.',
                'pubDate' => '-8 hours',
            ],
            [
                'title' => 'Snake? Snake?! SNAAAAKE!!',
                'link' => 'http://127.0.0.1/snake-snake-snaaaake',
                'description' => 'A Metal Gear Solid alert when Snake dies.',
                'pubDate' => '-9 hours',
            ],
            [
                'title' => 'You Died',
                'link' => 'http://127.0.0.1/you-died',
                'description' => 'The infamous Dark Souls screen.',
                'pubDate' => '-10 hours',
            ],
            [
                'title' => 'Leeeeeeeroy Jenkins!',
                'link' => 'http://127.0.0.1/leeroy-jenkins',
                'description' => 'The legendary World of Warcraft meme.',
                'pubDate' => '-11 hours',
            ],
            [
                'title' => 'Hadouken!',
                'link' => 'http://127.0.0.1/hadouken',
                'description' => 'Ryu’s iconic Street Fighter attack.',
                'pubDate' => '-12 hours',
            ],
        ];

        $this->data = array_map(function ($item) {
            $entry = new Entry();
            $entry->setType('rss');

            $entry->setTitle($item['title']);
            $entry->setLink($item['link']);
            $entry->setDateCreated(strtotime($item['pubDate']));
            $entry->setDescription($item['description']);

            return $entry;
        }, $data);

        $this->feed = new Feed();
        $this->feed->setTitle('Brand feed');
        $this->feed->setDescription('Description');
        $this->feed->setLink('http://127.0.0.1/');
        $this->feed->setFeedLink('http://127.0.0.1/feed', 'rss');
        $this->feed->addAuthor([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'uri' => 'http://127.0.0.1/'
        ]);
        $this->feed->setDateModified(time());
        $this->feed->addHub('https://pubsubhubbub.appspot.com/');
    }

    public function testReadBlogFeed(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        Reader::setHttpClient($httpClient);

        array_walk($this->data, fn ($item) => $this->feed->addEntry($item));
        $response = new \Laminas\Http\Response();
        $response->setStatusCode(200)
            ->setContent($this->feed->export('rss'));
        $httpClient->expects($this->once())
            ->method('send')
            ->willReturn($response);

        $inboxFactory = $this->createMock(InboxFactory::class);
        $flagManager = $this->createMock(FlagManager::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $helper = $this->createMock(Helper::class);

        $timezone->expects($this->once())
            ->method('date')
            ->with('-1 month')
            ->willReturn(new DateTime('-1 month'));

        $helper->expects($this->once())
            ->method('isUriAvailable')
            ->willReturn(true);

        $feedbackService = new FeedbackService(
            $inboxFactory,
            $flagManager,
            $timezone,
            $scopeConfig,
            $helper,
        );

        $actual = $feedbackService->readBlogFeed();
        $this->assertCount(count($this->data), $actual);
    }
}
