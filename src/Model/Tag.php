<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 10:19.
 */

namespace Foobargen\Model;

final class Tag
{
    /**
     * @var string Transliterated name
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $paths;

    public function __construct(string $name)
    {
        $this->id = strtr(transliterator_transliterate('Russian-Latin/BGN', $name), [' ' => '-', '\'' => '', '.' => '_', '@' => 'at_', '`' => '']);
        $this->name = $name;
        $this->paths = [];
    }

    public function of(string $path): void
    {
        $this->paths[] = $path;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return count($this->paths);
    }

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }
}