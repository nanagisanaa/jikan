<?php

namespace JikanTest;

use Jikan\Jikan;
use Jikan\Model\Forum\ForumTopic;
use Jikan\Model\News\NewsListItem;
use Jikan\Model\Top\TopAnime;
use Jikan\Model\Top\TopCharacter;
use Jikan\Model\Top\TopManga;
use Jikan\Model\Top\TopPeople;
use Jikan\Model\User\Friend;
use Jikan\Request\Forum\AnimeForumRequest;
use Jikan\Request\Forum\MangaForumRequest;
use Jikan\Request\News\AnimeNewsListRequest;
use Jikan\Request\News\MangaNewsListRequest;
use PHPUnit\Framework\TestCase;

/**
 * Class JikanTest
 */
class JikanTest extends TestCase
{
    /**
     * @var Jikan
     */
    private $jikan;

    public function setUp()
    {
        $this->jikan = new Jikan();
    }

    /**
     * @test
     * @vcr JikanTest_it_gets_anime.yaml
     */
    public function it_gets_anime()
    {
        $anime = $this->jikan->Anime(new \Jikan\Request\Anime\AnimeRequest(21));
        self::assertInstanceOf(\Jikan\Model\Anime\Anime::class, $anime);
    }

    /**
     * @test
     * @vcr JikanTest_it_gets_manga.yaml
     */
    public function it_gets_manga()
    {
        $manga = $this->jikan->Manga(new \Jikan\Request\Manga\MangaRequest(11));
        self::assertInstanceOf(\Jikan\Model\Manga\Manga::class, $manga);
    }

    /**
     * @test
     * @vcr CharacterParserTest.yaml
     */
    public function it_gets_characters()
    {
        $character = $this->jikan->Character(new \Jikan\Request\Character\CharacterRequest(116281));
        self::assertInstanceOf(\Jikan\Model\Character\Character::class, $character);
        self::assertCount(9, $character->getAnimeography());
        self::assertCount(2, $character->getMangaography());
        self::assertCount(4, $character->getVoiceActors());
    }

    /**
     * @test
     * @vcr PersonParserTest.yaml
     */
    public function it_gets_person()
    {
        $person = $this->jikan->Person(new \Jikan\Request\Person\PersonRequest(1));
        self::assertInstanceOf(\Jikan\Model\Person\Person::class, $person);
        self::assertCount(367, $person->getVoiceActingRoles());
        self::assertCount(15, $person->getAnimeStaffPositions());
        self::assertCount(0, $person->getPublishedManga());
    }

    /**
     * @test
     * @vcr SeasonalParserTest.yaml
     */
    public function it_gets_seasonal_anime()
    {
        $seasonal = $this->jikan->Seasonal(new \Jikan\Request\Seasonal\SeasonalRequest(2018, 'spring'));
        self::assertInstanceOf(\Jikan\Model\Seasonal\Seasonal::class, $seasonal);
        self::assertCount(234, $seasonal->getAnime());
        self::assertContainsOnlyInstancesOf(\Jikan\Model\Seasonal\SeasonalAnime::class, $seasonal->getAnime());
    }

    /**
     * @test
     * @vcr SeasonalParserTest.yaml
     */
    public function it_gets_user_profile()
    {
        $user = $this->jikan->UserProfile(new \Jikan\Request\User\UserProfileRequest('sandshark'));
        self::assertInstanceOf(\Jikan\Model\User\UserProfile::class, $user);
        self::assertInstanceOf(\Jikan\Model\User\AnimeStats::class, $user->getAnimeStats());
        self::assertInstanceOf(\Jikan\Model\User\MangaStats::class, $user->getMangaStats());
    }

    /**
     * @test
     * @vcr ScheduleParserTest.yaml
     */
    public function it_gets_schedule()
    {
        $schedule = $this->jikan->Schedule(new \Jikan\Request\Schedule\ScheduleRequest());
        self::assertInstanceOf(\Jikan\Model\Shedule\Schedule::class, $schedule);
    }

    /**
     * @test
     * @vcr FriendsParserTest.yaml
     */
    public function it_gets_friends()
    {
        $friends = $this->jikan->UserFriends(new \Jikan\Request\User\UserFriendsRequest('morshuwarrior'));
        self::assertContainsOnlyInstancesOf(Friend::class, $friends);
        self::assertCount(100, $friends);
        self::assertContains('sandshark', $friends);
        self::assertContains('mangalicker94', $friends);
        self::assertContains('Moune-Chan', $friends);

        // Second page
        $friends = $this->jikan->UserFriends(new \Jikan\Request\User\UserFriendsRequest('morshuwarrior', 1));
        self::assertContainsOnlyInstancesOf(Friend::class, $friends);
        self::assertCount(100, $friends);
        self::assertContains('sword123', $friends);
        self::assertContains('Impactaction', $friends);

        // Empty page
        // Second page
        $friends = $this->jikan->UserFriends(new \Jikan\Request\User\UserFriendsRequest('morshuwarrior', 100));
        self::assertContainsOnlyInstancesOf(Friend::class, $friends);
        self::assertCount(0, $friends);
    }

    /**
     * @test
     * @vcr ProducerParserTest.yaml
     */
    public function it_gets_producer()
    {
        $producer = $this->jikan->Producer(new \Jikan\Request\Producer\ProducerRequest(1));
        self::assertInstanceOf(\Jikan\Model\Producer\Producer::class, $producer);
    }

    /**
     * @test
     * @vcr CharactersAndStaffParserTest.yaml
     */
    public function it_gets_characters_and_staff()
    {
        $charactersAndStaff = $this->jikan->CharactersAndStaff(
            new \Jikan\Request\Anime\AnimeCharactersAndStaffRequest(35073)
        );
        $staff = $charactersAndStaff->getStaff();
        $characters = $charactersAndStaff->getCharacters();
        self::assertCount(53, $characters);
        self::assertCount(13, $staff);
    }

    /**
     * @test
     * @vcr AnimeVideosParserTest.yaml
     */
    public function it_gets_anime_videos()
    {
        $videos = $this->jikan->AnimeVideos(new \Jikan\Request\Anime\AnimeVideosRequest(1));
        $promos = $videos->getPromos();
        $episodes = $videos->getEpisodes();
        self::assertCount(3, $promos);
        self::assertCount(26, $episodes);
    }

    /**
     * @test
     * @vcr NewsParserTest.yaml
     */
    public function it_gets_manga_news_list()
    {
        $items = $this->jikan->MangaNewsList(new MangaNewsListRequest(2));
        self::assertCount(14, $items);
        self::assertContainsOnlyInstancesOf(NewsListItem::class, $items);
    }

    /**
     * @test
     * @vcr NewsParserTest.yaml
     */
    public function it_gets_anime_news_list()
    {
        $items = $this->jikan->AnimeNewsList(new AnimeNewsListRequest(21));
        self::assertCount(30, $items);
        self::assertContainsOnlyInstancesOf(NewsListItem::class, $items);
    }

    /**
     * @test
     * @vcr AnimeSearchParserTest.yaml
     */
    public function it_gets_anime_search()
    {
        $search = $this->jikan->AnimeSearch(new \Jikan\Request\Search\AnimeSearchRequest('Fate'));
        self::assertCount(50, $search->getResults());
        self::assertContainsOnlyInstancesOf(\Jikan\Model\Search\AnimeSearchListItem::class, $search->getResults());
    }

    /**
     * @test
     * @vcr MangaCharacterListParserTest.yaml
     */
    public function it_gets_manga_characters()
    {
        $characters = $this->jikan->MangaCharacters(new \Jikan\Request\Manga\MangaCharactersRequest(2));
        self::assertCount(70, $characters);
        self::assertContainsOnlyInstancesOf(\Jikan\Model\Manga\CharacterListItem::class, $characters);
    }

    /**
     * @test
     * @vcr TopAnimeParserTest.yaml
     */
    public function it_gets_top_anime()
    {
        $anime = $this->jikan->TopAnime();
        self::assertCount(50, $anime);
        self::assertContainsOnlyInstancesOf(TopAnime::class, $anime);
        self::assertContains('Fullmetal Alchemist: Brotherhood', $anime);
        self::assertContains('Mushishi Zoku Shou: Suzu no Shizuku', $anime);
    }

    /**
     * @test
     * @vcr TopMangaParserTest.yaml
     */
    public function it_gets_top_manga()
    {
        $manga = $this->jikan->TopManga();
        self::assertCount(50, $manga);
        self::assertContainsOnlyInstancesOf(TopManga::class, $manga);
        self::assertContains('Berserk', $manga);
        self::assertContains('Shigatsu wa Kimi no Uso', $manga);
    }

    /**
     * @test
     * @vcr TopCharacterParserTest.yaml
     */
    public function it_gets_top_characters()
    {
        $characters = $this->jikan->TopCharacters();
        self::assertCount(50, $characters);
        self::assertContainsOnlyInstancesOf(TopCharacter::class, $characters);
        self::assertContains('Lamperouge, Lelouch', $characters);
        self::assertContains('Usui, Takumi', $characters);
    }

    /**
     * @test
     * @vcr TopPeopleParserTest.yaml
     */
    public function it_gets_top_people()
    {
        $people = $this->jikan->TopPeople();
        self::assertCount(50, $people);
        self::assertContainsOnlyInstancesOf(TopPeople::class, $people);
        self::assertContains('Hanazawa, Kana', $people);
        self::assertContains('Asano, Inio', $people);
    }

    /**
     * @test
     * @vcr ForumTopicParserTest.yaml
     */
    public function it_gets_anime_forum()
    {
        $topics = $this->jikan->AnimeForum(new AnimeForumRequest(21));
        self::assertCount(15, $topics);
        self::assertContainsOnlyInstancesOf(ForumTopic::class, $topics);
        self::assertContains('One Piece Episode 461 Discussion', $topics);
        self::assertContains('One Piece Episode 101 Discussion', $topics);
    }

    /**
     * @test
     * @vcr MangaForumTopicParserTest.yaml
     */
    public function it_gets_manga_forum()
    {
        $topics = $this->jikan->MangaForum(new MangaForumRequest(21));
        self::assertCount(15, $topics);
        self::assertContainsOnlyInstancesOf(ForumTopic::class, $topics);
        self::assertContains('Death Note Chapter 60 Discussion', $topics);
        self::assertContains('Death Note Chapter 21 Discussion', $topics);
    }
}
