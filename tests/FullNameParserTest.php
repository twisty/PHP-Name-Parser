<?php

namespace NameParser\Test;


use NameParser\Test\TestBase;
use NameParser\FullNameParser;

class FullNameParserTest extends TestBase
{
    /**
     * @dataProvider functionalOneNameProvider
     */
    public function testName($name, $expectedResult)
    {
        $splitName = FullNameParser::parse($name);
        $this->assertEquals($expectedResult, $splitName);
    }
    /**
     * @dataProvider differentDictionaryProvider
     */
    public function testNameDiffDictionary($name, $expectedResult)
    {
        $prefixes = [
            ['mr', 'mister', 'master'],
            ['mrs', 'missus', 'missis'],
            ['ms', 'miss'],
            ['dr', 'doctor'],
            ['fr', 'father'],
            ['prof', 'professor'],
            ['hon', 'honorable'],
            ['sgt', 'sargent'],
            ['capt', 'captain'],
            ['cmdr', 'commander'],
            ['lt', 'lieutenant'],
            ['col', 'colonel'],
            ['gen', 'general'],
            ['icdr', 'doctor of canon law', 'juris cononici doctor'],
            ['judr', 'juris doctor utriusque', 'juris utriusque doctor', 'doctor rights', 'doctor of law'],
        ];
        $lineSuffixes = ['I', 'II', 'III', 'IV', 'V', 'jr', 'sr'];
        FullNameParser::setPrefixes($prefixes);
        FullNameParser::setLineSuffixes($lineSuffixes);
        $splitName = FullNameParser::parse($name);
        $this->assertEquals($expectedResult, $splitName);
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
                "Ms. Allyson De Guzman Junior",
                [
                    "full_name" => "Ms. Allyson De Guzman Junior",
                    "prefix"    => "Ms.",
                    "fname"     => "Allyson",
                    "mname"     => "",
                    "lname"     => "De Guzman",
                    "suffix"    => "Junior"
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
                "Alejandro De Los Fernandez Jr.",
                [
                    "full_name" => "Alejandro De Los Fernandez Jr.",
                    "prefix"    => "",
                    "fname"     => "Alejandro",
                    "mname"     => "",
                    "lname"     => "De Los Fernandez",
                    "suffix"    => "Jr."
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
                "Lt. Col. Erich von Stroheim",
                [
                    "full_name" => "Lt. Col. Erich von Stroheim",
                    "prefix"    => "Lt. Col.",
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
                "Roberta R. W. Kameda II Jr.",
                [
                    "full_name" => "Roberta R. W. Kameda II Jr.",
                    "prefix"    => "",
                    "fname"     => "Roberta",
                    "mname"     => "R. W.",
                    "lname"     => "Kameda",
                    "suffix"    => "II, Jr."
                ]
            ],
            [
                "Marc Jeffrey Lopez Jr. Ph.D.",
                [
                    "full_name" => "Marc Jeffrey Lopez Jr. Ph.D.",
                    "prefix"    => "",
                    "fname"     => "Marc",
                    "mname"     => "Jeffrey",
                    "lname"     => "Lopez",
                    "suffix"    => "Jr., Ph.D."
                ]
            ],
            [
                "Dr. Juan Xavier Q. de la Vega III",
                [
                    "full_name" => "Dr. Juan Xavier Q. de la Vega III",
                    "prefix" => "Dr.",
                    "fname" => "Juan",
                    "mname" => "Xavier Q.",
                    "lname" => "De La Vega",
                    "suffix" => "III"
                ]
            ],
            [
                "Patricia J. Peña",
                [
                    "full_name" => "Patricia J. Peña",
                    "prefix"    => "",
                    "fname"     => "Patricia",
                    "mname"     => "J.",
                    "lname"     => "Peña",
                    "suffix"    => ""
                ]
            ],
            [
                "Michael Richard Meng",
                [
                    "full_name" => "Michael Richard Meng",
                    "prefix"    => "",
                    "fname"     => "Michael",
                    "mname"     => "Richard",
                    "lname"     => "Meng",
                    "suffix"    => ""
                ]
            ],
            [
                "Justin Michael Senior",
                [
                    "full_name" => "Justin Michael Senior",
                    "prefix"    => "",
                    "fname"     => "Justin",
                    "mname"     => "Michael",
                    "lname"     => "Senior",
                    "suffix"    => ""
                ]
            ],
            [
                "Justin Michael Lopez Senior",
                [
                    "full_name" => "Justin Michael Lopez Senior",
                    "prefix"    => "",
                    "fname"     => "Justin",
                    "mname"     => "Michael",
                    "lname"     => "Lopez",
                    "suffix"    => "Senior"
                ]
            ],
        ];
    }
    public function differentDictionaryProvider()
    {
        return [
            [
                "Rev Jordan B Peck Jr.",
                [
                    "full_name" => "Rev Jordan B Peck Jr.",
                    "prefix"    => "",
                    "fname"     => "Rev",
                    "mname"     => "Jordan B",
                    "lname"     => "Peck",
                    "suffix"    => "Jr."
                ]
            ],
            [
                "Maj Vasigh",
                [
                    "full_name" => "Maj Vasigh",
                    "prefix"    => "",
                    "fname"     => "Maj",
                    "mname"     => "",
                    "lname"     => "Vasigh",
                    "suffix"    => ""
                ]
            ],
            [
                "Major Ryan Thompson",
                [
                    "full_name" => "Major Ryan Thompson",
                    "prefix"    => "",
                    "fname"     => "Major",
                    "mname"     => "Ryan",
                    "lname"     => "Thompson",
                    "suffix"    => ""
                ]
            ],
            [
                "Da Mao",
                [
                    "full_name" => "Da Mao",
                    "prefix"    => "",
                    "fname"     => "Da",
                    "mname"     => "",
                    "lname"     => "Mao",
                    "suffix"    => ""
                ]
            ],
            [
                "Mr Major Best Harding",
                [
                    "full_name" => "Mr Major Best Harding",
                    "prefix"    => "Mr",
                    "fname"     => "Major",
                    "mname"     => "Best",
                    "lname"     => "Harding",
                    "suffix"    => ""
                ]
            ],
        ];
    }
}
