<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 16:35.
 */

namespace Foobargen;

use Foobargen\Exception\MDFileNotFoundException;
use Foobargen\Model\Page;
use Foobargen\Model\TagPool;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

final class Generator
{
    /**
     * @var string
     */
    private $dataDir;

    /**
     * @var string
     */
    private $webDir;

    private $tagPool;

    public function __construct(string $dataDir, string $webDir)
    {
        $this->dataDir = $dataDir;
        $this->webDir = $webDir;
        $this->tagPool = new TagPool();
    }

    public function generate(): void
    {
        $pages = $this->parsePages();

        // render pages
    }

    private function parsePages(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../'.$this->dataDir)->name('*.meta');

        // parse data
        $pages = [];
        $pageFactory = new PageFactory($this->tagPool);
        foreach ($finder as $metaFile) {
            try {
                $pages[] = $pageFactory->createPage($metaFile);
            } catch (MDFileNotFoundException $e) {
                fwrite(STDERR, sprintf('Warning! File %s does have appropriate MD file!', $metaFile->getRealPath()));
            } catch (InvalidArgumentException $e) {
                fwrite(STDERR, sprintf('Invalid configuration: %s', $e->getMessage()));
            }
        }

        // sort
        usort($pages, function (Page $p1, Page $p2) {
            return $p2->getPublishedAt()->format('U') - $p1->getPublishedAt()->format('U');
        });

        return $pages;
    }
}