<?php

namespace NameParser;

use Exception;

/**
 * Split a full name into its constituent parts
 *   - prefix/salutation (Mr. Mrs. Dr. etc)
 *   - given/first name
 *   - middle name/initial(s)
 *   - surname (last name)
 *   - suffix (II, PhD, Jr. etc)
 *
 * Author: Josh Fraser
 *
 * Contribution from Clive Verrall www.cliveverrall.com February 2016
 * 
 * // other contributions: 
 * //   - eric willis [list of honorifics](http://notes.ericwillis.com/2009/11/common-name-prefixes-titles-and-honorifics/)
 * //   - `TomThak` for raising issue #16 and providing [wikepedia resource](https://cs.wikipedia.org/wiki/Akademick%C3%BD_titul)
 * //   - `atla5` for closing the issue.
 */
class FullNameParser
{
    /**
     * Create the dictionary of terms for use later
     *
     *  - Common honorific prefixes (english)
     *  - Common compound surname identifiers
     *  - Common suffixes (lineage and professional)
     * Note: longer professional titles should appear earlier in the array than shorter titles to reduce the risk of mis-identification e.g. BEng before BE
     * Also note that case and periods are part of the matching for professional titles and therefore need to be correct, there are no case conversions
     */
    protected static $dictionary = [
        'prefix' => [
            ['mr', 'mister', 'master'],
            ['mrs', 'missus', 'missis'],
            ['ms', 'miss'],
            ['dr', 'doctor'],
            ["rev", "rev'd", "reverend"],
            ['fr', 'father'],
            ['sr', 'sister'],
            ['prof', 'professor'],
            ['sir'],
            ['hon', 'honorable'],
            ['pres', 'president'],
            ['gov', 'governor', 'governer'],
            ['ofc', 'officer'],
            ['msgr', 'monsignor'],
            ['br, brother'],
            ['supt', 'superintendent'],
            ['rep', 'representatitve'],
            ['sen', 'senator'],
            ['amb', 'ambassador'],
            ['sec', 'secretary'],
            ['pvt', 'private'],
            ['cpl', 'corporal'],
            ['sgt', 'sargent'],
            ['adm', 'administrative', 'administrator', 'administrater'],
            ['maj', 'major'],
            ['capt', 'captain'],
            ['cmdr', 'commander'],
            ['lt', 'lieutenant'],
            ['col', 'colonel'],
            ['gen', 'general'],
            ['bc', 'bachelor', 'baccalaureus'],
            ['bca', 'bachelor of arts', 'baccalaureus artis'],
            ['icdr', 'doctor of canon law', 'juris cononici doctor'],
            ['ing', 'engineer', 'ingenieur'],
            ['judr', 'juris doctor utriusque', 'juris utriusque doctor', 'doctor rights', 'doctor of law'],
            ['mddr', 'doctor of dental medicine', 'medicinae doctor dentium'],
            ['ma', 'master of arts', 'magister artis'],
            ['mgr', 'master'],
            ['md', 'doctor of general medicine', 'doctor of medicine'],
            ['dvm', 'doctor of veterinary medicine'],
            ['phdr', 'doctor of philosophy'],
            ['phmr', 'master of pharmacy'],
            ['rcdr', 'doctor of economy'],
            ['rndr', 'doctor of natural sciences'],
            ['dsc', 'doctor of science'],
            ['rsdr', 'doctor of socio-political sciences', 'doctor of social sciences'],
            ['thdr', 'doctor of theology'],
            ['thd', 'dth', 'doctor of theology'],
            ['acad', 'academian', 'academic'],
            ['da', 'artd', 'darts', 'doctor of arts'],
            ['dis', 'certified specialist'],
            ['as', 'assistant'],
            ['assoc', 'associate'],
            ['treas', 'treasurer'],
            ['lth', 'licentiate of theology'],
            ['thm', 'master of theology'],
            ['mdiv', 'master of divinity'],
            ['edd', 'ded', 'doctor of education'],
            ['pharmdr', 'doctor of pharmacy'],
            ['arch', 'architect', 'intrudes upon architectus'],
            ['the']
        ],
        'compound' => [
            'da', 'de', 'del', 'della', 'dem', 'den', 'der', 'di', 'du', 'het', 'la', 'los', 'onder', 'op', 'pietro', 'st.', 'st', '\'t', 'ten', 'ter', 'van', 'vanden', 'vere', 'von'
        ],
        'suffixes' => [
            'line' => [
                'I', 'II', 'III', 'IV', 'V', '1st', '2nd', '3rd', '4th', '5th', 'Senior', 'Junior', 'Jr', 'Sr'
            ],
            'prof' => [
                'AO', 'B.A.', 'M.Sc', 'BCompt', 'PhD', 'Ph.D.', 'APR', 'RPh', 'PE', 'MD', 'M.D.', 'MA', 'DMD', 'CME', 'BSc', 'BSc(hons)', 'BEng', 'M.B.A.', 'MBA',
                'FAICD', 'CM', 'OBC', 'M.B.', 'ChB', 'FRCP', 'FRSC', 'FREng', 'Esq', 'MEng', 'MSc', 'J.D.', 'JD', 'BGDipBus', 'Dip', 'Dipl.Phys', 'M.H.Sc.', 'MPA', 'B.Comm', 'B.Eng',
                'B.Acc', 'FSA', 'PGDM', 'FCPA', 'RN', 'R.N.', 'MSN', 'PCA', 'PCCRM', 'PCFP', 'PCGD', 'PCHR', 'PCM', 'PCPS', 'PCPM', 'PCSCM', 'PCSM', 'PCMM', 'PCTC', 'ACA', 'FCA',
                'ACMA', 'FCMA', 'AAIA', 'FAIA', 'CCC', 'MIPA', 'FIPA', 'CIA', 'CFE', 'CISA', 'CFAP', 'QC', 'Q.C.', 'M.Tech', 'CTA', 'C.I.M.A.', 'B.Ec', 'CFIA', 'ICCP', 'CPS', 'CAP-OM',
                'CAPTA', 'TNAOAP', 'AFA', 'AVA', 'ASA', 'CAIA', 'CBA', 'CVA', 'ICVS', 'CIIA', 'CMU', 'PFM', 'PRM', 'CFP', 'CWM', 'CCP', 'EA', 'CCMT', 'CGAP', 'CDFM', 'CFO', 'CGFM',
                'CGAT', 'CGFO', 'CMFO', 'CPFO', 'CPFA', 'BMD', 'BIET', 'P.Eng', 'MBBS', 'MB', 'BCh', 'BAO', 'BMBS', 'MBBChir', 'MBChBa', 'MPhil', 'LL.D', 'LLD', 'D.Lit', 'DEA',
                'DESS', 'DClinPsy', 'DSc', 'MRes', 'M.Res', 'Psy.D', 'Pharm.D', 'BA(Admin)', 'BAcc', 'BACom', 'BAdmin', 'BAE', 'BAEcon', 'BA(Ed)', 'BA(FS)', 'BAgr', 'BAH', 'BAI', 'BAI(Elect)',
                'BAI(Mech)', 'BALaw', 'BAppSc', 'BArch', 'BArchSc', 'BARelSt', 'BASc', 'BASoc', 'DDS', 'D.D.S.', 'BATheol', 'BBA', 'BBLS', 'BBS', 'BBus', 'BChem', 'BCJ', 'BCL',
                'BCLD(SocSc)', 'BClinSci', 'BCom', 'BCombSt', 'BCommEdCommDev', 'BComp', 'BComSc', 'BCoun', 'BD', 'BDes', 'BE', 'BEcon', 'BEcon&Fin', 'M.P.P.M.', 'MPPM', 'BEconSci', 'BEd',
                'BES', 'BEng(Tech)', 'BFA', 'BFin', 'BFLS', 'BFST', 'BH', 'BHealthSc', 'BHSc', 'BHy', 'BJur', 'BL', 'BLE', 'BLegSc', 'BLib', 'BLing', 'BLitt', 'BLittCelt', 'BLS',
                'BMedSc', 'BMet', 'BMid', 'BMin', 'BMS', 'BMSc', 'BMus', 'BMusEd', 'BMusPerf', 'BN', 'BNS', 'BNurs', 'BOptom', 'BPA', 'BPharm', 'BPhil', 'TTC', 'Tchg',
                'MEd', 'ACIB', 'FCIM', 'FCIS', 'FCS', 'Bachelor', 'O.C.', 'JP', 'C.Eng', 'C.P.A.', 'B.B.S.', 'MBE', 'GBE', 'KBE', 'DBE', 'CBE', 'OBE', 'MRICS',  'D.P.S.K.',
                'D.P.P.J.', 'DPSK', 'DPPJ', 'B.B.A.', 'GBS', 'MIGEM', 'M.I.G.E.M.', 'BPhil(Ed)', 'BPhys', 'BPhysio', 'BPl', 'BRadiog', 'B.Sc', 'BScAgr', 'BSc(Dairy)',
                'BSc(DomSc)', 'BScEc', 'BScEcon', 'BSc(Econ)', 'BSc(Eng)', 'BScFor', 'BSc(HealthSc)', 'BSc(Hort)', 'BSc(MCRM)', 'BSc(Med)', 'BSc(Mid)', 'BSc(Min)',
                'BSc(Psych)', 'BSc(Tech)', 'BSD', 'BSocSc', 'BSS', 'BStSu', 'BTchg', 'BTCP', 'BTech', 'BTechEd', 'BTh', 'BTheol', 'BTS', 'EdB', 'LittB', 'LLB', 'MusB', 'ScBTech',
                'CEng', 'CFA', 'C.F.A.', 'LL.B', 'LLM', 'LL.M', 'CA(SA)', 'C.A.', 'CA', 'CPA',  'Solicitor',  'DMS', 'FIWO', 'CEnv', 'MICE', 'MIWEM', 'B.Com',
                'BA', 'BEc', 'MEc', 'HDip', 'B.Bus.', 'E.S.C.P.'
            ]
        ],
        'vowels' => [
            'a', 'e', 'i', 'o', 'u'
        ]
    ];

    protected $notNicknames = ["(hons)"];
    protected $temp = [];

    /**
     * Parse Static entry point.
     *
     * @param  string $name the full name you wish to parse
     * @return array  returns associative array of name parts
     */
    public static function parse($name)
    {
        return (new self())->parseName($name);
    }

    /**
     * This is the primary method which calls all other methods
     *
     * @param  string $name the full name you wish to parse
     * @return array  returns associative array of name parts
     */
    protected function parseName($fullName)
    {
        try {
            $fullName     = trim($fullName);
            $unparsedName = $fullName;

            $prefix       = '';
            $firstName    = '';
            $middleName   = '';
            $lastName     = '';
            $suffix       = '';

            $formatName   = $this->formatName($fullName, $prefix, $firstName, $middleName, $lastName, $suffix);

            # If nickname then it's hard to parse other stuff
            if ($this->getNickname($fullName)) {
                return $formatName;
            }
            # Parse professional prefix or prefixes
            list($fullName, $suffix) = $this->getProfessionalSuffix($fullName);

            # Grab a list of words from the remainder of the full name
            $unfilteredNameParts = $this->breakWords($fullName);

            list($prefix, $suffix, $unfilteredNameParts) = $this->getSuffixAndPrefix($suffix, $prefix, $unfilteredNameParts, $fullName);

            $unfilteredNameParts = $this->repackNameParts($unfilteredNameParts);

            list($unfilteredNameParts, $firstName, $middleName, $index) = $this->getFirstAndMiddle($unfilteredNameParts, $firstName, $middleName);

            list($unfilteredNameParts, $lastName) = $this->getLastOrFirst($unfilteredNameParts, $lastName, $index);

            # return the various parts in an array
            return $this->formatName($unparsedName, $prefix, trim($firstName), trim($middleName), trim($lastName), $suffix);
        } catch (Exception $e) {
            return $this->default($formatName, $fullName, $prefix, $suffix);
        }
    }

    protected function formatName($fullName, $prefix, $fname, $mname, $lname, $suffix)
    {
        return [
            'full_name' => $fullName,
            'prefix' => $prefix,
            'fname' => $fname,
            'mname' => $mname,
            'lname' => $lname,
            'suffix' => $suffix,
        ];
    }

    /**
     * Parse suffix and prefixes
     *
     * @param  string $suffix
     * @param  string $prefix
     * @param  array  $unfilteredNameParts
     * @param  string $fullName
     * @return array
     */
    protected function getSuffixAndPrefix($suffix, $prefix, $unfilteredNameParts, $fullName)
    {
        if (!count($unfilteredNameParts)) {
            return [
                '',
                '',
                $unfilteredNameParts,
            ];
        }
        # Is first word a title or multiple titles consecutively?
        # only start looking if there are any words left in the name to process
        while (count($unfilteredNameParts) && $this->isPrefix($unfilteredNameParts[0])) {
            $prefix .= "{$this->temp['prefix']} ";
            unset($this->temp['prefix']);
            array_shift($unfilteredNameParts);
        }
        $prefix = trim($prefix);
        # Find if there is a line suffix, if so then move it out
        # Is last word a suffix or multiple suffixes consecutively?
        while (
            count($unfilteredNameParts)
            && $this->isLineSuffix($unfilteredNameParts[count($unfilteredNameParts) - 1], $fullName)
        ) {
            $suffix = $suffix != '' ? "{$this->temp['suffix']}, {$suffix}" : $suffix . $this->temp['suffix'];
            unset($this->temp['suffix']);
            array_pop($unfilteredNameParts);
        }
        $suffix = trim($suffix);

        return [
            $prefix,
            $suffix,
            $unfilteredNameParts,
        ];
    }

    /**
     * Parse First and maybe Middle names
     *
     * @param  array  $unfilteredNameParts
     * @param  string $firstName
     * @param  string $middleName
     * @return array
     */
    protected function getFirstAndMiddle($unfilteredNameParts, $firstName, $middleName)
    {
        # set the ending range after prefix/suffix trim
        $end = count($unfilteredNameParts);
        # concat the first name
        for ($index = 0; $index < $end - 1; $index++) {
            $word = $unfilteredNameParts[$index];
            # move on to parsing the last name if we find an indicator of a compound last name (Von, Van, etc)
            # we use $index != 0 to allow for rare cases where an indicator is actually the first name (like "Von Fabella")
            if ($this->isCompound($word) && $index != 0) {
                break;
            }
            # is it a middle initial or part of their first name?
            # if we start off with an initial, we'll call it the first name
            if ($this->isInitial($word)) {
                # is the initial the first word?
                if ($index == 0) {
                    # if so, do a look-ahead to see if they go by their middle name
                    # for ex: "R. Jason Smith" => "Jason Smith" & "R." is stored as an initial
                    # but "R. J. Smith" => "R. Smith" and "J." is stored as an middle
                    $firstName .= " " . mb_strtoupper($word);
                    if ($this->isInitial($unfilteredNameParts[$index + 1]) && !$firstName) {
                        $firstName .= " " . mb_strtoupper($word);
                    }
                }
                # otherwise, just go ahead and save the initial
                else {
                    $middleName .= " " . mb_strtoupper($word);
                }
            } elseif (!$firstName) {
                $firstName .= " " . $this->fixCase($word);
            }
            # If not an initial and a first name was set, then this should be the middle name.
            elseif (!$middleName) {
                $middleName .= " " . $this->fixCase($word);
            } else {
                # Hard to parse
                throw new Exception('Hard to parse');
            }
        }

        return [
            $unfilteredNameParts,
            $firstName,
            $middleName,
            $index,
        ];
    }

    /**
     * Parse Last or maybe first name if the string is one word
     *
     * @param  array  $unfilteredNameParts
     * @param  string $lastName
     * @param  int    $index               Position of the unfilteredNameParts where the getFirstAndMiddle function ends
     * @return array
     */
    protected function getLastOrFirst($unfilteredNameParts, $lastName, $index)
    {
        # Resets the index number
        $unfilteredNameParts = array_values($unfilteredNameParts);
        $end = count($unfilteredNameParts);
        $lastNameSet = false;

        if (count($unfilteredNameParts)) {
            # check that we have more than 1 word in our string
            if ($end > 1) {
                # concat the last name and split last name in base and compound
                for ($index; $index < $end; $index++) {
                    $word = $unfilteredNameParts[$index];
                    # Check if we have a compound, we can have many of them as long as a normal word appears
                    if ($this->isCompound($word)) {
                        $lastName .= " " . $this->fixCase($word);
                    } elseif (!$lastNameSet) {
                        $lastName .= " " . $this->fixCase($word);
                        $lastNameSet = true;
                    } else {
                        # hard to parse
                        throw new Exception('Hard to parse');
                    }
                }
            } else {
                # otherwise, single word strings are assumed to be first names
                $firstName = $this->fixCase($unfilteredNameParts[0]);
            }
        } else {
            $firstName = "";
        }

        return [
            $unfilteredNameParts,
            $lastName,
        ];
    }


    /**
     * Re-pack the unfiltered name parts array and exclude empty words
     *
     * @param  array $unfilteredNameParts
     * @return array
     */
    protected function repackNameParts($unfilteredNameParts)
    {
        $nameParts = [];
        foreach ($unfilteredNameParts as $namePart) {
            $namePart = rtrim(trim($namePart), ',');
            if (mb_strlen($namePart) == '1' && !$this->mbCtypeAlpha($namePart)) {
                # If any word left is of one character that is not alphabetic then it is not a real word, so remove it
                $namePart = "";
            }
            if (mb_strlen(trim($namePart))) {
                $nameParts[] = $namePart;
            }
        }

        return $nameParts;
    }

    /**
     * Checks for the existence of, and returns professional suffix
     *
     * @param  string $name the name you wish to test
     * @return true   returns true if the suffix exists, false otherwise
     */
    protected function isProfessionalSuffix($name)
    {
        $foundSuffixArr = [];
        foreach (self::getProfSuffixes() as $suffix) {
            if (
                preg_match('/[,\s]' . preg_quote($suffix) . '([^\wñ]\b|\s|$)+/', $name, $matches)
                || mb_strtolower($suffix) === mb_strtolower($name)
            ) {
                $foundSuffix      = isset($matches[0]) ? trim($matches[0]) : $name;
                $foundSuffix      = trim($foundSuffix, ',');
                $foundSuffixArr[] = trim($foundSuffix);
            }
        }

        return $foundSuffixArr;
    }


    protected function getProfessionalSuffix($fullName)
    {
        $suffix = '';
        # Find all the professional suffixes possible
        $professionalSuffix = $this->isProfessionalSuffix($fullName);

        # The position of the first professional suffix denotes the end of the name and the start of suffixes
        $firstSuffixIndex = mb_strlen($fullName);
        foreach ($professionalSuffix as $proSuffix) {
            $start = mb_strpos($fullName, $proSuffix);
            if ($start === false) {
                continue;
            }
            if ($start < $firstSuffixIndex) {
                $firstSuffixIndex = $start;
            }
        }

        if (count($professionalSuffix)) {
            $realSuffix = $this->checkNextWords($fullName, $firstSuffixIndex);

            if ($realSuffix) {
                # everything to the right of the first professional suffix is part of the suffix
                $suffix = mb_substr($fullName, $firstSuffixIndex);

                # remove the suffixes from the fullName
                $fullName = mb_substr($fullName, 0, $firstSuffixIndex);
            } else {
                throw new Exception('Hard to parse');
            }
        }

        return [
            $fullName,
            $suffix,
        ];
    }

    /**
     * Check if next words after a professional suffix are professional suffixes too
     *
     * @param  string $fullName
     * @param  int    $professionalSuffixIndex Where in the $fullName the first professional suffix was found
     * @return bool
     */
    protected function checkNextWords($fullName, $professionalSuffixIndex)
    {
        $results = [];
        $supossedSuffix = mb_substr($fullName, $professionalSuffixIndex);

        $parts = $this->breakWords($supossedSuffix);

        if (count($parts) === 1) {
            return true;
        }

        foreach ($parts as $key => $word) {
            if (count($this->isProfessionalSuffix($word))) {
                continue;
            }
            return false;
        }

        return true;
    }

    /**
     * Breaks name into individual words
     *
     * @param  string $name the full name you wish to parse
     * @return array  full list of words broken down by spaces
     */
    protected function breakWords($name)
    {
        $tempWordArr  = explode(' ', $name);
        return array_values(array_filter($tempWordArr, function ($word) {
            return $word != "" && $word != "," ? true : false;
        }));
    }

    /**
     * Function to check name for existence of nickname based on these stipulations
     *  - String wrapped in parentheses (string)
     *  - String wrapped in double quotes "string"
     *  x String wrapped in single quotes 'string'
     *
     *  I removed the check for strings in single quotes 'string' due to possible
     *  conflicts with names that may include apostrophes. Arabic transliterations, for example
     *
     * @param  string $name the name you wish to test against
     * @return bool   returns true nickname if exists, false otherwise
     */
    protected function getNickname($name)
    {
        if (!preg_match("/[\(|\"].*?[\)|\"]/", $name, $matches)) {
            return false;
        }

        if (in_array(mb_strtolower($matches[0]), $this->notNicknames)) {
            return false;
        }

        return true;
    }

    /**
     * Checks word against array of common lineage suffixes
     *
     * @param  string $word the single word you wish to test
     * @param  string $name full name for context in determining edge-cases
     * @return mixed  boolean if false, string if true (returns suffix)
     */
    protected function isLineSuffix($word, $name)
    {
        # Ignore periods and righ commas, normalize case
        $word = rtrim($word, ',');
        $original = $word;
        $word = str_replace('.', '', mb_strtolower($word));

        # Search the array for our word
        $lineMatch = array_search($word, array_map('mb_strtolower', self::getLineSuffixes()));

        # Now test our edge cases based on lineage
        if ($lineMatch === false) {
            return false;
        }

        # Remove it from the array
        $tempArray = self::$dictionary['suffixes']['line'];
        unset($tempArray[$lineMatch]);

        # Make sure we're dealing with the suffix and not a surname
        if (in_array($word, ['senior', 'junior'])) {

            # If name is Joshua Senior, it's pretty likely that Senior is the surname
            # However, if the name is Joshua Jones Senior, then it's likely a suffix

            if ($this->mbStrWordCount($name) < 4) {
                return false;
            }

            # If the word Junior or Senior is contained, but so is some other
            # lineage suffix, then the word is likely a surname and not a suffix
            foreach ($tempArray as $suffix) {
                if (preg_match("/\b" . $suffix . "\b/i", $name)) {
                    return false;
                }
            }
        }

        # Store our match
        $this->temp['suffix'] = $original;
        return true;
    }

    /**
     * Checks word against list of common honorific prefixes
     *
     * @param  string  $word the single word you wish to test
     * @return boolean
     */
    protected function isPrefix($word)
    {
        $original = $word;
        $word = str_replace('.', '', mb_strtolower($word));
        foreach (self::getPrefixes() as $originals) {
            if (in_array($word, $originals)) {
                $this->temp['prefix'] = $original;
                return true;
            }
        }
        return false;
    }

    /**
     * Checks our dictionary of compound indicators to see if last name is compound
     *
     * @param  string  $word the single word you wish to test
     * @return boolean
     */
    protected function isCompound($word)
    {
        return in_array(mb_strtolower($word), self::getCompound());
    }

    /**
     * Test string to see if it's a single letter/initial (period optional)
     *
     * @param  string  $word the single word you wish to test
     * @return boolean
     */
    protected function isInitial($word)
    {
        return ((mb_strlen($word) == 1) || (mb_strlen($word) == 2 && $word[1] == "."));
    }

    /**
     * Send a default parsed name
     *
     * @param  array  $formatName array created via formatName function
     * @param  string $fullName
     * @param  string $prefix
     * @param  string $suffix
     * @return array
     */
    protected function default($formatName, $fullName, $prefix, $suffix)
    {
        $parts = $this->breakWords($fullName);
        if (count($parts) === 3) {
            return $this->formatName($fullName['full_name'], $prefix, trim($parts[0]), trim($parts[1]), trim($parts[2]), $suffix);
        }

        return $formatName;
    }

    # ucfirst words split by dashes or periods
    # ucfirst all upper/lower strings, but leave camelcase words alone
    protected function fixCase($word)
    {
        # Fix case for words split by periods (J.P.)
        if (mb_strpos($word, '.') !== false) {
            $word = $this->safeUcFirst(".", $word);
        }

        # Fix case for words split by hyphens (Kimura-Fay)
        if (mb_strpos($word, '-') !== false) {
            $word = $this->safeUcFirst("-", $word);
        }

        # Special case for single letters
        if (mb_strlen($word) == 1) {
            $word = mb_strtoupper($word);
        }

        # Special case for 2-letter words
        if (mb_strlen($word) == 2) {
            # Both letters vowels (uppercase both)
            $firstIsVowel = in_array(mb_strtolower($word[0]), self::$dictionary['vowels']);
            $secondIsVowel = in_array(mb_strtolower($word[1]), self::$dictionary['vowels']);

            if ($firstIsVowel && $secondIsVowel) {
                $word = mb_strtoupper($word);
            }
            # Both letters consonants (uppercase both)
            if (!$firstIsVowel && !$secondIsVowel) {
                $word = mb_strtoupper($word);
            }
            # First letter is vowel, second letter consonant (uppercase first)
            if ($firstIsVowel && !$secondIsVowel) {
                $word = $this->mbUcFirst(mb_strtolower($word));
            }
            # First letter consonant, second letter vowel or "y" (uppercase first)
            if (!$firstIsVowel && ($secondIsVowel || mb_strtolower($word[1]) == 'y')) {
                $word = $this->mbUcFirst(mb_strtolower($word));
            }
        }

        # Fix case for words which aren't initials, but are all uppercase or lowercase
        if ((mb_strlen($word) >= 3) && ($this->mbCtypeUpper($word) || $this->mbCtypeLower($word))) {
            $word = $this->mbUcFirst(mb_strtolower($word));
        }

        return $word;
    }

    # helper protected function for fixCase
    protected function safeUcFirst($seperator, $word)
    {
        # uppercase words split by the seperator (ex. dashes or periods)
        $parts = explode($seperator, $word);
        foreach ($parts as $word) {
            $words[] = ($this->isCamelCase($word)) ? $word : $this->mbUcFirst(mb_strtolower($word));
        }
        return implode($seperator, $words);
    }

    /**
     * Checks for camelCase words such as McDonald and MacElroy
     *
     * @param  string  $word the single word you wish to test
     * @return boolean
     */
    protected function isCamelCase($word)
    {
        return (bool) preg_match('/\p{L}(\p{Lu}*\p{Ll}\p{Ll}*\p{Lu}|\p{Ll}*\p{Lu}\p{Lu}*\p{Ll})\p{L}*/', $word);
    }

    # helper protected function for multibytes ctype_alpha
    protected function mbCtypeAlpha($text)
    {
        return (bool) preg_match('/^\p{L}*$/', $text);
    }

    # helper protected function for multibytes ctype_lower
    protected function mbCtypeLower($text)
    {
        return (bool) preg_match('/^\p{Ll}*$/', $text);
    }

    # helper protected function for multibytes ctype_upper
    protected function mbCtypeUpper($text)
    {
        return (bool) preg_match('/^\p{Lu}*$/', $text);
    }

    # helper protected function for multibytes str_word_count
    protected function mbStrWordCount($text)
    {
        if (empty($text)) {
            return 0;
        }
        return preg_match_all('/\s+/', $text) + 1;
    }

    # helper protected function for multibytes ucfirst
    protected function mbUcFirst($string)
    {
        $strlen    = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then      = mb_substr($string, 1, $strlen - 1);
        return mb_strtoupper($firstChar) . $then;
    }

    /*
     * G E T T E R S   &   S E T T E R S
     */

    public static function getPrefixes()
    {
        return self::$dictionary['prefix'];
    }


    public static function getLineSuffixes()
    {
        return self::$dictionary['suffixes']['line'];
    }


    public static function getProfSuffixes()
    {
        return self::$dictionary['suffixes']['prof'];
    }


    public static function getCompound()
    {
        return self::$dictionary['compound'];
    }


    public static function setPrefixes($prefixes)
    {
        self::$dictionary['prefix'] = $prefixes;
    }


    public static function setLineSuffixes($lineSuffixes)
    {
        self::$dictionary['suffixes']['line'] = $lineSuffixes;
    }


    public static function setProfSuffixes($profSuffixes)
    {
        self::$dictionary['suffixes']['prof'] = $profSuffixes;
    }


    public static function setCompound($compound)
    {
        self::$dictionary['compound'] = $compound;
    }
}
