<?php
/*
** Zabbix
** Copyright (C) 2001-2017 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


class CHtmlUrlValidator {

	/**
	 * URL is validated if schema validation is enabled (see VALIDATE_URI_SCHEMES).
	 *
	 * Relative URL should start with .php file name.
	 * Absolute URL schema must match schemes mentioned in ZBX_URL_VALID_SCHEMES comma separated list.
	 *
	 * @static
	 *
	 * @param string $url                   URL string to validate.
	 * @param bool   $allow_user_macro      If set to be true, URLs containing user macros will be considered as valid.
	 * @param bool   $validate_uri_schemes  Parameter allows to overwrite global switch VALIDATE_URI_SCHEMES for
	 *                                      specific uses.
	 *
	 * @return bool
	 */
	public static function validate($url, $allow_user_macro = true, $validate_uri_schemes = VALIDATE_URI_SCHEMES) {
		if ($validate_uri_schemes === false) {
			return true;
		}
		elseif ($allow_user_macro) {
			$contains_user_macro = false;
			$user_macro_parser = new CUserMacroParser();
			$strlen = strlen($url);

			for ($pos = 0; $strlen > $pos; $pos++) {
				if ($user_macro_parser->parse($url, $pos) != CParser::PARSE_FAIL) {
					$contains_user_macro = true;
					break;
				}
			}

			if ($contains_user_macro) {
				return true;
			}
		}

		$url = parse_url($url);
		$allowed_schemes = explode(',', strtolower(ZBX_URI_VALID_SCHEMES));

		return ($url && ((array_key_exists('scheme', $url) && in_array(strtolower($url['scheme']), $allowed_schemes))
					|| (array_key_exists('path', $url) && preg_match('/^[a-z_\.]+\.php/i', $url['path']) == 1)
				));
	}
}
