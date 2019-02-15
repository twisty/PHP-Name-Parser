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
    protected $dict = [
        'prefix' => [
            'Mr.'   => ['mr', 'mister', 'master'],
            'Mrs.'  => ['mrs', 'missus', 'missis'],
            'Ms.'   => ['ms', 'miss'],
            'Dr.'   => ['dr'],
            'Rev.'  => ["rev", "rev'd", "reverend"],
            'Fr.'   => ['fr', 'father'],
            'Sr.'   => ['sr', 'sister'],
            'Prof.' => ['prof', 'professor'],
            'Sir'   => ['sir'],
            'Hon.'  => ['hon', 'honorable'],
            'Pres.' => ['pres', 'president'],
            'Gov'   => ['gov', 'governor', 'governer'],
            'Ofc'   => ['ofc', 'officer'],
            'Msgr'  => ['msgr', 'monsignor'],
            'Br.'   => ['br, brother'],
            'Supt.' => ['supt', 'superintendent'],
            'Rep.'  => ['rep', 'representatitve'],
            'Sen.'  => ['sen', 'senator'],
            'Amb.'  => ['amb', 'ambassador'],
            'Sec.'  => ['sec', 'secretary'],
            'Pvt.'  => ['pvt', 'private'],
            'Cpl.'  => ['cpl', 'corporal'],
            'Sgt.'  => ['sgt', 'sargent'],
            'Adm.'  => ['adm', 'administrative', 'administrator', 'administrater'],
            'Maj.'  => ['maj', 'major'],
            'Capt.' => ['capt', 'captain'],
            'Cmdr.' => ['cmdr', 'commander'],
            'Lt.'   => ['lt', 'lieutenant'],
            'Col.'  => ['col', 'colonel'],
            'Gen.'  => ['gen', 'general'],
            'Bc.'   => ['bc', 'bachelor', 'baccalaureus'],
            'BcA.'  => ['bca', 'bachelor of arts', 'baccalaureus artis'],
            'ICDr.' => ['icdr', 'doctor of canon law', 'juris cononici doctor'],
            'Ing.'  => ['ing', 'engineer', 'ingenieur'],
            'JUDr.' => ['judr', 'juris doctor utriusque', 'juris utriusque doctor', 'doctor rights', 'doctor of law'],
            'MDDr.' => ['mddr', 'doctor of dental medicine', 'medicinae doctor dentium'],
            'MA.'  => ['ma', 'master of arts', 'magister artis'],
            'Mgr.'  => ['mgr', 'master'],
            'MD.'   => ['md', 'doctor of general medicine', 'doctor of medicine'],
            'DVM.'  => ['dvm', 'doctor of veterinary medicine'],
            'PhDr.' => ['phdr', 'doctor of philosophy'],
            'PhMr.' => ['phmr', 'master of pharmacy'],
            'RCDr.' => ['rcdr', 'doctor of economy'],
            'RNDr.' => ['rndr', 'doctor of natural sciences'],
            'DSc.'  => ['dsc', 'doctor of science'],
            'RSDr.' => ['rsdr', 'doctor of socio-political sciences', 'doctor of social sciences'],
            'ThDr.' => ['thdr', 'doctor of theology'],
            'Th.D.' => ['thd', 'dth', 'doctor of theology'],
            'Acad.' => ['acad', 'academian', 'academic'],
            'Art.D.' => ['da', 'artd', 'darts', 'doctor of arts'],
            'DiS.'  => ['dis', 'certified specialist'],
            'As.'   => ['as', 'assistant'],
            'Assoc.'  => ['assoc', 'associate'],
            'Treas.' => ['treas', 'treasurer'],
            'LTh.' => ['lth', 'licentiate of theology'],
            'ThM.' => ['thm', 'master of theology'],
            'MDiv.' => ['mdiv', 'master of divinity'],
            'EdD.' => ['edd', 'ded', 'doctor of education'],
            'PharmDr.' => ['pharmdr', 'doctor of pharmacy'],
            'Arch.' => ['arch', 'architect', 'intrudes upon architectus'],
            ' ' => ['the']
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

    protected $not_nicknames = ["(hons)"];

    protected $temp = [];

    /**
     * Parse Static entry point.
     *
     * @param string $name the full name you wish to parse
     * @return array returns associative array of name parts
     */
    public static function parse($name)
    {
        return (new self())->parse_name($name);
    }

    /**
     * This is the primary method which calls all other methods
     *
     * @param string $name the full name you wish to parse
     * @return array returns associative array of name parts
     */
    public function parse_name($full_name)
    {
        try {
            $full_name = trim($full_name);
            $unparsed_name = $full_name;

            $prefix      = '';
            $first_name  = '';
            $middle_name = '';
            $last_name   = '';
            $suffix      = '';

            $format_name = $this->format_name($full_name, $prefix, $first_name, $middle_name, $last_name, $suffix);

            # If nickname then it's hard to parse other stuff
            if ($this->get_nickname($full_name)) {
                return $format_name;
            }
            # Parse professional prefix or prefixes
            list($full_name, $suffix) = $this->get_professional_suffix($full_name);

            # Grab a list of words from the remainder of the full name
            $unfiltered_name_parts = $this->break_words($full_name);

            list($prefix, $suffix, $unfiltered_name_parts) = $this->get_suffix_and_prefix($suffix, $prefix, $unfiltered_name_parts, $full_name);

            $unfiltered_name_parts = $this->repack_name_parts($unfiltered_name_parts);

            list($unfiltered_name_parts, $first_name, $middle_name, $index) = $this->get_first_and_middle($unfiltered_name_parts, $first_name, $middle_name);

            list($unfiltered_name_parts, $last_name) = $this->get_last_or_first($unfiltered_name_parts, $last_name, $index);

            # return the various parts in an array
            return $this->format_name($unparsed_name, $prefix, trim($first_name), trim($middle_name), trim($last_name), $suffix);
        } catch (Exception $e) {
            return $this->default($format_name, $full_name, $prefix, $suffix);
        }
    }

    public function format_name($full_name, $prefix, $fname, $mname, $lname, $suffix)
    {
        return [
            'full_name' => $full_name,
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
     * @param  array $unfiltered_name_parts
     * @param  string $full_name
     * @return array
     */
    protected function get_suffix_and_prefix($suffix, $prefix, $unfiltered_name_parts, $full_name)
    {
        if (!count($unfiltered_name_parts)) {
            return [
                '',
                '',
                $unfiltered_name_parts,
            ];
        }
        # Is first word a title or multiple titles consecutively?
        # only start looking if there are any words left in the name to process
        while (count($unfiltered_name_parts) && $this->is_prefix($unfiltered_name_parts[0])) {
            $prefix .= "{$this->temp['prefix']} ";
            unset($this->temp['prefix']);
            array_shift($unfiltered_name_parts);
        }
        $prefix = trim($prefix);
        # Find if there is a line suffix, if so then move it out
        # Is last word a suffix or multiple suffixes consecutively?
        while (
            count($unfiltered_name_parts) 
            && $this->is_line_suffix($unfiltered_name_parts[count($unfiltered_name_parts) - 1], $full_name)
        ) {
            $suffix = $suffix != '' ? "{$this->temp['suffix']}, {$suffix}" : $suffix . $this->temp['suffix'];
            unset($this->temp['suffix']);
            array_pop($unfiltered_name_parts);
        }
        $suffix = trim($suffix);

        return [
            $prefix,
            $suffix,
            $unfiltered_name_parts,
        ];
    }

    /**
     * Parse First and maybe Middle names
     * 
     * @param  array $unfiltered_name_parts
     * @param  string $first_name
     * @param  string $middle_name
     * @return array
     */
    protected function get_first_and_middle($unfiltered_name_parts, $first_name, $middle_name)
    {
        # set the ending range after prefix/suffix trim
        $end = count($unfiltered_name_parts);
        # concat the first name
        for ($index = 0; $index < $end - 1; $index++) {
            $word = $unfiltered_name_parts[$index];
            # move on to parsing the last name if we find an indicator of a compound last name (Von, Van, etc)
            # we use $index != 0 to allow for rare cases where an indicator is actually the first name (like "Von Fabella")
            if ($this->is_compound($word) && $index != 0) {
                break;
            }
            # is it a middle initial or part of their first name?
            # if we start off with an initial, we'll call it the first name
            if ($this->is_initial($word)) {
                # is the initial the first word?
                if ($index == 0) {
                    # if so, do a look-ahead to see if they go by their middle name
                    # for ex: "R. Jason Smith" => "Jason Smith" & "R." is stored as an initial
                    # but "R. J. Smith" => "R. Smith" and "J." is stored as an middle
                    $first_name .= " " . mb_strtoupper($word);
                    if ($this->is_initial($unfiltered_name_parts[$index + 1]) && !$first_name) {
                        $first_name .= " " . mb_strtoupper($word);
                    }
                }
                # otherwise, just go ahead and save the initial
                else {
                    $middle_name .= " " . mb_strtoupper($word);
                }
            } elseif (!$first_name) {
                $first_name .= " " . $this->fix_case($word);
            }
            # If not an initial and a first name was set, then this should be the middle name.
            elseif (!$middle_name) {
                $middle_name .= " " . $this->fix_case($word);
            } else {
                # Hard to parse
                throw new Exception('Hard to parse');
            }
        }

        return [
            $unfiltered_name_parts,
            $first_name,
            $middle_name,
            $index,
        ];
    }

    /**
     * Parse Last or maybe first name if the string is one word
     * 
     * @param  array  $unfiltered_name_parts
     * @param  string $last_name
     * @param  int    $index                 Position of the unfiltered_name_parts where the get_first_and_middle function ends
     * @return array
     */
    protected function get_last_or_first($unfiltered_name_parts, $last_name, $index)
    {
        # Resets the index number
        $unfiltered_name_parts = array_values($unfiltered_name_parts);
        $end = count($unfiltered_name_parts);
        $last_name_set = false;
        if (count($unfiltered_name_parts)) {
            # check that we have more than 1 word in our string
            if ($end > 1) {
                # concat the last name and split last name in base and compound
                for ($index; $index < $end; $index++) {
                    $word = $unfiltered_name_parts[$index];
                    # Check if we have a compound, we can have many of them as long as a normal word appears
                    if ($this->is_compound($word)) {
                        $last_name .= " " . $this->fix_case($word);
                    } elseif (!$last_name_set) {
                        $last_name .= " " . $this->fix_case($word);
                        $last_name_set = true;
                    } else {
                        # hard to parse
                        throw new Exception('Hard to parse');
                    }
                }
            } else {
                # otherwise, single word strings are assumed to be first names
                $first_name = $this->fix_case($unfiltered_name_parts[0]);
            }
        } else {
            $first_name = "";
        }

        return [
            $unfiltered_name_parts,
            $last_name,
        ];
    }


    /**
     * Re-pack the unfiltered name parts array and exclude empty words
     *
     * @param  array $unfiltered_name_parts
     * @return array
     */
    protected function repack_name_parts($unfiltered_name_parts)
    {
        $name_parts = [];
        foreach ($unfiltered_name_parts as $name_part) {
            $name_part = rtrim(trim($name_part), ',');
            if (mb_strlen($name_part) == '1' && !$this->mb_ctype_alpha($name_part)) {
                # If any word left is of one character that is not alphabetic then it is not a real word, so remove it
                $name_part = "";
            }
            if (mb_strlen(trim($name_part))) {
                $name_parts[] = $name_part;
            }
        }

        return $name_parts;
    }

    /**
     * Checks for the existence of, and returns professional suffix
     *
     * @param string $name the name you wish to test
     * @return true returns true if the suffix exists, false otherwise
     */
    public function is_professional_suffix($name)
    {
        $found_suffix_arr = [];
        foreach ($this->dict['suffixes']['prof'] as $suffix) {
            if (
                preg_match('/[,\s]' . preg_quote($suffix) . '(\b|\s|$)+/i', $name, $matches)
                || mb_strtolower($suffix) === mb_strtolower($name)
            ) {
                $found_suffix       = isset($matches[0]) ? trim($matches[0]) : $name;
                $found_suffix       = trim($found_suffix, ',');
                $found_suffix_arr[] = trim($found_suffix);
            }
        }

        return $found_suffix_arr;
    }

    protected function get_professional_suffix($full_name)
    {
        $suffix = '';
        # Find all the professional suffixes possible
        $professional_suffix = $this->is_professional_suffix($full_name);

        # The position of the first professional suffix denotes the end of the name and the start of suffixes
        $first_suffix_index = mb_strlen($full_name);
        foreach ($professional_suffix as $pro_suffix) {
            $start = mb_strpos($full_name, $pro_suffix);
            if ($start === FALSE) {
                continue;
            }
            if ($start < $first_suffix_index) {
                $first_suffix_index = $start;
            }
        }

        if (count($professional_suffix)) {
            $real_suffix = $this->check_next_words($full_name, $first_suffix_index);

            if ($real_suffix) {
                # everything to the right of the first professional suffix is part of the suffix
                $suffix = mb_substr($full_name, $first_suffix_index);

                # remove the suffixes from the full_name
                $full_name = mb_substr($full_name, 0, $first_suffix_index);
            } else {
                throw new Exception('Hard to parse');
            }
        }

        return [
            $full_name,
            $suffix,
        ];
    }

    /**
     * Check if next words after a professional suffix are professional suffixes too
     * 
     * @param  string $full_name
     * @param  int $professional_suffix_index Where in the $full_name the first professional suffix was found
     * @return bool
     */
    public function check_next_words($full_name, $professional_suffix_index)
    {
        $results = [];
        $supossed_suffix = mb_substr($full_name, $professional_suffix_index);

        $parts = $this->break_words($supossed_suffix);

        if (count($parts) === 1) {
            return true;
        }

        foreach ($parts as $key => $word) {
            if (count($this->is_professional_suffix($word))) {
                continue;
            }
            return false;
        }

        return true;
    }

    /**
     * Breaks name into individual words
     *
     * @param string $name the full name you wish to parse
     * @return array full list of words broken down by spaces
     */
    public function break_words($name)
    {
        $temp_word_arr  = explode(' ', $name);
        return array_values(array_filter($temp_word_arr, function($word) {
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
     * @param string $name the name you wish to test against
     * @return bool returns true nickname if exists, false otherwise
     */
    protected function get_nickname($name)
    {
        if (!preg_match("/[\(|\"].*?[\)|\"]/", $name, $matches)) {
            return false;
        }

        if (in_array(mb_strtolower($matches[0]), $this->not_nicknames)) {
            return false;
        }

        return true;
    }

    /**
     * Checks word against array of common lineage suffixes
     *
     * @param string $word the single word you wish to test
     * @param string $name full name for context in determining edge-cases
     * @return mixed boolean if false, string if true (returns suffix)
     */
    protected function is_line_suffix($word, $name)
    {
        # Ignore periods and righ commas, normalize case
        $word = str_replace('.', '', mb_strtolower($word));
        $word = rtrim($word, ',');

        # Search the array for our word
        $line_match = array_search($word, array_map('mb_strtolower', $this->dict['suffixes']['line']));

        # Now test our edge cases based on lineage
        if ($line_match === false) {
            return false;
        }

        # Remove it from the array
        $temp_array = $this->dict['suffixes']['line'];
        unset($temp_array[$line_match]);

        # Make sure we're dealing with the suffix and not a surname
        if (in_array($word, ['senior', 'junior'])) {

            # If name is Joshua Senior, it's pretty likely that Senior is the surname
            # However, if the name is Joshua Jones Senior, then it's likely a suffix
            if ($this->mb_str_word_count($name) < 3) {
                return false;
            }

            # If the word Junior or Senior is contained, but so is some other
            # lineage suffix, then the word is likely a surname and not a suffix
            foreach ($temp_array as $suffix) {
                if (preg_match("/\b" . $suffix . "\b/i", $name)) {
                    return false;
                }
            }
        }

        # Store our match
        $this->temp['suffix'] = $this->dict['suffixes']['line'][$line_match];
        return true;
    }

    /**
     * Checks word against list of common honorific prefixes
     *
     * @param string $word the single word you wish to test
     * @return boolean
     */
    protected function is_prefix($word)
    {
        $word = str_replace('.', '', mb_strtolower($word));
        foreach ($this->dict['prefix'] as $replace => $originals) {
            if (in_array($word, $originals)) {
                $this->temp['prefix'] = $replace;
                return true;
            }
        }
        return false;
    }

    /**
     * Checks our dictionary of compound indicators to see if last name is compound
     *
     * @param string $word the single word you wish to test
     * @return boolean
     */
    protected function is_compound($word)
    {
        return in_array(mb_strtolower($word), $this->dict['compound']);
    }

    /**
     * Test string to see if it's a single letter/initial (period optional)
     *
     * @param string $word the single word you wish to test
     * @return boolean
     */
    protected function is_initial($word)
    {
        return ((mb_strlen($word) == 1) || (mb_strlen($word) == 2 && $word[1] == "."));
    }

    protected function default($format_name, $full_name, $prefix, $suffix)
    {
        $parts = $this->break_words($full_name);
        if (count($parts) === 3) {
            return $this->format_name($full_name['full_name'], $prefix, trim($parts[0]), trim($parts[1]), trim($parts[2]), $suffix);
        }

        return $format_name;
    }

    # ucfirst words split by dashes or periods
    # ucfirst all upper/lower strings, but leave camelcase words alone
    public function fix_case($word)
    {
        # Fix case for words split by periods (J.P.)
        if (mb_strpos($word, '.') !== false) {
            $word = $this->safe_ucfirst(".", $word);
        }

        # Fix case for words split by hyphens (Kimura-Fay)
        if (mb_strpos($word, '-') !== false) {
            $word = $this->safe_ucfirst("-", $word);
        }

        # Special case for single letters
        if (mb_strlen($word) == 1) {
            $word = mb_strtoupper($word);
        }

        # Special case for 2-letter words
        if (mb_strlen($word) == 2) {
            # Both letters vowels (uppercase both)
            $first_is_vowel = in_array(mb_strtolower($word[0]), $this->dict['vowels']);
            $second_is_vowel = in_array(mb_strtolower($word[1]), $this->dict['vowels']);

            if ($first_is_vowel && $second_is_vowel) {
                $word = mb_strtoupper($word);
            }
            # Both letters consonants (uppercase both)
            if (!$first_is_vowel && !$second_is_vowel) {
                $word = mb_strtoupper($word);
            }
            # First letter is vowel, second letter consonant (uppercase first)
            if ($first_is_vowel && !$second_is_vowel) {
                $word = $this->mb_ucfirst(mb_strtolower($word));
            }
            # First letter consonant, second letter vowel or "y" (uppercase first)
            if (!$first_is_vowel && ($second_is_vowel || mb_strtolower($word[1]) == 'y')) {
                $word = $this->mb_ucfirst(mb_strtolower($word));
            }
        }

        # Fix case for words which aren't initials, but are all uppercase or lowercase
        if ((mb_strlen($word) >= 3) && ($this->mb_ctype_upper($word) || $this->mb_ctype_lower($word))) {
            $word = $this->mb_ucfirst(mb_strtolower($word));
        }

        return $word;
    }

    # helper public function for fix_case
    public function safe_ucfirst($seperator, $word)
    {
        # uppercase words split by the seperator (ex. dashes or periods)
        $parts = explode($seperator, $word);
        foreach ($parts as $word) {
            $words[] = ($this->is_camel_case($word)) ? $word : $this->mb_ucfirst(mb_strtolower($word));
        }
        return implode($seperator, $words);
    }

    /**
     * Checks for camelCase words such as McDonald and MacElroy
     *
     * @param string $word the single word you wish to test
     * @return boolean
     */
    protected function is_camel_case($word)
    {
        return (bool) preg_match('/\p{L}(\p{Lu}*\p{Ll}\p{Ll}*\p{Lu}|\p{Ll}*\p{Lu}\p{Lu}*\p{Ll})\p{L}*/', $word);
    }

    # helper public function for multibytes ctype_alpha
    public function mb_ctype_alpha($text)
    {
        return (bool) preg_match('/^\p{L}*$/', $text);
    }

    # helper public function for multibytes ctype_lower
    public function mb_ctype_lower($text)
    {
        return (bool) preg_match('/^\p{Ll}*$/', $text);
    }

    # helper public function for multibytes ctype_upper
    public function mb_ctype_upper($text)
    {
        return (bool) preg_match('/^\p{Lu}*$/', $text);
    }

    # helper public function for multibytes str_word_count
    public function mb_str_word_count($text)
    {
        if (empty($text)) {
            return 0;
        } else {
            return preg_match('/s+/', $text) + 1;
        }
    }

    # helper public function for multibytes ucfirst
    public function mb_ucfirst($string)
    {
        $strlen    = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then      = mb_substr($string, 1, $strlen - 1);
        return mb_strtoupper($firstChar) . $then;
    }
}
