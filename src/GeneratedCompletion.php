<?php


namespace Laradic\Idea;


class GeneratedCompletion
{
    /** @var \Laradic\Generators\DocBlock\Result[] */
    protected $results;

    /**
     * GeneratedCompletion constructor.
     *
     * @param \Laradic\Generators\DocBlock\Result[] $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return \Illuminate\Support\Collection|\Laradic\Generators\DocBlock\Result[]
     */
    public function getResults()
    {
        return collect($this->results);
    }

    public function writeToSourceFiles()
    {
        foreach($this->results as $result){
            $class = $result->getClass();
            file_put_contents($class->getFileName(), $result->content());
        }
    }


    public function combineForCompletionFile()
    {
        $lines = ['<?php'];
        foreach ($this->results as $result) {
            $class   = $result->getClass();
            $lines[] = "namespace {$class->getNamespaceName()} {";
            $lines[] = $result->getDoc();
            $lines[] = "class {$class->getShortName()}{}";
            $lines[] = '}';
        }

        return implode(PHP_EOL, $lines);

    }

    public function writeToCompletionFile($path)
    {
        if (path_is_relative($path)) {
            $path = base_path($path);
        }
        file_put_contents($path, $this->combineForCompletionFile());
        return $path;
    }

}