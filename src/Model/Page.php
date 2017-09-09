<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 10:27.
 */

namespace Foobargen\Model;

final class Page
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @var string
     */
    private $audioURL;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $youTubeId;

    /**
     * @var Tag[]
     */
    private $tags;

    /**
     * @param string $path
     * @param \DateTime $publishedAt
     * @param string $audioURL
     * @param string $content MD formatted text
     * @param string $youTubeId
     * @param array $tags
     */
    public function __construct(string $path, \DateTime $publishedAt, string $audioURL, string $content, string $youTubeId = null, array $tags = [])
    {
        $this->path = $path;
        $this->publishedAt = $publishedAt;
        $this->audioURL = $audioURL;
        $this->content = $content;
        $this->youTubeId = $youTubeId;
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return string
     */
    public function getAudioURL(): string
    {
        return $this->audioURL;
    }

    /**
     * @return string
     */
    public function getYouTubeId(): string
    {
        return $this->youTubeId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}