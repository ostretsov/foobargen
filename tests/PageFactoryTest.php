<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 17:07.
 */

namespace Tests;

use Foobargen\Model\TagPool;
use Foobargen\PageFactory;
use PHPUnit\Framework\TestCase;

class PageFactoryTest extends TestCase
{
    public function testValidBlogPost()
    {
        $tagPool = new TagPool();
        $fileInfo = new \SplFileInfo(__DIR__.'/PageFactoryTestFixtures/ValidBlogPost/post_01.meta');

        $page = (new PageFactory($tagPool))->createPage($fileInfo);
        $this->assertEquals('/2017/06/04/selfcast-01/', $page->getPath());
        $this->assertInstanceOf(\DateTime::class, $page->getPublishedAt());
        $this->assertEquals('/archive/self1.mp3', $page->getAudioURL());
        $this->assertEquals('3Mc1h_-gYqE', $page->getYouTubeId());
        $this->assertEquals("<h1>Header</h1>\n<h2>sub header</h2>\n<p>Foobar blog post!</p>", $page->getContent());
        $this->assertCount(10, $page->getTags());
    }
}