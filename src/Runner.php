<?php

namespace NameParser;


use NameParser\FullNameParser;

class Runner
{
    protected $input;
    protected $output;
    protected $files;
    protected $full_name_parser;

    public function __construct()
    {
        $this->input = dirname(__FILE__) . '/files/input';
        $this->output = dirname(__FILE__) . '/files/output';
        $this->files = array_slice(scandir($this->input), 2);
        $this->full_name_parser = new FullNameParser();
    }

    public function run()
    {
        foreach ($this->files as $key => $file) {
            $full_path = "{$this->input}/{$file}";
            if (file_exists($full_path)) {
                $outputname = $file . '.csv';
                $this->process_file($full_path, $outputname);
            }
        }
    }


    public function process_file($file, $outputname)
    {
        $file_content = file_get_contents($file);
        $parts = explode(PHP_EOL, $file_content);
        $output = [];

        foreach ($parts as $key => $full_name) {
            $format_name = $this->full_name_parser->parse_name($full_name);

            if (!count($output)) {
                $output[] = implode(', ', array_keys($format_name));
            }

            $output[] = implode(', ', array_values($format_name));
        }

        $output_text = implode(PHP_EOL, $output);
        file_put_contents("{$this->output}/{$outputname}", $output_text);
    }
}