<?php

namespace NameParser;


use NameParser\FullNameParser;

class Runner
{
    protected $input;
    protected $output;
    protected $special_output;
    protected $files;
    protected $full_name_parser;

    public function __construct()
    {
        $this->input = dirname(__FILE__) . '/files/input';
        $this->output = dirname(__FILE__) . '/files/output';
        $this->special_output = dirname(__FILE__) . '/files/special';

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
        $special_output = [];

        foreach ($parts as $key => $full_name) {
            $format_name = $this->full_name_parser->parse_name($full_name);

            if (!count($output)) {
                $output[] = implode(', ', array_keys($format_name));
            }

            $middle_parts = explode(' ', $format_name['mname']);
            if (count($middle_parts) > 1) {
                $special_output[] = implode(', ', array_values($format_name));
            }

            $output[] = implode(', ', array_values($format_name));
        }

        // $this->write_csv($this->special_output, $outputname, $special_output);
        $this->write_csv($this->output, $outputname, $output);
    }

    public function write_csv($folder, $filename, $content)
    {
        $content_text = implode(PHP_EOL, $content);
        file_put_contents("{$folder}/{$filename}", $content_text);
    }
}