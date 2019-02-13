<?php

namespace NameParser\Test;


use NameParser\Test\TestBase;
use NameParser\FullNameParser;

class FullNameParserTest extends TestBase
{
    public function setUp()
    {
        parent::setUp();
    }


    /**
     * @dataProvider functionalOneNameProvider
     */
    public function testName($name, $expected_result)
    {
        $parser = new FullNameParser();
        $split_name = $parser->parse_name($name);
        $this->assertEquals($expected_result, $split_name, "Failed asserting that " . json_encode($expected_result, JSON_PRETTY_PRINT) . PHP_EOL . "Is same as " . json_encode($split_name, JSON_PRETTY_PRINT));
    }


    public function functionalOneNameProvider()
    {
        return [
            [
                "Marc Jeffrey Del DelPiero",
                [
                    "full_name" => "Marc Jeffrey Del DelPiero",
                    "prefix"    => "",
                    "fname"     => "Marc",
                    "mname"     => "Jeffrey",
                    "lname"     => "Del DelPiero",
                    "suffix"    => ""
                ]
            ],
            [
                "Alyssa Ruiz De Esparza",
                [
                    "full_name" => "Alyssa Ruiz De Esparza",
                    "prefix"    => "",
                    "fname"     => "Alyssa",
                    "mname"     => "Ruiz",
                    "lname"     => "De Esparza",
                    "suffix"    => ""
                ]
            ],
            [
                "Joseph Edward A. Connell Jr",
                [
                    "full_name" => "Joseph Edward A. Connell Jr",
                    "prefix"    => "",
                    "fname"     => "Joseph",
                    "mname"     => "Edward A.",
                    "lname"     => "Connell",
                    "suffix"    => "Jr"
                ]
            ],
            [
                "Allyson De Guzman Jr.",
                [
                    "full_name" => "Allyson De Guzman Jr.",
                    "prefix"    => "",
                    "fname"     => "Allyson",
                    "mname"     => "",
                    "lname"     => "De Guzman",
                    "suffix"    => "Jr"
                ]
            ],
            [
                "Julie Del V Catancio",
                [
                    "full_name" => "Julie Del V Catancio",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Alejandro Fernandez Catan",
                [
                    "full_name" => "Alejandro Fernandez Catan",
                    "prefix"    => "",
                    "fname"     => "Alejandro",
                    "mname"     => "Fernandez",
                    "lname"     => "Catan",
                    "suffix"    => ""
                ]
            ],
            [
                "Alejandro Los Fernandez Jr.",
                [
                    "full_name" => "Alejandro Los Fernandez Jr.",
                    "prefix"    => "",
                    "fname"     => "Alejandro",
                    "mname"     => "",
                    "lname"     => "Los Fernandez",
                    "suffix"    => "Jr"
                ]
            ],
            [
                "Gerardo De Los Ang Camacho III",
                [
                    "full_name" => "Gerardo De Los Ang Camacho III",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Gerardo De Los Angeles III",
                [
                    "full_name" => "Gerardo De Los Angeles III",
                    "prefix"    => "",
                    "fname"     => "Gerardo",
                    "mname"     => "",
                    "lname"     => "De Los Angeles",
                    "suffix"    => "III"
                ]
            ],
            [
                "Erich von Stroheim",
                [
                    "full_name" => "Erich von Stroheim",
                    "prefix"    => "",
                    "fname"     => "Erich",
                    "mname"     => "",
                    "lname"     => "Von Stroheim",
                    "suffix"    => ""
                ]
            ],
            [
                "Manuel De Jesus Van Halen Jr",
                [
                    "full_name" => "Manuel De Jesus Van Halen Jr",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Manuel De Jesus Balam Jr",
                [
                    "full_name" => "Manuel De Jesus Balam Jr",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Carol Frances Ma Molloy",
                [
                    "full_name" => "Carol Frances Ma Molloy",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Coral Del Mar Lopez Rosario",
                [
                    "full_name" => "Coral Del Mar Lopez Rosario",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                ]
            ],
            [
                "Roberta R. W. Kameda",
                [
                    "full_name" => "Roberta R. W. Kameda",
                    "prefix"    => "",
                    "fname"     => "Roberta",
                    "mname"     => "R. W.",
                    "lname"     => "Kameda",
                    "suffix"    => ""
                ]
            ],
        ];
    }
}
