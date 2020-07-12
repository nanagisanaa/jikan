<?php

namespace Jikan\Parser\Recommendations;

use Jikan\Model\Anime\AnimeReview;
use Jikan\Model\Manga\MangaReview;
use Jikan\Model\Recommendations\RecentRecommendations;
use Jikan\Model\Recommendations\RecommendationListItem;
use Jikan\Model\Reviews\RecentReviews;
use Jikan\Parser\Anime\AnimeReviewParser;
use Jikan\Parser\Manga\MangaReviewParser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class RecentRecommendationsParser
 *
 * @package Jikan\Parser\Top
 */
class RecentRecommendationsParser
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * CharacterListItemParser constructor.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @return RecentReviews
     * @throws \Exception
     */
    public function getModel(): RecentRecommendations
    {
        return RecentRecommendations::fromParser($this);
    }

    /**
     * @return array
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getRecentRecommendations(): array
    {
        return $this->crawler
            ->filterXPath('//*[@id="content"]/div[3]/div[contains(@class, "spaceit borderClass")]')
            ->each(
                function (Crawler $crawler) {
                    return RecommendationListItem::fromParser(new RecommendationListItemParser($crawler));
                }
            );
    }

    /**
     * @return array
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getUserRecommendations(): array
    {

        return $this->crawler
            ->filterXPath('//*[@id="content"]/div/div[2]/div/div[2]/div[contains(@class, "spaceit borderClass")]')
            ->each(
                function (Crawler $crawler) {
                    return RecommendationListItem::fromParser(new RecommendationListItemParser($crawler));
                }
            );
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        $node = $this->crawler
            ->filterXPath('//*[@id="content"]/div/div[2]/div/div[2]/div[1]/a[contains(text(), "More Recommendations")]');

        if ($node->count()) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getLastPage(): int
    {
        return 1;
        $pages = $this->crawler
            ->filterXPath('//*[@id="content"]/table/tr/td[2]/div[2]/div[contains(@class, "mt12 mb12")]/div[contains(@class, "pagination")]');

        if (!$pages->count()) {
            return 1;
        }

        $pages = $pages
            ->filterXPath('//a[contains(@class, "link")]')
            ->last();

        if (empty($pages)) {
            return 1;
        }

        preg_match('~\?offset=(\d+)$~', $pages->attr('href'), $page);

        return ((int) $page[1]/100) + 1;
    }
}
