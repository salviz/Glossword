<?php
/**
 *  Glossword - glossary compiler (http://glossword.biz/)
 *  © 2008 Glossword.biz team
 *  © 2002-2008 Dmitry N. Shilnikov <dev at glossword dot info>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  (see `http://creativecommons.org/licenses/GPL/2.0/' for details)
 */
/* --------------------------------------------------------
 * Helps to uppercase and lowercase characters
 * Removes special characters
 * Addition to Translation Kit, http://glossword.info/tkit/
 * ----------------------------------------------------- */
if (!defined('IS_CLASS_CASE')) { define('IS_CLASS_CASE', 1);
$tmp['mtime'] = explode(' ', microtime());
$tmp['start_time'] = (float)$tmp['mtime'][1] + (float)$tmp['mtime'][0];
/* ------------------------------------------------------*/
$sys['class_case'] = 'gwv_casemap';
class gwv_casemap
{
	/* Case mapping */
	var $ar = array();
	/* Special characters mapping */
	var $arsp = array();
	/* Array with profiles, Latin (1) */
	var $arp = array(1);
	var $arp_sp = array(1);
	/* Enable usage of PHP-extension `mbstring' */
	var $is_use_mbstring = 0;
	/* No any of case mappings is in memory */
	var $is_loaded = 0;
	var $is_loaded_sp = 0;
	/* Character encoding */
	var $encoding = 'UTF-8';
	/* Autostart */
	function gwv_casemap($arp = array(1), $arp_sp = array(1))
	{
		$this->arp = $arp;
		$this->arp_sp = $arp_sp;
	}
	/* Reload settings */
	function reload($arp = array(1), $arp_sp = array(1))
	{
		$this->arp = $arp;
		$this->arp_sp = $arp_sp;
		$this->is_loaded = 0;
		$this->is_loaded_sp = 0;
	}
	/**
	 * @access private
	 */
	function _load_profile($ar = array(1))
	{
		/* Exit when `mbstring' is enabled or the casemap has been loaded before */
		if ((function_exists('mb_strtoupper')
			&& function_exists('mb_strtolower')
			&& $this->is_use_mbstring) || $this->is_loaded)
		{
			return;
		}
		$this->ar['lc'] = $this->ar['uc'] = $this->ar['nn'] = $this->ar['tr'] = array();
		switch($this->encoding)
		{
			case 'windows-1251': $func_enc = '_get_windows1251_casemap'; break;
			default: $func_enc = '_get_utf8_casemap'; break;
		}
		for (reset($ar); list($k, $v) = each($ar);)
		{
			$this->ar['lc'] = array_merge($this->ar['lc'], unserialize($this->$func_enc('lc', $v)));
			$this->ar['uc'] = array_merge($this->ar['uc'], unserialize($this->$func_enc('uc', $v)));
			$this->ar['nn'] = array_merge($this->ar['nn'], unserialize($this->$func_enc('nn', $v)));
			$this->ar['tr'] = array_merge($this->ar['tr'], unserialize($this->$func_enc('tr', $v)));
		}
		$this->is_loaded = 1;
	}
	/* */
	function _load_profile_sp($ar = array(1))
	{
		if ($this->is_loaded_sp)
		{
			return;
		}
		$this->ar_sp = array();
		for (reset($ar); list($k, $v) = each($ar);)
		{
			$this->ar_sp = array_merge($this->ar_sp, unserialize($this->_get_specials($v)));
		}
		$this->is_loaded_sp = 1;
	}
	/**
	 * Change a letter case
	 * @access private
	 */
	function _c($t, $src = 'lc', $trg = 'uc')
	{
		/* Read settings */
		$is_use_mbstring = $this->is_use_mbstring;
		/* Disable usage of `mbstring' to allow custom case mapping */
		$this->is_use_mbstring = 0;
		$this->_load_profile($this->arp);
		/* Restore settings */
		$this->is_use_mbstring = $is_use_mbstring;
		if (isset($this->ar[$src]) && isset($this->ar[$trg]))
		{
			for (reset($this->ar[$trg]); list($k, $v) = each($this->ar[$trg]);)
			{
				if (isset($this->ar[$src][$k]))
				{
					$t = str_replace($this->ar[$src][$k], $v, $t);
				}
			}
			return $t;
		}
		return $t;
	}
	/**
	 * Extended Uppercase function
	 * @access public
	 */
	function uc($t)
	{
		if ( function_exists('mb_strtoupper') && $this->is_use_mbstring )
		{
			return mb_strtoupper( $t, $this->encoding );
		}
		return $this->_c( $t, 'lc', 'uc' );
	}
	/**
	 * Extended Lowercase function
	 * @access public
	 */
	function lc( $t )
	{
		if ( function_exists('mb_strtolower') && $this->is_use_mbstring )
		{
			return mb_strtolower( $t, $this->encoding );
		}
		return $this->_c( $t, 'uc', 'lc' );
	}
	/**
	 * "Normalcase" function.
	 * Normaizes already uppercased string
	 * @access public
	 */
	function nc($t)
	{
		return $this->_c($this->uc($t), 'uc', 'nn');
	}
	/**
	 * Converts a lowercased string into transliterated variant
	 * @access public
	 */
	function translit($t)
	{
		return $this->_c($t, 'lc', 'tr');
	}
	/**
	 * Script-based configuration
	 * @access private
	 */
	private function _get_utf8_casemap($map = 'lc', $id_profile = 1)
	{
		/* 1: Latin */
		$uc[1] = 'a:26:{i:0;s:1:"A";i:1;s:1:"B";i:2;s:1:"C";i:3;s:1:"D";i:4;s:1:"E";i:5;s:1:"F";i:6;s:1:"G";i:7;s:1:"H";i:8;s:1:"I";i:9;s:1:"J";i:10;s:1:"K";i:11;s:1:"L";i:12;s:1:"M";i:13;s:1:"N";i:14;s:1:"O";i:15;s:1:"P";i:16;s:1:"Q";i:17;s:1:"R";i:18;s:1:"S";i:19;s:1:"T";i:20;s:1:"U";i:21;s:1:"V";i:22;s:1:"W";i:23;s:1:"X";i:24;s:1:"Y";i:25;s:1:"Z";}';
		$lc[1] = 'a:26:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;s:1:"d";i:4;s:1:"e";i:5;s:1:"f";i:6;s:1:"g";i:7;s:1:"h";i:8;s:1:"i";i:9;s:1:"j";i:10;s:1:"k";i:11;s:1:"l";i:12;s:1:"m";i:13;s:1:"n";i:14;s:1:"o";i:15;s:1:"p";i:16;s:1:"q";i:17;s:1:"r";i:18;s:1:"s";i:19;s:1:"t";i:20;s:1:"u";i:21;s:1:"v";i:22;s:1:"w";i:23;s:1:"x";i:24;s:1:"y";i:25;s:1:"z";}';
		/* 2: Cyrillic: Russian, Mongolian */
		$uc[2] = 'a:34:{i:0;s:2:"А";i:1;s:2:"Б";i:2;s:2:"В";i:3;s:2:"Г";i:4;s:2:"Д";i:5;s:2:"Е";i:6;s:2:"Ё";i:7;s:2:"Ж";i:8;s:2:"З";i:9;s:2:"И";i:10;s:2:"Й";i:11;s:2:"К";i:12;s:2:"Л";i:13;s:2:"М";i:14;s:2:"Н";i:15;s:2:"О";i:16;s:2:"П";i:17;s:2:"Р";i:18;s:2:"С";i:19;s:2:"Т";i:20;s:2:"У";i:21;s:2:"Ф";i:22;s:2:"Х";i:23;s:2:"Ц";i:24;s:2:"Ч";i:25;s:2:"Ш";i:26;s:2:"Щ";i:27;s:2:"Ъ";i:28;s:2:"Ы";i:29;s:2:"Ь";i:30;s:2:"Э";i:31;s:2:"Ю";i:32;s:2:"Я";i:33;s:2:"Ѣ";}';
		$lc[2] = 'a:34:{i:0;s:2:"а";i:1;s:2:"б";i:2;s:2:"в";i:3;s:2:"г";i:4;s:2:"д";i:5;s:2:"е";i:6;s:2:"ё";i:7;s:2:"ж";i:8;s:2:"з";i:9;s:2:"и";i:10;s:2:"й";i:11;s:2:"к";i:12;s:2:"л";i:13;s:2:"м";i:14;s:2:"н";i:15;s:2:"о";i:16;s:2:"п";i:17;s:2:"р";i:18;s:2:"с";i:19;s:2:"т";i:20;s:2:"у";i:21;s:2:"ф";i:22;s:2:"х";i:23;s:2:"ц";i:24;s:2:"ч";i:25;s:2:"ш";i:26;s:2:"щ";i:27;s:2:"ъ";i:28;s:2:"ы";i:29;s:2:"ь";i:30;s:2:"э";i:31;s:2:"ю";i:32;s:2:"я";i:33;s:2:"ѣ";}';
		/* 3: Basic diacritical signs: covers the most of European languages */
		$uc[3] = 'a:41:{i:0;s:2:"À";i:1;s:2:"Á";i:2;s:2:"Â";i:3;s:2:"Ǎ";i:4;s:2:"Ã";i:5;s:2:"Ä";i:6;s:2:"Å";i:7;s:2:"Æ";i:8;s:2:"Ç";i:9;s:2:"È";i:10;s:2:"É";i:11;s:2:"Ê";i:12;s:2:"Ě";i:13;s:2:"Ë";i:14;s:2:"Ì";i:15;s:2:"Í";i:16;s:2:"Î";i:17;s:2:"Ǐ";i:18;s:2:"Ï";i:19;s:2:"Ñ";i:20;s:2:"Ò";i:21;s:2:"Ó";i:22;s:2:"Ô";i:23;s:2:"Ǒ";i:24;s:2:"Õ";i:25;s:2:"Ö";i:26;s:2:"Ø";i:27;s:2:"Ù";i:28;s:2:"Ú";i:29;s:2:"Û";i:30;s:2:"Ǔ";i:31;s:2:"Ü";i:32;s:2:"Ǖ";i:33;s:2:"Ǘ";i:34;s:2:"Ǚ";i:35;s:2:"Ǜ";i:36;s:2:"Ý";i:37;s:2:"Ү";i:38;s:2:"Ө";i:39;s:2:"Ǆ";i:40;s:2:"Ǳ";}';
		$lc[3] = 'a:41:{i:0;s:2:"à";i:1;s:2:"á";i:2;s:2:"â";i:3;s:2:"ǎ";i:4;s:2:"ã";i:5;s:2:"ä";i:6;s:2:"å";i:7;s:2:"æ";i:8;s:2:"ç";i:9;s:2:"è";i:10;s:2:"é";i:11;s:2:"ê";i:12;s:2:"ě";i:13;s:2:"ë";i:14;s:2:"ì";i:15;s:2:"í";i:16;s:2:"î";i:17;s:2:"ǐ";i:18;s:2:"ï";i:19;s:2:"ñ";i:20;s:2:"ò";i:21;s:2:"ó";i:22;s:2:"ô";i:23;s:2:"ǒ";i:24;s:2:"õ";i:25;s:2:"ö";i:26;s:2:"ø";i:27;s:2:"ù";i:28;s:2:"ú";i:29;s:2:"û";i:30;s:2:"ǔ";i:31;s:2:"ü";i:32;s:2:"ǖ";i:33;s:2:"ǘ";i:34;s:2:"ǚ";i:35;s:2:"ǜ";i:36;s:2:"ý";i:37;s:2:"ү";i:38;s:2:"ө";i:39;s:2:"ǆ";i:40;s:2:"ǳ";}';
		/* 4: Cyrillic Extended: Tatar, Kalmyk */
		$uc[4] = 'a:48:{i:0;s:2:"Ѓ";i:1;s:2:"Є";i:2;s:2:"Ѕ";i:3;s:2:"І";i:4;s:2:"Ї";i:5;s:2:"Ј";i:6;s:2:"Ќ";i:7;s:2:"Ў";i:8;s:2:"Џ";i:9;s:2:"Ґ";i:10;s:2:"Љ";i:11;s:2:"Њ";i:12;s:2:"ћ";i:13;s:2:"Ђ";i:14;s:2:"Ҋ";i:15;s:2:"Ҍ";i:16;s:2:"Ҏ";i:17;s:2:"Ґ";i:18;s:2:"Ғ";i:19;s:2:"Ҕ";i:20;s:2:"Җ";i:21;s:2:"Ҙ";i:22;s:2:"Қ";i:23;s:2:"Ҝ";i:24;s:2:"Ҟ";i:25;s:2:"Ҡ";i:26;s:2:"Ң";i:27;s:2:"Ҥ";i:28;s:2:"Ҧ";i:29;s:2:"Ҩ";i:30;s:2:"Ҫ";i:31;s:2:"Ҭ";i:32;s:2:"Ү";i:33;s:2:"Ұ";i:34;s:2:"Ҳ";i:35;s:2:"Ҵ";i:36;s:2:"Ҷ";i:37;s:2:"Ҹ";i:38;s:2:"Һ";i:39;s:2:"Ҽ";i:40;s:2:"Ҿ";i:41;s:2:"Ӂ";i:42;s:2:"Ӄ";i:43;s:2:"Ӆ";i:44;s:2:"Ӈ";i:45;s:2:"Ӊ";i:46;s:2:"Ӌ";i:47;s:2:"Ӎ";}';
		$lc[4] = 'a:48:{i:0;s:2:"ѓ";i:1;s:2:"є";i:2;s:2:"ѕ";i:3;s:2:"і";i:4;s:2:"ї";i:5;s:2:"ј";i:6;s:2:"ќ";i:7;s:2:"ў";i:8;s:2:"џ";i:9;s:2:"ґ";i:10;s:2:"љ";i:11;s:2:"њ";i:12;s:2:"Ћ";i:13;s:2:"ђ";i:14;s:2:"ҋ";i:15;s:2:"ҍ";i:16;s:2:"ҏ";i:17;s:2:"ґ";i:18;s:2:"ғ";i:19;s:2:"ҕ";i:20;s:2:"җ";i:21;s:2:"ҙ";i:22;s:2:"қ";i:23;s:2:"ҝ";i:24;s:2:"ҟ";i:25;s:2:"ҡ";i:26;s:2:"ң";i:27;s:2:"ҥ";i:28;s:2:"ҧ";i:29;s:2:"ҩ";i:30;s:2:"ҫ";i:31;s:2:"ҭ";i:32;s:2:"ү";i:33;s:2:"ұ";i:34;s:2:"ҳ";i:35;s:2:"ҵ";i:36;s:2:"ҷ";i:37;s:2:"ҹ";i:38;s:2:"һ";i:39;s:2:"ҽ";i:40;s:2:"ҿ";i:41;s:2:"ӂ";i:42;s:2:"ӄ";i:43;s:2:"ӆ";i:44;s:2:"ӈ";i:45;s:2:"ӊ";i:46;s:2:"ӌ";i:47;s:2:"ӎ";}';
		/* 5: Modern Greek including polytonic */
		$uc[5] = 'a:142:{i:0;s:2:"Ά";i:1;s:2:"Έ";i:2;s:2:"Ί";i:3;s:2:"Ϊ";i:4;s:2:"Ό";i:5;s:2:"Ή";i:6;s:2:"Ύ";i:7;s:2:"Ϋ";i:8;s:2:"Ώ";i:9;s:2:"Α";i:10;s:2:"Β";i:11;s:2:"Γ";i:12;s:2:"Δ";i:13;s:2:"Ε";i:14;s:2:"Ζ";i:15;s:2:"Η";i:16;s:2:"Θ";i:17;s:2:"Ι";i:18;s:2:"Κ";i:19;s:2:"Λ";i:20;s:2:"Μ";i:21;s:2:"Ν";i:22;s:2:"Ξ";i:23;s:2:"Ο";i:24;s:2:"Π";i:25;s:2:"Ρ";i:26;s:2:"Σ";i:27;s:2:"Σ";i:28;s:2:"Τ";i:29;s:2:"Υ";i:30;s:2:"Φ";i:31;s:2:"Χ";i:32;s:2:"Ψ";i:33;s:2:"Ω";i:34;s:2:"Ϙ";i:35;s:2:"Ϛ";i:36;s:2:"Ϝ";i:37;s:2:"Ϟ";i:38;s:2:"Ϡ";i:39;s:2:"Ϣ";i:40;s:2:"Ϥ";i:41;s:2:"Ϧ";i:42;s:2:"Ϩ";i:43;s:2:"Ϫ";i:44;s:2:"Ϭ";i:45;s:2:"Ϯ";i:46;s:3:"Ἀ";i:47;s:3:"Ἁ";i:48;s:3:"Ἂ";i:49;s:3:"Ἃ";i:50;s:3:"Ἄ";i:51;s:3:"Ἅ";i:52;s:3:"Ἆ";i:53;s:3:"Ἇ";i:54;s:3:"Ἐ";i:55;s:3:"Ἑ";i:56;s:3:"Ἒ";i:57;s:3:"Ἓ";i:58;s:3:"Ἔ";i:59;s:3:"Ἕ";i:60;s:3:"Ἠ";i:61;s:3:"Ἡ";i:62;s:3:"Ἢ";i:63;s:3:"Ἣ";i:64;s:3:"Ἤ";i:65;s:3:"Ἥ";i:66;s:3:"Ἦ";i:67;s:3:"Ἧ";i:68;s:3:"Ἰ";i:69;s:3:"Ἱ";i:70;s:3:"Ἲ";i:71;s:3:"Ἳ";i:72;s:3:"Ἴ";i:73;s:3:"Ἵ";i:74;s:3:"Ἶ";i:75;s:3:"Ἷ";i:76;s:3:"Ὀ";i:77;s:3:"Ὁ";i:78;s:3:"Ὂ";i:79;s:3:"Ὃ";i:80;s:3:"Ὄ";i:81;s:3:"Ὅ";i:82;s:3:"Ὑ";i:83;s:3:"Ὓ";i:84;s:3:"Ὕ";i:85;s:3:"Ὗ";i:86;s:3:"Ὠ";i:87;s:3:"Ὡ";i:88;s:3:"Ὢ";i:89;s:3:"Ὣ";i:90;s:3:"Ὤ";i:91;s:3:"Ὥ";i:92;s:3:"Ὦ";i:93;s:3:"Ὧ";i:94;s:3:"Ὰ";i:95;s:3:"Ά";i:96;s:3:"Ὲ";i:97;s:3:"Έ";i:98;s:3:"Ὴ";i:99;s:3:"Ή";i:100;s:3:"Ὶ";i:101;s:3:"Ί";i:102;s:3:"Ὸ";i:103;s:3:"Ό";i:104;s:3:"Ὺ";i:105;s:3:"Ύ";i:106;s:3:"Ὼ";i:107;s:3:"Ώ";i:108;s:3:"ᾈ";i:109;s:3:"ᾉ";i:110;s:3:"ᾊ";i:111;s:3:"ᾋ";i:112;s:3:"ᾌ";i:113;s:3:"ᾍ";i:114;s:3:"ᾎ";i:115;s:3:"ᾏ";i:116;s:3:"ᾘ";i:117;s:3:"ᾙ";i:118;s:3:"ᾚ";i:119;s:3:"ᾛ";i:120;s:3:"ᾜ";i:121;s:3:"ᾝ";i:122;s:3:"ᾞ";i:123;s:3:"ᾟ";i:124;s:3:"ᾨ";i:125;s:3:"ᾩ";i:126;s:3:"ᾪ";i:127;s:3:"ᾫ";i:128;s:3:"ᾬ";i:129;s:3:"ᾭ";i:130;s:3:"ᾮ";i:131;s:3:"ᾯ";i:132;s:3:"Ᾰ";i:133;s:3:"Ᾱ";i:134;s:3:"ᾼ";i:135;s:3:"ῌ";i:136;s:3:"Ῐ";i:137;s:3:"Ῑ";i:138;s:3:"Ῠ";i:139;s:3:"Ῡ";i:140;s:3:"Ῥ";i:141;s:3:"ῼ";}';
		$lc[5] = 'a:142:{i:0;s:2:"ά";i:1;s:2:"έ";i:2;s:2:"ί";i:3;s:2:"ϊ";i:4;s:2:"ό";i:5;s:2:"ή";i:6;s:2:"ύ";i:7;s:2:"ϋ";i:8;s:2:"ώ";i:9;s:2:"α";i:10;s:2:"β";i:11;s:2:"γ";i:12;s:2:"δ";i:13;s:2:"ε";i:14;s:2:"ζ";i:15;s:2:"η";i:16;s:2:"θ";i:17;s:2:"ι";i:18;s:2:"κ";i:19;s:2:"λ";i:20;s:2:"μ";i:21;s:2:"ν";i:22;s:2:"ξ";i:23;s:2:"ο";i:24;s:2:"π";i:25;s:2:"ρ";i:26;s:2:"σ";i:27;s:2:"ς";i:28;s:2:"τ";i:29;s:2:"υ";i:30;s:2:"φ";i:31;s:2:"χ";i:32;s:2:"ψ";i:33;s:2:"ω";i:34;s:2:"ϙ";i:35;s:2:"ϛ";i:36;s:2:"ϝ";i:37;s:2:"ϟ";i:38;s:2:"ϡ";i:39;s:2:"ϣ";i:40;s:2:"ϥ";i:41;s:2:"ϧ";i:42;s:2:"ϩ";i:43;s:2:"ϫ";i:44;s:2:"ϭ";i:45;s:2:"ϯ";i:46;s:3:"ἀ";i:47;s:3:"ἁ";i:48;s:3:"ἂ";i:49;s:3:"ἃ";i:50;s:3:"ἄ";i:51;s:3:"ἅ";i:52;s:3:"ἆ";i:53;s:3:"ἇ";i:54;s:3:"ἐ";i:55;s:3:"ἑ";i:56;s:3:"ἒ";i:57;s:3:"ἓ";i:58;s:3:"ἔ";i:59;s:3:"ἕ";i:60;s:3:"ἠ";i:61;s:3:"ἡ";i:62;s:3:"ἢ";i:63;s:3:"ἣ";i:64;s:3:"ἤ";i:65;s:3:"ἥ";i:66;s:3:"ἦ";i:67;s:3:"ἧ";i:68;s:3:"ἰ";i:69;s:3:"ἱ";i:70;s:3:"ἲ";i:71;s:3:"ἳ";i:72;s:3:"ἴ";i:73;s:3:"ἵ";i:74;s:3:"ἶ";i:75;s:3:"ἷ";i:76;s:3:"ὀ";i:77;s:3:"ὁ";i:78;s:3:"ὂ";i:79;s:3:"ὃ";i:80;s:3:"ὄ";i:81;s:3:"ὅ";i:82;s:3:"ὑ";i:83;s:3:"ὓ";i:84;s:3:"ὕ";i:85;s:3:"ὗ";i:86;s:3:"ὠ";i:87;s:3:"ὡ";i:88;s:3:"ὢ";i:89;s:3:"ὣ";i:90;s:3:"ὤ";i:91;s:3:"ὥ";i:92;s:3:"ὦ";i:93;s:3:"ὧ";i:94;s:3:"ὰ";i:95;s:3:"ά";i:96;s:3:"ὲ";i:97;s:3:"έ";i:98;s:3:"ὴ";i:99;s:3:"ή";i:100;s:3:"ὶ";i:101;s:3:"ί";i:102;s:3:"ὸ";i:103;s:3:"ό";i:104;s:3:"ὺ";i:105;s:3:"ύ";i:106;s:3:"ὼ";i:107;s:3:"ώ";i:108;s:3:"ᾀ";i:109;s:3:"ᾁ";i:110;s:3:"ᾂ";i:111;s:3:"ᾃ";i:112;s:3:"ᾄ";i:113;s:3:"ᾅ";i:114;s:3:"ᾆ";i:115;s:3:"ᾇ";i:116;s:3:"ᾐ";i:117;s:3:"ᾑ";i:118;s:3:"ᾒ";i:119;s:3:"ᾓ";i:120;s:3:"ᾔ";i:121;s:3:"ᾕ";i:122;s:3:"ᾖ";i:123;s:3:"ᾗ";i:124;s:3:"ᾠ";i:125;s:3:"ᾡ";i:126;s:3:"ᾢ";i:127;s:3:"ᾣ";i:128;s:3:"ᾤ";i:129;s:3:"ᾥ";i:130;s:3:"ᾦ";i:131;s:3:"ᾧ";i:132;s:3:"ᾰ";i:133;s:3:"ᾱ";i:134;s:3:"ᾳ";i:135;s:3:"ῃ";i:136;s:3:"ῐ";i:137;s:3:"ῑ";i:138;s:3:"ῠ";i:139;s:3:"ῡ";i:140;s:3:"ῥ";i:141;s:3:"ῳ";}';
		/* 6: Latvian, Czech */
		$uc[6] = 'a:66:{i:0;s:2:"Ā";i:1;s:2:"Ă";i:2;s:2:"Ą";i:3;s:2:"Ć";i:4;s:2:"Ĉ";i:5;s:2:"Ċ";i:6;s:2:"Č";i:7;s:2:"Ď";i:8;s:2:"Đ";i:9;s:2:"Ē";i:10;s:2:"Ĕ";i:11;s:2:"Ė";i:12;s:2:"Ę";i:13;s:2:"Ě";i:14;s:2:"Ĝ";i:15;s:2:"Ğ";i:16;s:2:"Ǧ";i:17;s:2:"Ġ";i:18;s:2:"Ģ";i:19;s:2:"Ĥ";i:20;s:2:"Ħ";i:21;s:2:"Ĩ";i:22;s:2:"Ī";i:23;s:2:"Ĭ";i:24;s:2:"Į";i:25;s:2:"İ";i:26;s:1:"I";i:27;s:2:"Ĳ";i:28;s:2:"Ĵ";i:29;s:2:"Ķ";i:30;s:2:"Ĺ";i:31;s:2:"Ļ";i:32;s:2:"Ľ";i:33;s:2:"Ŀ";i:34;s:2:"Ł";i:35;s:2:"Ń";i:36;s:2:"Ņ";i:37;s:2:"Ň";i:38;s:2:"Ŋ";i:39;s:2:"Ō";i:40;s:2:"Ŏ";i:41;s:2:"Ő";i:42;s:2:"Œ";i:43;s:2:"Ŕ";i:44;s:2:"Ŗ";i:45;s:2:"Ř";i:46;s:2:"Ś";i:47;s:2:"Ŝ";i:48;s:2:"Ş";i:49;s:2:"Ș";i:50;s:2:"Š";i:51;s:2:"Ţ";i:52;s:2:"Ť";i:53;s:2:"Ŧ";i:54;s:2:"Ũ";i:55;s:2:"Ū";i:56;s:2:"Ŭ";i:57;s:2:"Ů";i:58;s:2:"Ű";i:59;s:2:"Ų";i:60;s:2:"Ŵ";i:61;s:2:"Ŷ";i:62;s:2:"Ÿ";i:63;s:2:"Ź";i:64;s:2:"Ż";i:65;s:2:"Ž";}';
		$lc[6] = 'a:66:{i:0;s:2:"ā";i:1;s:2:"ă";i:2;s:2:"ą";i:3;s:2:"ć";i:4;s:2:"ĉ";i:5;s:2:"ċ";i:6;s:2:"č";i:7;s:2:"ď";i:8;s:2:"đ";i:9;s:2:"ē";i:10;s:2:"ĕ";i:11;s:2:"ė";i:12;s:2:"ę";i:13;s:2:"ě";i:14;s:2:"ĝ";i:15;s:2:"ğ";i:16;s:2:"ǧ";i:17;s:2:"ġ";i:18;s:2:"ģ";i:19;s:2:"ĥ";i:20;s:2:"ħ";i:21;s:2:"ĩ";i:22;s:2:"ī";i:23;s:2:"ĭ";i:24;s:2:"į";i:25;s:1:"i";i:26;s:2:"ı";i:27;s:2:"ĳ";i:28;s:2:"ĵ";i:29;s:2:"ķ";i:30;s:2:"ĺ";i:31;s:2:"ļ";i:32;s:2:"ľ";i:33;s:2:"ŀ";i:34;s:2:"ł";i:35;s:2:"ń";i:36;s:2:"ņ";i:37;s:2:"ň";i:38;s:2:"ŋ";i:39;s:2:"ō";i:40;s:2:"ŏ";i:41;s:2:"ő";i:42;s:2:"œ";i:43;s:2:"ŕ";i:44;s:2:"ŗ";i:45;s:2:"ř";i:46;s:2:"ś";i:47;s:2:"ŝ";i:48;s:2:"ş";i:49;s:2:"ș";i:50;s:2:"š";i:51;s:2:"ţ";i:52;s:2:"ť";i:53;s:2:"ŧ";i:54;s:2:"ũ";i:55;s:2:"ū";i:56;s:2:"ŭ";i:57;s:2:"ů";i:58;s:2:"ű";i:59;s:2:"ų";i:60;s:2:"ŵ";i:61;s:2:"ŷ";i:62;s:2:"ÿ";i:63;s:2:"ź";i:64;s:2:"ż";i:65;s:2:"ž";}';
		/* 7: Vietnamese, Berber */
		$uc[7] = 'a:126:{i:0;s:3:"Ạ";i:1;s:3:"Ả";i:2;s:3:"Ấ";i:3;s:3:"Ầ";i:4;s:3:"Ẩ";i:5;s:3:"Ẫ";i:6;s:3:"Ậ";i:7;s:3:"Ắ";i:8;s:3:"Ằ";i:9;s:3:"Ẳ";i:10;s:3:"Ẵ";i:11;s:3:"Ặ";i:12;s:3:"Ẹ";i:13;s:3:"Ẻ";i:14;s:3:"Ẽ";i:15;s:3:"Ế";i:16;s:3:"Ề";i:17;s:3:"Ể";i:18;s:3:"Ễ";i:19;s:3:"Ệ";i:20;s:3:"Ỉ";i:21;s:3:"Ị";i:22;s:3:"Ọ";i:23;s:3:"Ỏ";i:24;s:3:"Ố";i:25;s:3:"Ồ";i:26;s:3:"Ổ";i:27;s:3:"Ỗ";i:28;s:3:"Ộ";i:29;s:3:"Ớ";i:30;s:3:"Ờ";i:31;s:3:"Ở";i:32;s:3:"Ỡ";i:33;s:3:"Ợ";i:34;s:3:"Ụ";i:35;s:3:"Ủ";i:36;s:3:"Ứ";i:37;s:3:"Ừ";i:38;s:3:"Ử";i:39;s:2:"Ư";i:40;s:3:"Ữ";i:41;s:3:"Ự";i:42;s:3:"Ỳ";i:43;s:3:"Ỵ";i:44;s:3:"Ỷ";i:45;s:3:"Ỹ";i:46;s:3:"Ẁ";i:47;s:3:"Ẃ";i:48;s:3:"Ẅ";i:49;s:2:"Ơ";i:50;s:3:"Ḁ";i:51;s:3:"Ḃ";i:52;s:3:"Ḅ";i:53;s:3:"Ḇ";i:54;s:3:"Ḉ";i:55;s:3:"Ḋ";i:56;s:3:"Ḍ";i:57;s:3:"Ḏ";i:58;s:3:"Ḑ";i:59;s:3:"Ḓ";i:60;s:3:"Ḕ";i:61;s:3:"Ḗ";i:62;s:3:"Ḙ";i:63;s:3:"Ḛ";i:64;s:3:"Ḝ";i:65;s:3:"Ḟ";i:66;s:3:"Ḡ";i:67;s:3:"Ḣ";i:68;s:3:"Ḥ";i:69;s:3:"Ḧ";i:70;s:3:"Ḩ";i:71;s:3:"Ḫ";i:72;s:3:"Ḭ";i:73;s:3:"Ḯ";i:74;s:3:"Ḱ";i:75;s:3:"Ḳ";i:76;s:3:"Ḵ";i:77;s:3:"Ḷ";i:78;s:3:"ḷ";i:79;s:3:"Ḹ";i:80;s:3:"Ḻ";i:81;s:3:"Ḽ";i:82;s:3:"Ḿ";i:83;s:3:"Ṁ";i:84;s:3:"Ṃ";i:85;s:3:"Ṅ";i:86;s:3:"Ṇ";i:87;s:3:"Ṉ";i:88;s:3:"Ṋ";i:89;s:3:"Ṍ";i:90;s:3:"Ṏ";i:91;s:3:"Ṑ";i:92;s:3:"Ṓ";i:93;s:3:"Ṕ";i:94;s:3:"Ṗ";i:95;s:3:"Ṙ";i:96;s:3:"Ṛ";i:97;s:3:"Ṝ";i:98;s:3:"Ṟ";i:99;s:3:"Ṡ";i:100;s:3:"Ṣ";i:101;s:3:"Ṥ";i:102;s:3:"Ṧ";i:103;s:3:"Ṩ";i:104;s:3:"Ṫ";i:105;s:3:"Ṭ";i:106;s:3:"Ṯ";i:107;s:3:"Ṱ";i:108;s:3:"Ṳ";i:109;s:3:"Ṵ";i:110;s:3:"Ṷ";i:111;s:3:"Ṹ";i:112;s:3:"Ṻ";i:113;s:3:"Ṽ";i:114;s:3:"Ṿ";i:115;s:3:"Ẁ";i:116;s:3:"Ẃ";i:117;s:3:"Ẅ";i:118;s:3:"Ẇ";i:119;s:3:"Ẉ";i:120;s:3:"Ẋ";i:121;s:3:"Ẍ";i:122;s:3:"Ẏ";i:123;s:3:"Ẑ";i:124;s:3:"Ẓ";i:125;s:3:"Ẕ";}';
		$lc[7] = 'a:126:{i:0;s:3:"ạ";i:1;s:3:"ả";i:2;s:3:"ấ";i:3;s:3:"ầ";i:4;s:3:"ẩ";i:5;s:3:"ẫ";i:6;s:3:"ậ";i:7;s:3:"ắ";i:8;s:3:"ằ";i:9;s:3:"ẳ";i:10;s:3:"ẵ";i:11;s:3:"ặ";i:12;s:3:"ẹ";i:13;s:3:"ẻ";i:14;s:3:"ẽ";i:15;s:3:"ế";i:16;s:3:"ề";i:17;s:3:"ể";i:18;s:3:"ễ";i:19;s:3:"ệ";i:20;s:3:"ỉ";i:21;s:3:"ị";i:22;s:3:"ọ";i:23;s:3:"ỏ";i:24;s:3:"ố";i:25;s:3:"ồ";i:26;s:3:"ổ";i:27;s:3:"ỗ";i:28;s:3:"ộ";i:29;s:3:"ớ";i:30;s:3:"ờ";i:31;s:3:"ở";i:32;s:3:"ỡ";i:33;s:3:"ợ";i:34;s:3:"ụ";i:35;s:3:"ủ";i:36;s:3:"ứ";i:37;s:3:"ừ";i:38;s:3:"ử";i:39;s:2:"ư";i:40;s:3:"ữ";i:41;s:3:"ự";i:42;s:3:"ỳ";i:43;s:3:"ỵ";i:44;s:3:"ỷ";i:45;s:3:"ỹ";i:46;s:3:"ẁ";i:47;s:3:"ẃ";i:48;s:3:"ẅ";i:49;s:2:"ơ";i:50;s:3:"ḁ";i:51;s:3:"ḃ";i:52;s:3:"ḅ";i:53;s:3:"ḇ";i:54;s:3:"ḉ";i:55;s:3:"ḋ";i:56;s:3:"ḍ";i:57;s:3:"ḏ";i:58;s:3:"ḑ";i:59;s:3:"ḓ";i:60;s:3:"ḕ";i:61;s:3:"ḗ";i:62;s:3:"ḙ";i:63;s:3:"ḛ";i:64;s:3:"ḝ";i:65;s:3:"ḟ";i:66;s:3:"ḡ";i:67;s:3:"ḣ";i:68;s:3:"ḥ";i:69;s:3:"ḧ";i:70;s:3:"ḩ";i:71;s:3:"ḫ";i:72;s:3:"ḭ";i:73;s:3:"ḯ";i:74;s:3:"ḱ";i:75;s:3:"ḳ";i:76;s:3:"ḵ";i:77;s:3:"ḷ";i:78;s:3:"ḷ";i:79;s:3:"ḹ";i:80;s:3:"ḻ";i:81;s:3:"ḽ";i:82;s:3:"ḿ";i:83;s:3:"ṁ";i:84;s:3:"ṃ";i:85;s:3:"ṅ";i:86;s:3:"ṇ";i:87;s:3:"ṉ";i:88;s:3:"ṋ";i:89;s:3:"ṍ";i:90;s:3:"ṏ";i:91;s:3:"ṑ";i:92;s:3:"ṓ";i:93;s:3:"ṕ";i:94;s:3:"ṗ";i:95;s:3:"ṙ";i:96;s:3:"ṛ";i:97;s:3:"ṝ";i:98;s:3:"ṟ";i:99;s:3:"ṡ";i:100;s:3:"ṣ";i:101;s:3:"ṥ";i:102;s:3:"ṧ";i:103;s:3:"ṩ";i:104;s:3:"ṫ";i:105;s:3:"ṭ";i:106;s:3:"ṯ";i:107;s:3:"ṱ";i:108;s:3:"ṳ";i:109;s:3:"ṵ";i:110;s:3:"ṷ";i:111;s:3:"ṹ";i:112;s:3:"ṻ";i:113;s:3:"ṽ";i:114;s:3:"ṿ";i:115;s:3:"ẁ";i:116;s:3:"ẃ";i:117;s:3:"ẅ";i:118;s:3:"ẇ";i:119;s:3:"ẉ";i:120;s:3:"ẋ";i:121;s:3:"ẍ";i:122;s:3:"ẏ";i:123;s:3:"ẑ";i:124;s:3:"ẓ";i:125;s:3:"ẕ";}';
		/* 8: Armenian */
		$uc[8] = 'a:39:{i:0;s:2:"Ա";i:1;s:2:"Բ";i:2;s:2:"Գ";i:3;s:2:"Դ";i:4;s:2:"Ե";i:5;s:2:"Զ";i:6;s:2:"Է";i:7;s:2:"Ը";i:8;s:2:"Թ";i:9;s:2:"Ժ";i:10;s:2:"Ի";i:11;s:2:"Լ";i:12;s:2:"Խ";i:13;s:2:"Ծ";i:14;s:2:"Կ";i:15;s:2:"Հ";i:16;s:2:"Ձ";i:17;s:2:"Ղ";i:18;s:2:"Ճ";i:19;s:2:"Մ";i:20;s:2:"Յ";i:21;s:2:"Ն";i:22;s:2:"Շ";i:23;s:2:"Ո";i:24;s:2:"Չ";i:25;s:2:"Պ";i:26;s:2:"Ջ";i:27;s:2:"Ռ";i:28;s:2:"Ս";i:29;s:2:"Վ";i:30;s:2:"Տ";i:31;s:2:"Ր";i:32;s:2:"Ց";i:33;s:2:"Ւ";i:34;s:2:"Փ";i:35;s:2:"Ք";i:36;s:2:"Օ";i:37;s:2:"Ֆ";i:38;s:2:"՚";}';
		$lc[8] = 'a:39:{i:0;s:2:"ա";i:1;s:2:"բ";i:2;s:2:"գ";i:3;s:2:"դ";i:4;s:2:"ե";i:5;s:2:"զ";i:6;s:2:"է";i:7;s:2:"ը";i:8;s:2:"թ";i:9;s:2:"ժ";i:10;s:2:"ի";i:11;s:2:"լ";i:12;s:2:"խ";i:13;s:2:"ծ";i:14;s:2:"կ";i:15;s:2:"հ";i:16;s:2:"ձ";i:17;s:2:"ղ";i:18;s:2:"ճ";i:19;s:2:"մ";i:20;s:2:"յ";i:21;s:2:"ն";i:22;s:2:"շ";i:23;s:2:"ո";i:24;s:2:"չ";i:25;s:2:"պ";i:26;s:2:"ջ";i:27;s:2:"ռ";i:28;s:2:"ս";i:29;s:2:"վ";i:30;s:2:"տ";i:31;s:2:"ր";i:32;s:2:"ց";i:33;s:2:"ւ";i:34;s:2:"փ";i:35;s:2:"ք";i:36;s:2:"օ";i:37;s:2:"ֆ";i:38;s:0:"";}';
		/* 9 mail to <dev at glossword dot info> if you need more language profiles */
		if (isset(${$map}[$id_profile]))
		{
			return ${$map}[$id_profile];
		}
		return 'a:0:{}';
	}
	/**
	 * Database-driven configuration
	 * @access private
	 */
	function _get_utf8_casemap_db($map = 'lc', $id_profile = 1)
	{
		return array();
	}
	/* Remove everything */
	function rm_($t)
	{
		$t = ' '.$this->rm_crlf($t).' ';
		/* add space to HTML-tags */
		$t = str_replace('><', '> <', $t);
		$t = str_replace('<'.'?'.'php', '', $t);
		$t = strip_tags($t);
		/* remove comments? */
		/* remove {TEMPLATES}, {%TEMPLATES%} */
		$t = preg_replace("/{(%)?([A-Za-z0-9:\-_]+)(%)?}/", '', $t);
		$t = $this->rm_entity($t);
		$t = $this->rm_specials($t);
		$t = trim($t);
		return $t;
	}
	/* Remove new lines and tabs */
	function rm_crlf($t)
	{
		return preg_replace("/(\t|\r\n|\n|\r)/", ' ', $t);
	}
	/* Remove HTML-entities */
	function rm_entity($t)
	{
		/* remove hex values first */
		/* then remove others */
		return preg_replace('/&[a-z]+;/', ' ', preg_replace('/&#[x0-9a-fA-F]+;/', ' ', $t));
	}
	/**
	 * Remove numeric values
	 * if (...min=2 and ...max=7) then (keep numbers),
	 * where the length is from 3 to 6.
	 * 12, 345, 5678, 8901234 => 345, 5678
	 * PHP 4.3.2
	 *
	 * @param $t       string   A text
	 * @param $int_min integer  Must be > 1
	 * @param $int_max integer  Must be > $int_min
	 */
	function rm_number($t, $int_min = 2, $int_max = 7)
	{
		if ($int_max == "0") { return $t; }
		if ($int_min < 0) { $int_min = 1; }
		if (($int_max != '')&&($int_max < $int_min)) { $int_max = $int_min + 1; }
/*		
		print('[0-9]{'. $int_min .','. $int_max .'}');
*/
		return preg_replace("/ [0-9]{". $int_min .','. $int_max ."}\b/u", ' ', $t);
	}
	/**
	 * Remove special characters
	 * @access public
	 */
	function rm_specials($t)
	{
		$this->_load_profile_sp($this->arp_sp);
		$t = str_replace(array_keys($this->ar_sp), array_values($this->ar_sp), $t);
		$t = preg_replace("/ {2,}/", ' ', $t);
		return $t;
	}
	/**
	 * Assign new replacement map for special characters
	 * @access public
	 */
	function set_replace_sp($ar)
	{
		if (!is_array($ar)){ return; }
		$this->_load_profile_sp($this->arp_sp);
		for (reset($ar); list($k, $v) = each($ar);)
		{
			$this->ar_sp[$k] = $v;
		}
	}
	/**
	 * Script-based configuration
	 * @access private
	 */
	function _get_specials($id_profile = 1)
	{
		/* Custom */
		$sp[1] = 'a:3:{s:1:".";s:1:" ";s:1:"*";s:1:" ";s:1:"?";s:1:" ";}';
		/* Basic special characters */
		$sp[2] = 'a:91:{s:1:"!";s:1:" ";s:1:""";s:1:" ";s:1:"#";s:1:" ";s:3:"№";s:1:" ";s:1:"$";s:1:" ";s:1:"%";s:1:" ";s:1:"&";s:1:" ";s:1:"\'";s:1:" ";s:1:"(";s:1:" ";s:1:")";s:1:" ";s:1:"+";s:1:" ";s:1:",";s:1:" ";s:1:"-";s:1:" ";s:1:"/";s:1:" ";s:1:":";s:1:" ";s:1:";";s:1:" ";s:1:"<";s:1:" ";s:1:"=";s:1:" ";s:1:">";s:1:" ";s:1:"@";s:1:" ";s:1:"[";s:1:" ";s:1:"\";s:1:" ";s:1:"]";s:1:" ";s:1:"^";s:1:" ";s:1:"_";s:1:" ";s:1:"`";s:1:" ";s:1:"{";s:1:" ";s:1:"|";s:1:" ";s:1:"}";s:1:" ";s:1:"~";s:1:" ";s:1:"";s:1:" ";s:3:"€";s:1:" ";s:2:"";s:1:" ";s:3:"‚";s:1:" ";s:2:"ƒ";s:1:" ";s:3:"„";s:1:" ";s:3:"…";s:1:" ";s:3:"†";s:1:" ";s:3:"‡";s:1:" ";s:2:"ˆ";s:1:" ";s:3:"‰";s:1:" ";s:2:"Š";s:1:"s";s:3:"‹";s:1:" ";s:2:"Œ";s:1:" ";s:2:"";s:1:" ";s:2:"Ž";s:1:"z";s:2:"";s:1:" ";s:2:"";s:1:" ";s:3:"‘";s:1:" ";s:3:"’";s:1:" ";s:3:"“";s:1:" ";s:3:"”";s:1:" ";s:3:"•";s:1:" ";s:3:"–";s:1:" ";s:3:"—";s:1:" ";s:2:"˜";s:1:" ";s:3:"™";s:1:" ";s:2:"š";s:1:" ";s:3:"›";s:1:" ";s:2:"œ";s:1:" ";s:2:"";s:1:" ";s:2:"ž";s:1:"z";s:2:"Ÿ";s:1:"y";s:1:" ";s:1:" ";s:2:"¡";s:1:" ";s:2:"¢";s:1:" ";s:2:"£";s:1:" ";s:2:"¤";s:1:" ";s:2:"¥";s:1:" ";s:2:"¦";s:1:" ";s:2:"§";s:1:" ";s:2:"¨";s:1:" ";s:2:"©";s:1:" ";s:2:"ª";s:1:" ";s:2:"«";s:1:" ";s:2:"¬";s:1:" ";s:2:"­";s:1:" ";s:2:"®";s:1:" ";s:2:"¯";s:1:" ";s:2:"°";s:1:" ";s:2:"±";s:1:" ";s:2:"²";s:1:" ";s:2:"³";s:1:" ";s:2:"´";s:1:" ";s:2:"µ";s:1:" ";s:2:"¶";s:1:" ";s:2:"·";s:1:" ";s:2:"¸";s:1:" ";s:2:"¹";s:1:" ";s:2:"º";s:1:" ";s:2:"»";s:1:" ";}';
		/* Diacritics */
		$sp[3] = 'a:47:{s:2:"̀";s:0:"";s:2:"́";s:0:"";s:2:"̂";s:0:"";s:2:"̃";s:0:"";s:2:"̄";s:0:"";s:2:"̅";s:0:"";s:2:"̆";s:0:"";s:2:"̇";s:0:"";s:2:"̈";s:0:"";s:2:"̉";s:0:"";s:2:"̊";s:0:"";s:2:"̋";s:0:"";s:2:"̌";s:0:"";s:2:"̍";s:0:"";s:2:"̎";s:0:"";s:2:"̏";s:0:"";s:2:"̐";s:0:"";s:2:"̑";s:0:"";s:2:"̒";s:0:"";s:2:"̓";s:0:"";s:2:"̔";s:0:"";s:2:"̕";s:0:"";s:2:"̖";s:0:"";s:2:"̗";s:0:"";s:2:"̘";s:0:"";s:2:"̙";s:0:"";s:2:"̚";s:0:"";s:2:"̛";s:0:"";s:2:"̜";s:0:"";s:2:"̝";s:0:"";s:2:"̞";s:0:"";s:2:"̟";s:0:"";s:2:"̠";s:0:"";s:2:"̡";s:0:"";s:2:"̢";s:0:"";s:2:"̣";s:0:"";s:2:"̤";s:0:"";s:2:"̥";s:0:"";s:2:"̦";s:0:"";s:2:"̧";s:0:"";s:2:"̨";s:0:"";s:2:"̩";s:0:"";s:2:"̪";s:0:"";s:2:"̫";s:0:"";s:2:"̬";s:0:"";s:2:"̭";s:0:"";s:2:"̮";s:0:"";}';
		/* Chinese high specials and fullwidth characters */
		$sp[4] = 'a:93:{s:3:"！";s:1:" ";s:3:"＂";s:1:" ";s:3:"＃";s:1:" ";s:3:"＄";s:1:" ";s:3:"％";s:1:" ";s:3:"＆";s:1:" ";s:3:"＇";s:1:" ";s:3:"（";s:1:" ";s:3:"）";s:1:" ";s:3:"＊";s:1:" ";s:3:"＋";s:1:" ";s:3:"，";s:1:" ";s:3:"－";s:1:" ";s:3:"．";s:1:" ";s:3:"／";s:1:" ";s:3:"０";s:1:"0";s:3:"１";s:1:"1";s:3:"２";s:1:"2";s:3:"３";s:1:"3";s:3:"４";s:1:"4";s:3:"５";s:1:"5";s:3:"６";s:1:"6";s:3:"７";s:1:"7";s:3:"８";s:1:"8";s:3:"９";s:1:"9";s:3:"：";s:1:" ";s:3:"；";s:1:" ";s:3:"＜";s:1:" ";s:3:"＝";s:1:" ";s:3:"＞";s:1:" ";s:3:"？";s:1:" ";s:3:"＠";s:1:" ";s:3:"Ａ";s:1:"A";s:3:"Ｂ";s:1:"B";s:3:"Ｃ";s:1:"C";s:3:"Ｄ";s:1:"D";s:3:"Ｅ";s:1:"E";s:3:"Ｆ";s:1:"F";s:3:"Ｇ";s:1:"G";s:3:"Ｈ";s:1:"H";s:3:"Ｉ";s:1:"I";s:3:"Ｊ";s:1:"J";s:3:"Ｋ";s:1:"K";s:3:"Ｌ";s:1:"L";s:3:"Ｍ";s:1:"M";s:3:"Ｎ";s:1:"N";s:3:"Ｏ";s:1:"O";s:3:"Ｐ";s:1:"P";s:3:"Ｑ";s:1:"Q";s:3:"Ｒ";s:1:"R";s:3:"Ｓ";s:1:"S";s:3:"Ｔ";s:1:"T";s:3:"Ｕ";s:1:"U";s:3:"Ｖ";s:1:"V";s:3:"Ｗ";s:1:"W";s:3:"Ｘ";s:1:"X";s:3:"Ｙ";s:1:"Y";s:3:"Ｚ";s:1:"Z";s:3:"［";s:1:" ";s:3:"＼";s:1:" ";s:3:"］";s:1:" ";s:3:"＾";s:1:" ";s:3:"＿";s:1:" ";s:3:"｀";s:1:" ";s:3:"ａ";s:1:"a";s:3:"ｂ";s:1:"b";s:3:"ｃ";s:1:"c";s:3:"ｄ";s:1:"d";s:3:"ｅ";s:1:"e";s:3:"ｆ";s:1:"f";s:3:"ｇ";s:1:"g";s:3:"ｈ";s:1:"h";s:3:"ｉ";s:1:"i";s:3:"ｊ";s:1:"j";s:3:"ｋ";s:1:"k";s:3:"ｌ";s:1:"l";s:3:"ｍ";s:1:"m";s:3:"ｎ";s:1:"n";s:3:"ｏ";s:1:"o";s:3:"ｐ";s:1:"p";s:3:"ｑ";s:1:"q";s:3:"ｒ";s:1:"r";s:3:"ｓ";s:1:"s";s:3:"ｔ";s:1:"t";s:3:"ｕ";s:1:"u";s:3:"ｖ";s:1:"v";s:3:"ｗ";s:1:"w";s:3:"ｘ";s:1:"x";s:3:"ｙ";s:1:"y";s:3:"ｚ";s:1:"z";s:3:"｛";s:1:" ";s:3:"｜";s:1:" ";s:3:"｝";s:1:" ";}';
		/* Ideographic special characters */
		$sp[5] = 'a:31:{s:3:"　";s:1:" ";s:3:"、";s:1:" ";s:3:"。";s:1:" ";s:3:"〃";s:1:" ";s:3:"〄";s:1:" ";s:3:"々";s:1:" ";s:3:"〆";s:1:" ";s:3:"〇";s:1:" ";s:3:"〈";s:1:" ";s:3:"〉";s:1:" ";s:3:"《";s:1:" ";s:3:"》";s:1:" ";s:3:"「";s:1:" ";s:3:"」";s:1:" ";s:3:"『";s:1:" ";s:3:"』";s:1:" ";s:3:"【";s:1:" ";s:3:"】";s:1:" ";s:3:"〒";s:1:" ";s:3:"〓";s:1:" ";s:3:"〔";s:1:" ";s:3:"〕";s:1:" ";s:3:"〖";s:1:" ";s:3:"〗";s:1:" ";s:3:"〘";s:1:" ";s:3:"〙";s:1:" ";s:3:"〚";s:1:" ";s:3:"〛";s:1:" ";s:3:"〜";s:1:" ";s:3:"〝";s:1:" ";s:3:"〞";s:1:" ";}';
		if (isset($sp[$id_profile]))
		{
			return $sp[$id_profile];
		}
		return array();
	}
	/**
	 * Database-driven configuration
	 * @access private
	 */
	function _get_specials_db($id_profile = 1)
	{
		return array();
	}
}
/* ------------------------------------------------------*/
$tmp['mtime'] = explode(' ', microtime());
$tmp['endtime'] = (float)$tmp['mtime'][1] + (float)$tmp['mtime'][0];
$tmp['time'][__FILE__] = ($tmp['endtime'] - $tmp['start_time']);
}
/* end of file */
?>