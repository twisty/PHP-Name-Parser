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
     * @dataProvider functionalNameProvider
     */
    public function testName($name, $expected_result)
    {
        $parser = new FullNameParser();
        $split_name = $parser->parse_name($name);
        $this->assertEquals($split_name, $expected_result, "Failed asserting that " . json_encode($split_name, JSON_PRETTY_PRINT) . PHP_EOL . "Is same as " . json_encode($expected_result, JSON_PRETTY_PRINT));
    }


    public function functionalNameProvider()
    {
        return array(
            array(
                "Roberta R.W. Kameda",
                array(
                    "full_name" => "Roberta R.W. Kameda",
                    "prefix"    => "",
                    "fname"     => "Roberta",
                    "mname"     => "R.W",
                    "lname"     => "Kameda",
                    "suffix"    => ""
                )
            ),
            array(
                "Mr Anthony R Von Fange III",
                array(
                    "full_name"  => "Mr Anthony R Von Fange III",
                    "prefix"     => "Mr.",
                    "fname"      => "Anthony",
                    "mname"      => "R",
                    "lname"      => "Von Fange",
                    "suffix"     => "III",
                )
            ),
            array(
                "J. B. Hunt",
                array(
                    "full_name" => "J. B. Hunt",
                    "prefix"    => "",
                    "fname"     => "J.",
                    "mname"     => "B.",
                    "lname"     => "Hunt",
                    "suffix"    => ""
                )
            ),
            array(
                "J.B. Hunt",
                array(
                    "full_name"  => "J.B. Hunt",
                    "prefix"     => "",
                    "fname"      => "J.B.",
                    "mname"      => "",
                    "lname"      => "Hunt",
                    "suffix"     => ""
                )
            ),
            array(
                "Edward Senior III",
                array(
                    "full_name" => "Edward Senior III",
                    "prefix"    => "",
                    "fname"     => "Edward",
                    "mname"     => "",
                    "lname"     => "Senior",
                    "suffix"    => "III"
                )
            ),
            array(
                "Edward Dale Senior II",
                array(
                    "full_name" => "Edward Dale Senior II",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Dale Edward Jones Senior",
                array(
                    "full_name" => "Dale Edward Jones Senior",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Edward Senior II",
                array(
                    "full_name" => "Edward Senior II",
                    "prefix"    => "",
                    "fname"     => "Edward",
                    "mname"     => "",
                    "lname"     => "Senior",
                    "suffix"    => "II"
                )
            ),
            array(
                "Dale E. Senior II, PhD",
                array(
                    "full_name" => "Dale E. Senior II, PhD",
                    "prefix"    => "",
                    "fname"     => "Dale",
                    "mname"     => "E.",
                    "lname"     => "Senior",
                    "suffix"    => "II, PhD"
                )
            ),
            array(
                "Jason Rodriguez Sr.",
                array(
                    "full_name" => "Jason Rodriguez Sr.",
                    "prefix"    => "",
                    "fname"     => "Jason",
                    "mname"     => "",
                    "lname"     => "Rodriguez",
                    "suffix"    => "Sr"
                )
            ),
            array(
                "Jason Senior",
                array(
                    "full_name" => "Jason Senior",
                    "prefix"    => "",
                    "fname"     => "Jason",
                    "mname"     => "",
                    "lname"     => "Senior",
                    "suffix"    => ""
                )
            ),
            array(
                "Abby U. Van Grinsven",
                array(
                    "full_name" => "Abby U. Van Grinsven",
                    "prefix"    => "",
                    "fname"     => "Abby",
                    "mname"     => "U.",
                    "lname"     => "Van Grinsven",
                    "suffix"    => ""
                )
            ),
            array(
                "Sara Ann Fraser",
                array(
                    "full_name" => "Sara Ann Fraser",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Adam",
                array(
                    "full_name" => "Adam",
                    "prefix"    => "",
                    "fname"     => "Adam",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "OLD MACDONALD",
                array(
                    "full_name" => "OLD MACDONALD",
                    "prefix"    => "",
                    "fname"     => "Old",
                    "mname"     => "",
                    "lname"     => "Macdonald",
                    "suffix"    => ""
                )
            ),
            array(
                "Old MacDonald",
                array(
                    "full_name" => "Old MacDonald",
                    "prefix"    => "",
                    "fname"     => "Old",
                    "mname"     => "",
                    "lname"     => "MacDonald",
                    "suffix"    => ""
                )
            ),
            array(
                "Old McDonald",
                array(
                    "full_name" => "Old McDonald",
                    "prefix"    => "",
                    "fname"     => "Old",
                    "mname"     => "",
                    "lname"     => "McDonald",
                    "suffix"    => ""
                )
            ),
            array(
                "Old Mc Donald",
                array(
                    "full_name" => "Old Mc Donald",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Old Mac Donald",
                array(
                    "full_name" => "Old Mac Donald",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "James van Allen",
                array(
                    "full_name" => "James van Allen",
                    "prefix"    => "",
                    "fname"     => "James",
                    "mname"     => "",
                    "lname"     => "Van Allen",
                    "suffix"    => ""
                )
            ),
            array(
                "Jimmy (Bubba) Smith",
                array(
                    "full_name" => "Jimmy (Bubba) Smith",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Miss Jennifer Shrader Lawrence",
                array(
                    "full_name" => "Miss Jennifer Shrader Lawrence",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Jonathan Smith, MD",
                array(
                    "full_name" => "Jonathan Smith, MD",
                    "prefix"    => "",
                    "fname"     => "Jonathan",
                    "mname"     => "",
                    "lname"     => "Smith",
                    "suffix"    => "MD"
                )
            ),
            array(
                "Dr. Jonathan Smith",
                array(
                    "full_name" => "Dr. Jonathan Smith",
                    "prefix"    => "Dr.",
                    "fname"     => "Jonathan",
                    "mname"     => "",
                    "lname"     => "Smith",
                    "suffix"    => ""
                )
            ),
            array(
                "ABIGAIL G. FREEDMAN",
                array(
                    "full_name" => "ABIGAIL G. FREEDMAN",
                    "prefix"    => "",
                    "fname"     => "Abigail",
                    "mname"     => "G.",
                    "lname"     => "Freedman",
                    "suffix"    => ""
                )
            ),
            array(
                "Miss Jamie P. Harrowitz",
                array(
                    "full_name" => "Miss Jamie P. Harrowitz",
                    "prefix"    => "Ms.",
                    "fname"     => "Jamie",
                    "mname"     => "P.",
                    "lname"     => "Harrowitz",
                    "suffix"    => ""
                )
            ),
            array(
                "Mr John Doe",
                array(
                    "full_name" => "Mr John Doe",
                    "prefix"    => "Mr.",
                    "fname"     => "John",
                    "mname"     => "",
                    "lname"     => "Doe",
                    "suffix"    => ""
                )
            ),
            array(
                "Rev. Dr John Doe",
                array(
                    "full_name" => "Rev. Dr John Doe",
                    "prefix"    => "Rev. Dr.",
                    "fname"     => "John",
                    "mname"     => "",
                    "lname"     => "Doe",
                    "suffix"    => ""
                )
            ),
            array(
                "Anthony Von Fange III",
                array(
                    "full_name" => "Anthony Von Fange III",
                    "prefix"    => "",
                    "fname"     => "Anthony",
                    "mname"     => "",
                    "lname"     => "Von Fange",
                    "suffix"    => "III"
                )
            ),
            array(
                "Anthony Von Fange III, PhD",
                array(
                    "full_name" => "Anthony Von Fange III, PhD",
                    "prefix"    => "",
                    "fname"     => "Anthony",
                    "mname"     => "",
                    "lname"     => "Von Fange",
                    "suffix"    => "III, PhD"
                )
            ),
            array(
                "Smarty Pants Phd",
                array(
                    "full_name" => "Smarty Pants Phd",
                    "prefix"    => "",
                    "fname"     => "Smarty",
                    "mname"     => "",
                    "lname"     => "Pants",
                    "suffix"    => "Phd"
                )
            ),
            array(
                "Mark Peter Williams",
                array(
                    "full_name" => "Mark Peter Williams",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Mark P Williams",
                array(
                    "full_name" => "Mark P Williams",
                    "prefix"    => "",
                    "fname"     => "Mark",
                    "mname"     => "P",
                    "lname"     => "Williams",
                    "suffix"    => ""
                )
            ),
            array(
                "Mark P. Williams",
                array(
                    "full_name" => "Mark P. Williams",
                    "prefix"    => "",
                    "fname"     => "Mark",
                    "mname"     => "P.",
                    "lname"     => "Williams",
                    "suffix"    => ""
                )
            ),
            array(
                "M Peter Williams",
                array(
                    "full_name" => "M Peter Williams",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "M. Peter Williams",
                array(
                    "full_name" => "M. Peter Williams",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "M. P. Williams",
                array(
                    "full_name" => "M. P. Williams",
                    "prefix"    => "",
                    "fname"     => "M.",
                    "mname"     => "P.",
                    "lname"     => "Williams",
                    "suffix"    => ""
                )
            ),
            array(
                "The Rev. Mark Williams",
                array(
                    "full_name" => "The Rev. Mark Williams",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Mister Mark Williams",
                array(
                    "full_name" => "Mister Mark Williams",
                    "prefix"    => "Mr.",
                    "fname"     => "Mark",
                    "mname"     => "",
                    "lname"     => "Williams",
                    "suffix"    => ""
                )
            ),
            array(
                "Rev Al Sharpton",
                array(
                    "full_name" => "Rev Al Sharpton",
                    "prefix"    => "Rev.",
                    "fname"     => "Al",
                    "mname"     => "",
                    "lname"     => "Sharpton",
                    "suffix"    => ""
                )
            ),
            array(
                "Dr Ty P. Bennington iIi",
                array(
                    "full_name" => "Dr Ty P. Bennington iIi",
                    "prefix"    => "Dr.",
                    "fname"     => "Ty",
                    "mname"     => "P.",
                    "lname"     => "Bennington",
                    "suffix"    => "III"
                )
            ),
            array(
                "Prof. Ron Brown MD",
                array(
                    "full_name" => "Prof. Ron Brown MD",
                    "prefix"    => "Prof.",
                    "fname"     => "Ron",
                    "mname"     => "",
                    "lname"     => "Brown",
                    "suffix"    => "MD"
                )
            ),
            array(
                "Not So Smarty Pants, Silly",
                array(
                    "full_name" => "Not So Smarty Pants, Silly",
                    "prefix"    => "",
                    "fname"     => "",
                    "mname"     => "",
                    "lname"     => "",
                    "suffix"    => ""
                )
            ),
            array(
                "Louis-Alphonse Quig",
                array(
                    "full_name" => "Louis-Alphonse Quig",
                    "prefix"    => "",
                    "fname"     => "Louis-Alphonse",
                    "mname"     => "",
                    "lname"     => "Quig",
                    "suffix"    => ""
                )
            ),
        );
    }
}
