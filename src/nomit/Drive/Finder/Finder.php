<?php

namespace nomit\Drive\Finder;

use AppendIterator;
use ArrayIterator;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use LogicException;
use nomit\FileSystem\Exception\IOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use nomit\Drive\Finder\Comparators\DateComparator;
use nomit\Drive\Finder\Comparators\NumberComparator;
use nomit\Drive\Finder\Iterators\Filters\Patterns\FileNameFilter;
use nomit\Drive\Finder\Iterators\Filters\CustomFilter;
use nomit\Drive\Finder\Iterators\Filters\DateRangeFilter;
use nomit\Drive\Finder\Iterators\Filters\DepthRangeFilter;
use nomit\Drive\Finder\Iterators\Filters\ExcludeDirectoryFilter;
use nomit\Drive\Finder\Iterators\Filters\FileTypeFilter;
use nomit\Drive\Finder\Iterators\Filters\GitIgnoreFilter;
use nomit\Drive\Finder\Iterators\Filters\Patterns\FileContentFilter;
use nomit\Drive\Finder\Iterators\Filters\Patterns\PathFilter;
use nomit\Drive\Finder\Iterators\Filters\SizeRangeFilter;
use nomit\Drive\Finder\Iterators\RelativeDirectoryIterator;
use nomit\Drive\Finder\Iterators\SortableIterator;
use SplFileInfo;
use Traversable;

/**
 * Class Finder
 *
 * Facilitates the building of rules to search/find files and directories.
 *
 * It's a thin wrapper for several specialized {@link Iterator} classes.
 *
 * All rules may be invoked several times.
 *
 * All methods return the current Finder object to allow chaining:
 * @example $finder = Finder::create()->files()->name('*.php')->in(__DIR__);
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Finder.php
 *
 * For the original documentation:
 * @see https://symfony.com/doc/current/components/finder.html
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Włodzimierz Gajda <gajdaw@gajdaw.pl>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class Finder implements IteratorAggregate, Countable
{

    public const IGNORE_VCS_FILES = 1;
    public const IGNORE_DOT_FILES = 2;
    public const IGNORE_VCS_IGNORED_FILES = 4;
    /**
     * Version control-software file extensions.
     *
     * @var string[]
     */
    private static array $vcs_patterns = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr',
        '.git', '.hg'];
    /**
     * Whether to search only files, directories, or both.
     *
     * @var int
     */
    private int $mode = 0;
    /**
     * Array of file/directory names that MUST be matched.
     *
     * @var array
     */
    private array $names = [];
    /**
     * Array of excluded file/directory names.
     *
     * @var array
     */
    private array $not_names = [];
    /**
     * Excluded directories.
     *
     * @var array
     */
    private array $exclude = [];
    /**
     * Array of filter callbacks or {@link CustomFilter} instances.
     *
     * @var array
     */
    private array $filters = [];
    /**
     * Array of {@link NumberComparator} instances for directory-depth filtering.
     *
     * @var array
     */
    private array $depths = [];
    /**
     * Array of {@link NumberComparator} instances for file-size filtering.
     *
     * @var array
     */
    private array $sizes = [];
    /**
     * Whether to follow symbolic file/directory links.
     *
     * @var bool
     */
    private bool $follow_links = false;
    /**
     * Whether to reverse the applied sorting.
     *
     * @var bool
     */
    private bool $reverse_sorting = false;
    /**
     * Sort-by {@link SortableIterator} flags or anonymous call-back functions.
     *
     * @see SortableIterator::SORT_BY_NONE
     * @see SortableIterator::SORT_BY_NAME
     * @see SortableIterator::SORT_BY_TYPE
     * @see SortableIterator::SORT_BY_ACCESSED_TIME
     * @see SortableIterator::SORT_BY_CHANGED_TIME
     * @see SortableIterator::SORT_BY_MODIFIED_TIME
     * @see SortableIterator::SORT_BY_NAME_NATURAL
     *
     * @var bool|Closure|int
     */
    private bool|Closure|int $sort = false;
    /**
     * Ignoring-mode flags.
     *
     * @see IGNORE_VCS_FILES
     * @see IGNORE_DOT_FILES
     * @see IGNORE_VCS_IGNORED_FILES
     *
     * @var int
     */
    private int $ignore = 0;
    /**
     * Array of directories to be searched.
     *
     * @var array
     */
    private array $directories = [];
    /**
     * Array of {@link DateComparator} instances for date-range filtering.
     *
     * @var array
     */
    private array $dates = [];
    /**
     * Array of "iterable" {@link \Iterator} instances for filtering.
     *
     * @var array
     */
    private array $iterators = [];
    /**
     * Array of must-match file content-contains patterns.
     *
     * @var array
     */
    private array $contains = [];
    /**
     * Array of must-NOT-match file content-contains patterns.
     *
     * @var array
     */
    private array $not_contains = [];
    /**
     * Array of must-match path patterns for filtering.
     *
     * @var array
     */
    private array $paths = [];
    /**
     * Array of must-NOT-match path patterns for filtering.
     *
     * @var array
     */
    private array $not_paths = [];
    /**
     * Whether to ignore unreadable directories.
     *
     * @var bool
     */
    private bool $ignore_unreadable_directories = false;

    public function __construct()
    {
        $this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;
    }

    /**
     * Creates a new {@link Finder}.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Adds VCS patterns.
     *
     * @param string|string[] $pattern VCS patterns to ignore
     * @see ignoreVCS()
     *
     */
    public static function addVCSPattern(array|string $pattern)
    {
        foreach ((array)$pattern as $p) {
            self::$vcs_patterns[] = $p;
        }

        self::$vcs_patterns = array_unique(self::$vcs_patterns);
    }

    /**
     * Restricts the matching to directories (i.e. no files) only.
     *
     * @return $this
     */
    public function directories()
    {
        $this->mode = FileTypeFilter::ONLY_DIRECTORIES;

        return $this;
    }

    /**
     * Restricts the matching to files (i.e. no directories) only.
     *
     * @return $this
     */
    public function files()
    {
        $this->mode = FileTypeFilter::ONLY_FILES;

        return $this;
    }

    /**
     * Adds tests for the directory depth.
     *
     * @example $finder->depth('> 3') // will start matching at level 3.
     * @example $finder->depth('< 3') // will descend at most 3 levels of directories below the starting point.
     * @example $finder->depth(['>= 3', '< 3']) // will descend at least 3 and less than 3 directories below starting.
     *
     * @see DepthRangeFilter
     * @see NumberComparator
     *
     * @param string|int|string[]|int[] $levels The depth level expression or an array of depth levels
     * @return $this
     */
    public function depth(string|int|array $levels)
    {
        foreach ((array)$levels as $level) {
            $this->depths[] = new NumberComparator($level);
        }

        return $this;
    }

    /**
     * Adds tests for file dates (last modified).
     *
     * The date must be something that {@link strtotime()} is able to parse.
     *
     * @param string|string[] $dates A date range string or an array of date ranges
     * @return $this
     * @example $finder->date('since yesterday');
     * @example $finder->date('until 2 days ago');
     * @example $finder->date('> now - 2 hours');
     * @example $finder->date('>= 2005-10-15');
     * @example $finder->date(['>= 2005-10-15', '<= 2006-05-27']);
     *
     * @see strtotime
     * @see DateRangeFilter
     * @see DateComparator
     *
     */
    public function date(string|array $dates)
    {
        foreach ((array)$dates as $date) {
            $this->dates[] = new DateComparator($date);
        }

        return $this;
    }

    /**
     * Adds rules that files MUST match.
     *
     * Patterns (delimited with slash (/) sign), globs or simple strings may be used.
     *
     * @param string|string[] $patterns A pattern (a regexp, a glob, or a string) or an array of patterns
     * @return $this
     * @example $finder->name('test.php')
     * @example $finder->name(['test.py', 'test.php'])
     *
     * @see FileNameFilter
     *
     * @example $finder->name('*.php')
     * @example $finder->name('/\.php$/') // same as above
     */
    public function name(string|array $patterns)
    {
        $this->names = array_merge($this->names, (array)$patterns);

        return $this;
    }

    /**
     * Adds rules that files must NOT match.
     *
     * @param string|string[] $patterns A pattern (a regexp, a glob, or a string) or an array of patterns
     * @return $this
     * @see FileNameFilter
     *
     */
    public function notName(string|array $patterns)
    {
        $this->not_names = array_merge($this->not_names, (array)$patterns);

        return $this;
    }

    /**
     * Adds tests that file contents MUST match.
     *
     * Strings or PCRE patterns can be used.
     *
     * @param string|string[] $patterns A pattern (string or regexp) or an array of patterns
     * @return $this
     * @example $finder->contains(['dolor', '/ipsum/i'])
     *
     * @see FileContentFilter
     *
     * @example $finder->contains('Lorem ipsum')
     * @example $finder->contains('/Lorem ipsum/i')
     */
    public function contains(string|array $patterns)
    {
        $this->contains = array_merge($this->contains, (array)$patterns);

        return $this;
    }

    /**
     * Adds tests that file contents must NOT match.
     *
     * Strings or PCRE patterns can be used.
     *
     * @param string|string[] $patterns A pattern (string or regular expression) or an array of patterns
     * @return $this
     * @example $finder->notContains(['lorem', '/dolor/i'])
     *
     * @see FileContentFilter
     *
     * @example $finder->notContains('Lorem ipsum')
     * @example $finder->notContains('/Lorem ipsum/i')
     */
    public function notContains(string|array $patterns)
    {
        $this->not_contains = array_merge($this->not_contains, (array)$patterns);

        return $this;
    }

    /**
     * Adds rules that filenames MUST match.
     *
     * You can use patterns (delimited with slash (/) sign) or simple strings.
     *
     * @param string|string[] $patterns A pattern (a regular expression or a string) or an array of patterns
     * @return $this
     * @example $finder->path(['some dir', 'another/dir'])
     *
     * Use only slash (/) as dirname separator.
     *
     * @see FileNameFilter
     *
     * @example $finder->path('some/special/dir')
     * @example $finder->path('/some\/special\/dir/') // same as above
     */
    public function path(string|array $patterns)
    {
        $this->paths = array_merge($this->paths, (array)$patterns);

        return $this;
    }

    /**
     * Adds rules that filenames must NOT match.
     *
     * You can use patterns (delimited with slash (/) sign) or simple strings.
     *
     * @param string|string[] $patterns A pattern (a regexp or a string) or an array of patterns
     * @return $this
     * @example $finder->notPath(['some/file.txt', 'another/file.log'])
     *
     * Use only slash (/) as dirname separator.
     *
     * @see FileNameFilter
     *
     * @example $finder->notPath('some/special/dir')
     * @example $finder->notPath('/some\/special\/dir/') // same as above
     */
    public function notPath(string|array $patterns)
    {
        $this->not_paths = array_merge($this->not_paths, (array)$patterns);

        return $this;
    }

    /**
     * Adds tests for file sizes.
     *
     * @param string|int|string[]|int[] $sizes A size range string or an integer or an array of size ranges
     * @return $this
     * @example $finder->size(4);
     * @example $finder->size(['> 10K', '< 20K'])
     *
     * @see NumberComparator
     * @see SizeRangeFilter
     *
     * @example $finder->size('> 10K');
     * @example $finder->size('<= 1Ki');
     */
    public function size(string|array $sizes)
    {
        foreach ((array)$sizes as $size) {
            $this->sizes[] = new NumberComparator($size);
        }

        return $this;
    }

    /**
     * Excludes directories.
     *
     * Directories passed as argument must be relative to the ones defined with the {@see in()} method.
     *
     * @param string|array $directories A directory path or an array of directories
     * @return $this
     * @example $finder->in(__DIR__)->exclude('ruby');
     *
     * @see ExcludeDirectoryFilter
     *
     */
    public function exclude(string|array $directories)
    {
        $this->exclude = array_merge($this->exclude, (array)$directories);

        return $this;
    }

    /**
     * Excludes "hidden" directories and files (starting with a dot).
     *
     * This option is enabled by default.
     *
     * @param bool $ignore_dot_files
     * @return $this
     * @see ExcludeDirectoryFilter
     *
     */
    public function ignoreDotFiles(bool $ignore_dot_files)
    {
        if ($ignore_dot_files) {
            $this->ignore |= static::IGNORE_DOT_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_DOT_FILES;
        }

        return $this;
    }

    /**
     * Forces the finder to ignore version control directories.
     *
     * This option is enabled by default.
     *
     * @param bool $ignore_vcs
     * @return $this
     * @see ExcludeDirectoryFilter
     *
     */
    public function ignoreVCS(bool $ignore_vcs)
    {
        if ($ignore_vcs) {
            $this->ignore |= static::IGNORE_VCS_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_VCS_FILES;
        }

        return $this;
    }

    /**
     * Forces Finder to obey ".gitignore" and ignore files based on rules listed there.
     *
     * This option is disabled by default.
     *
     * @return $this
     */
    public function ignoreVCSIgnored(bool $ignore_vcs_ignored)
    {
        if ($ignore_vcs_ignored) {
            $this->ignore |= static::IGNORE_VCS_IGNORED_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_VCS_IGNORED_FILES;
        }

        return $this;
    }

    /**
     * Sorts files and directories by an anonymous function.
     *
     * The anonymous function receives two {@link \SplFileInfo} instances to compare.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @param Closure $closure
     * @return $this
     * @see SortableIterator
     *
     */
    public function sort(Closure $closure)
    {
        $this->sort = $closure;

        return $this;
    }

    /**
     * Sorts files and directories by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @param bool $use_natural_sort
     * @return $this
     * @see SortableIterator
     *
     */
    public function sortByName(bool $use_natural_sort = false)
    {
        $this->sort = $use_natural_sort ? SortableIterator::SORT_BY_NAME_NATURAL : SortableIterator::SORT_BY_NAME;

        return $this;
    }

    /**
     * Sorts files and directories by type (directories before files), then by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     * @see SortableIterator
     *
     */
    public function sortByType()
    {
        $this->sort = SortableIterator::SORT_BY_TYPE;

        return $this;
    }

    /**
     * Sorts files and directories by the last accessed time.
     *
     * This is the time that the file was last accessed, read or written to.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     * @see SortableIterator
     *
     */
    public function sortByAccessedTime()
    {
        $this->sort = SortableIterator::SORT_BY_ACCESSED_TIME;

        return $this;
    }

    /**
     * Reverses the sorting.
     *
     * @return $this
     */
    public function reverseSorting()
    {
        $this->reverse_sorting = true;

        return $this;
    }

    /**
     * Sorts files and directories by the last inode changed time.
     *
     * This is the time that the inode information was last modified (permissions, owner, group or other metadata).
     *
     * On Windows, since inode is not available, changed time is actually the file creation time.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     * @see SortableIterator
     *
     */
    public function sortByChangedTime()
    {
        $this->sort = SortableIterator::SORT_BY_CHANGED_TIME;

        return $this;
    }

    /**
     * Sorts files and directories by the last modified time.
     *
     * This is the last time the actual contents of the file were last modified.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     * @see SortableIterator
     *
     */
    public function sortByModifiedTime()
    {
        $this->sort = SortableIterator::SORT_BY_MODIFIED_TIME;

        return $this;
    }

    /**
     * Filters the iterator with an anonymous function.
     *
     * The anonymous function receives a {@link SplFileInfo} and must return false to remove files.
     *
     * @param Closure $closure
     * @return $this
     * @see CustomFilter
     *
     */
    public function filter(Closure $closure)
    {
        $this->filters[] = $closure;

        return $this;
    }

    /**
     * Forces the following of symlinks.
     *
     * @return $this
     */
    public function followLinks()
    {
        $this->follow_links = true;

        return $this;
    }

    /**
     * Tells the finder to ignore unreadable directories.
     *
     * By default, scanning unreadable directories content throws an {@link AccessDeniedError}.
     *
     * @param bool $ignore
     * @return $this
     */
    public function ignoreUnreadableDirs(bool $ignore = true)
    {
        $this->ignore_unreadable_directories = $ignore;

        return $this;
    }

    /**
     * Searches files and directories which match defined rules.
     *
     * @param string|string[] $directories A directory path or an array of directories
     *
     * @return $this
     */
    public function in(string|array $directories)
    {
        $resolved_directories = [];

        foreach ((array)$directories as $directory) {
            if (is_dir($directory)) {
                $resolved_directories[] = $this->normalizeDirectory($directory);
            } else if ($glob = glob($directory, (defined('GLOB_BRACE') ? GLOB_BRACE : 0)
                | GLOB_ONLYDIR | GLOB_NOSORT)) {
                sort($glob);

                $resolved_directories = array_merge($resolved_directories, array_map([$this, 'normalizeDirectory'],
                    $glob));
            } else {
                throw new \RuntimeException(sprintf('No directory with the path "%s" exists.', $directory));
            }
        }

        $this->directories = array_merge($this->directories, $resolved_directories);

        return $this;
    }

    /**
     * Normalizes given directory names by removing trailing slashes.
     *
     * Excluding: (s)ftp:// or ssh2.(s)ftp:// wrapper
     *
     * @param string $directory
     * @return string
     */
    private function normalizeDirectory(string $directory): string
    {
        if ('/' === $directory) {
            return $directory;
        }

        $directory = rtrim($directory, '/' . DIRECTORY_SEPARATOR);

        if (preg_match('#^(ssh2\.)?s?ftp://#', $directory)) {
            $directory .= '/';
        }

        return $directory;
    }

    /**
     * Appends an existing set of files/directories to the finder.
     *
     * The set can be another Finder, an {@link Iterator}, an {@link IteratorAggregate}, or even a plain array.
     *
     * @param iterable $iterator
     * @return $this
     * @throws Exception
     */
    public function append(iterable $iterator)
    {
        if ($iterator instanceof IteratorAggregate) {
            $this->iterators[] = $iterator->getIterator();
        } else if ($iterator instanceof Iterator) {
            $this->iterators[] = $iterator;
        } else if ($iterator instanceof Traversable || is_array($iterator)) {
            $it = new ArrayIterator();

            foreach ($iterator as $file) {
                $it->append($file instanceof SplFileInfo ? $file : new SplFileInfo($file));
            }

            $this->iterators[] = $it;
        } else {
            throw new InvalidArgumentException('The supplied {{$iterator}} argument is of an invalid type.');
        }

        return $this;
    }

    /**
     * Check if any results were found.
     *
     * @return bool
     */
    public function hasResults()
    {
        foreach ($this->getIterator() as $check) {
            return true;
        }

        return false;
    }

    /**
     * Returns an {@link Iterator} for the current finder configuration.
     *
     * This method implements the {@link IteratorAggregate} interface.
     *
     * @return Iterator|SplFileInfo[] An iterator
     * @throws LogicException if the {@see in()} method has not been called
     */
    public function getIterator(): \Traversable
    {
        if (0 === count($this->directories) && 0 === count($this->iterators)) {
            throw new LogicException('Either the {{self::in()}} or {{self::append()}} methods must be ' .
                'called before attempting to iterate over a {{' . __CLASS__ . '}} object.');
        }

        if (1 === count($this->directories) && 0 === count($this->iterators)) {
            return $this->searchInDirectory($this->directories[0]);
        }

        $iterator = new AppendIterator();

        foreach ($this->directories as $dir) {
            $iterator->append($this->searchInDirectory($dir));
        }

        foreach ($this->iterators as $it) {
            $iterator->append($it);
        }

        return $iterator;
    }

    private function searchInDirectory(string $directory): Iterator
    {
        $exclude = $this->exclude;
        $not_paths = $this->not_paths;

        if (static::IGNORE_VCS_FILES === (static::IGNORE_VCS_FILES & $this->ignore)) {
            $exclude = array_merge($exclude, self::$vcs_patterns);
        }

        if (static::IGNORE_DOT_FILES === (static::IGNORE_DOT_FILES & $this->ignore)) {
            $not_paths[] = '#(^|/)\..+(/|$)#';
        }

        if (static::IGNORE_VCS_IGNORED_FILES === (static::IGNORE_VCS_IGNORED_FILES & $this->ignore)) {
            $git_ignore_file_path = sprintf('%s/.gitignore', $directory);

            if (!is_readable($git_ignore_file_path)) {
                throw new IOException('The {{IGNORE_VCS_IGNORED_FILES}} flag ' .
                    'cannot be used, as the file is not readable.');
            }

            $not_paths = array_merge($not_paths, [GitIgnoreFilter::toRegex(file_get_contents($git_ignore_file_path))]);
        }

        $minimum_depth = 0;
        $maximum_depth = PHP_INT_MAX;

        foreach ($this->depths as $comparator) {
            switch ($comparator->getOperator()) {
                case '>':
                    $minimum_depth = $comparator->getTarget() + 1;
                    break;

                case '>=':
                    $minimum_depth = $comparator->getTarget();
                    break;

                case '<':
                    $maximum_depth = $comparator->getTarget() - 1;
                    break;

                case '<=':
                    $maximum_depth = $comparator->getTarget();
                    break;

                default:
                    $minimum_depth = $maximum_depth = $comparator->getTarget();
            }
        }

        $flags = RecursiveDirectoryIterator::SKIP_DOTS;

        if ($this->follow_links) {
            $flags |= RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        }

        $iterator = new RelativeDirectoryIterator($directory, $flags, $this->ignore_unreadable_directories);

        if ($exclude) {
            $iterator = new ExcludeDirectoryFilter($iterator, $exclude);
        }

        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

        if ($minimum_depth > 0 || $maximum_depth < PHP_INT_MAX) {
            $iterator = new DepthRangeFilter($iterator, $minimum_depth, $maximum_depth);
        }

        if ($this->mode) {
            $iterator = new FileTypeFilter($iterator, $this->mode);
        }

        if ($this->names || $this->not_names) {
            $iterator = new FileNameFilter($iterator, $this->names, $this->not_names);
        }

        if ($this->contains || $this->not_contains) {
            $iterator = new FileContentFilter($iterator, $this->contains, $this->not_contains);
        }

        if ($this->sizes) {
            $iterator = new SizeRangeFilter($iterator, $this->sizes);
        }

        if ($this->dates) {
            $iterator = new DateRangeFilter($iterator, $this->dates);
        }

        if ($this->filters) {
            $iterator = new CustomFilter($iterator, $this->filters);
        }

        if ($this->paths || $not_paths) {
            $iterator = new PathFilter($iterator, $this->paths, $not_paths);
        }

        if ($this->sort || $this->reverse_sorting) {
            $iterator_aggregate = new SortableIterator($iterator, $this->sort, $this->reverse_sorting);
            $iterator = $iterator_aggregate->getIterator();
        }

        return $iterator;
    }

    /**
     * Counts all the results collected by the iterators.
     *
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

}