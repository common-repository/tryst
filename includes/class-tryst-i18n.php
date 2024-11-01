<?php
/**
* Define the internationalization functionality
*
* Loads and defines the internationalization files for this plugin
* so that it is ready for translation.
*
* @link       https://matteus.dev
* @since      1.0.0
*
* @package    Tryst
* @subpackage Tryst/includes
*/
/**
* Define the internationalization functionality.
*
* Loads and defines the internationalization files for this plugin
* so that it is ready for translation.
*
* @since      1.0.0
* @package    Tryst
* @subpackage Tryst/includes
* @author     Matteus Barbosa <contato@desenvolvedormatteus.com.br>
*/
class Tryst_i18n {
	private $form_country;
	/**
	* Load the plugin text domain for translation.
	*
	* @since    1.0.0
	*/
	public function __construct($form_country){
		$this->form_country = $form_country;
	}
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'tryst',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
	function force_locale_filter()
	{
		return $this->form_country;
	}

	function getDateFormatted($date){
		return $date->format($this->getLocaleDateString());
	}

	function getLocaleDateString(){

		$formats = [
		   "ar-SA" => "d/m/Y",
		   "bg-BG" => "d.M.Y",
		   "ca-ES" => "d/m/Y",
		   "zh-TW" => "Y/M/d",
		   "cs-CZ" => "d.M.Y",
		   "da-DK" => "d-m-Y",
		   "de-DE" => "d.m.Y",
		   "el-GR" => "d/M/Y",
		   "en-US" => "Y-m-d",
		   "fi-FI" => "d.M.Y",
		   "fr-FR" => "d/m/Y",
		   "he-IL" => "d/m/Y",
		   "hu-HU" => "Y. m. d.",
		   "is-IS" => "d.M.Y",
		   "it-IT" => "d/m/Y",
		   "ja-JP" => "Y/m/d",
		   "ko-KR" => "Y-m-d",
		   "nl-NL" => "d-M-Y",
		   "nb-NO" => "d.m.Y",
		   "pl-PL" => "Y-m-d",
		   "pt-BR" => "d/M/Y",
		   "ro-RO" => "d.m.Y",
		   "ru-RU" => "d.m.Y",
		   "hr-HR" => "d.M.Y",
		   "sk-SK" => "d. M. Y",
		   "sq-AL" => "Y-m-d",
		   "sv-SE" => "Y-m-d",
		   "th-TH" => "d/M/Y",
		   "tr-TR" => "d.m.Y",
		   "ur-PK" => "d/m/Y",
		   "id-ID" => "d/m/Y",
		   "uk-UA" => "d.m.Y",
		   "be-BY" => "d.m.Y",
		   "sl-SI" => "d.M.Y",
		   "et-EE" => "d.m.Y",
		   "lv-LV" => "Y.m.d.",
		   "lt-LT" => "Y.m.d",
		   "fa-IR" => "m/d/Y",
		   "vi-VN" => "d/m/Y",
		   "hy-AM" => "d.m.Y",
		   "az-Latn-AZ" => "d.m.Y",
		   "eu-ES" => "Y/m/d",
		   "mk-MK" => "d.m.Y",
		   "af-ZA" => "Y/m/d",
		   "ka-GE" => "d.m.Y",
		   "fo-FO" => "d-m-Y",
		   "hi-IN" => "d-m-Y",
		   "ms-MY" => "d/m/Y",
		   "kk-KZ" => "d.m.Y",
		   "ky-KG" => "d.m.Y",
		   "sw-KE" => "M/d/Y",
		   "uz-Latn-UZ" => "d/m Y",
		   "tt-RU" => "d.m.Y",
		   "pa-IN" => "d-m-Y",
		   "gu-IN" => "d-m-Y",
		   "ta-IN" => "d-m-Y",
		   "te-IN" => "d-m-Y",
		   "kn-IN" => "d-m-Y",
		   "mr-IN" => "d-m-Y",
		   "sa-IN" => "d-m-Y",
		   "mn-MN" => "Y.m.d",
		   "gl-ES" => "d/m/Y",
		   "kok-IN" => "d-m-Y",
		   "syr-SY" => "d/m/Y",
		   "dv-MV" => "d/m/Y",
		   "ar-IQ" => "d/m/Y",
		   "zh-CN" => "Y/M/d",
		   "de-CH" => "d.m.Y",
		   "en-GB" => "d/m/Y",
		   "es-MX" => "d/m/Y",
		   "fr-BE" => "d/m/Y",
		   "it-CH" => "d.m.Y",
		   "nl-BE" => "d/m/Y",
		   "nn-NO" => "d.m.Y",
		   "pt-PT" => "d-m-Y",
		   "sr-Latn-CS" => "d.M.Y",
		   "sv-FI" => "d.M.Y",
		   "az-Cyrl-AZ" => "d.m.Y",
		   "ms-BN" => "d/m/Y",
		   "uz-Cyrl-UZ" => "d.m.Y",
		   "ar-EG" => "d/m/Y",
		   "zh-HK" => "d/M/Y",
		   "de-AT" => "d.m.Y",
		   "en-AU" => "d/m/Y",
		   "es-ES" => "d/m/Y",
		   "fr-CA" => "Y-m-d",
		   "sr-Cyrl-CS" => "d.M.Y",
		   "ar-LY" => "d/m/Y",
		   "zh-SG" => "d/M/Y",
		   "de-LU" => "d.m.Y",
		   "en-CA" => "d/m/Y",
		   "es-GT" => "d/m/Y",
		   "fr-CH" => "d.m.Y",
		   "ar-DZ" => "d-m-Y",
		   "zh-MO" => "d/M/Y",
		   "de-LI" => "d.m.Y",
		   "en-NZ" => "d/m/Y",
		   "es-CR" => "d/m/Y",
		   "fr-LU" => "d/m/Y",
		   "ar-MA" => "d-m-Y",
		   "en-IE" => "d/m/Y",
		   "es-PA" => "m/d/Y",
		   "fr-MC" => "d/m/Y",
		   "ar-TN" => "d-m-Y",
		   "en-ZA" => "Y/m/d",
		   "es-DO" => "d/m/Y",
		   "ar-OM" => "d/m/Y",
		   "en-JM" => "d/m/Y",
		   "es-VE" => "d/m/Y",
		   "ar-YE" => "d/m/Y",
		   "en-029" => "m/d/Y",
		   "es-CO" => "d/m/Y",
		   "ar-SY" => "d/m/Y",
		   "en-BZ" => "d/m/Y",
		   "es-PE" => "d/m/Y",
		   "ar-JO" => "d/m/Y",
		   "en-TT" => "d/m/Y",
		   "es-AR" => "d/m/Y",
		   "ar-LB" => "d/m/Y",
		   "en-ZW" => "M/d/Y",
		   "es-EC" => "d/m/Y",
		   "ar-KW" => "d/m/Y",
		   "en-PH" => "M/d/Y",
		   "es-CL" => "d-m-Y",
		   "ar-AE" => "d/m/Y",
		   "es-UY" => "d/m/Y",
		   "ar-BH" => "d/m/Y",
		   "es-PY" => "d/m/Y",
		   "ar-QA" => "d/m/Y",
		   "es-BO" => "d/m/Y",
		   "es-SV" => "d/m/Y",
		   "es-HN" => "d/m/Y",
		   "es-NI" => "d/m/Y",
		   "es-PR" => "d/m/Y",
		   "am-ET" => "d/M/Y",
		   "tzm-Latn-DZ" => "d-m-Y",
		   "iu-Latn-CA" => "d/m/Y",
		   "sma-NO" => "d.m.Y",
		   "mn-Mong-CN" => "Y/M/d",
		   "gd-GB" => "d/m/Y",
		   "en-MY" => "d/M/Y",
		   "prs-AF" => "d/m/Y",
		   "bn-BD" => "d-m-Y",
		   "wo-SN" => "d/m/Y",
		   "rw-RW" => "M/d/Y",
		   "qut-GT" => "d/m/Y",
		   "sah-RU" => "m.d.Y",
		   "gsw-FR" => "d/m/Y",
		   "co-FR" => "d/m/Y",
		   "oc-FR" => "d/m/Y",
		   "mi-NZ" => "d/m/Y",
		   "ga-IE" => "d/m/Y",
		   "se-SE" => "Y-m-d",
		   "br-FR" => "d/m/Y",
		   "smn-FI" => "d.M.Y",
		   "moh-CA" => "M/d/Y",
		   "arn-CL" => "d-m-Y",
		   "ii-CN" => "Y/M/d",
		   "dsb-DE" => "d. M. Y",
		   "ig-NG" => "d/M/Y",
		   "kl-GL" => "d-m-Y",
		   "lb-LU" => "d/m/Y",
		   "ba-RU" => "d.m.Y",
		   "nso-ZA" => "Y/m/d",
		   "quz-BO" => "d/m/Y",
		   "yo-NG" => "d/M/Y",
		   "ha-Latn-NG" => "d/M/Y",
		   "fil-PH" => "M/d/Y",
		   "ps-AF" => "d/m/Y",
		   "fy-NL" => "d-M-Y",
		   "ne-NP" => "M/d/Y",
		   "se-NO" => "d.m.Y",
		   "iu-Cans-CA" => "d/M/Y",
		   "sr-Latn-RS" => "d.M.Y",
		   "si-LK" => "Y-m-d",
		   "sr-Cyrl-RS" => "d.M.Y",
		   "lo-LA" => "d/m/Y",
		   "km-KH" => "Y-m-d",
		   "cy-GB" => "d/m/Y",
		   "bo-CN" => "Y/M/d",
		   "sms-FI" => "d.M.Y",
		   "as-IN" => "d-m-Y",
		   "ml-IN" => "d-m-Y",
		   "en-IN" => "d-m-Y",
		   "or-IN" => "d-m-Y",
		   "bn-IN" => "d-m-Y",
		   "tk-TM" => "d.m.Y",
		   "bs-Latn-BA" => "d.M.Y",
		   "mt-MT" => "d/m/Y",
		   "sr-Cyrl-ME" => "d.M.Y",
		   "se-FI" => "d.M.Y",
		   "zu-ZA" => "Y/m/d",
		   "xh-ZA" => "Y/m/d",
		   "tn-ZA" => "Y/m/d",
		   "hsb-DE" => "d. M. Y",
		   "bs-Cyrl-BA" => "d.M.Y",
		   "tg-Cyrl-TJ" => "d.m.Y",
		   "sr-Latn-BA" => "d.M.Y",
		   "smj-NO" => "d.m.Y",
		   "rm-CH" => "d/m/Y",
		   "smj-SE" => "Y-m-d",
		   "quz-EC" => "d/m/Y",
		   "quz-PE" => "d/m/Y",
		   "hr-BA" => "d.M.Y.",
		   "sr-Latn-ME" => "d.M.Y",
		   "sma-SE" => "Y-m-d",
		   "en-SG" => "d/M/Y",
		   "ug-CN" => "Y-M-d",
		   "sr-Cyrl-BA" => "d.M.Y",
		   "es-US" => "M/d/Y"
		];
		
		return $formats[$this->form_country];
		
	} 
}
