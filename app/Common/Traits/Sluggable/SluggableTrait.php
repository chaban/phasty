<?php namespace Phasty\Common\Traits\Sluggable;

trait SluggableTrait {

	protected $defaultOptions = [
		/**
		 * What attributes do we use to build the slug?
		 * This can be a single field, like "name" which will build a slug from:
		 *
		 *     $model->name;
		 *
		 * Or it can be an array of fields, like ("name", "company"), which builds a slug from:
		 *
		 *     $model->name . ' ' . $model->company;
		 *
		 * If you've defined custom getters in your model, you can use those too,
		 * since Eloquent will call them when you request a custom attribute.
		 *
		 * Defaults to null, which uses the toString() method on your model.
		 */
		'build_from' => null,

		/**
		 * What field to we store the slug in?  Defaults to "slug".
		 * You need to configure this when building the SQL for your database, e.g.:
		 *
		 * Schema::create('users', function($table)
		 * {
		 *    $table->string('slug');
		 * });
		 */
		'save_to' => 'slug',

		/**
		 * The maximum length of a generated slug.  Defaults to "null", which means
		 * no length restrictions are enforced.  Set it to a positive integer if you
		 * want to make sure your slugs aren't too long.
		 */
		'max_length' => null,

		/**
		 * If left to "null", then use Laravel's built-in Str::slug() method to
		 * generate the slug (with the separator defined below).
		 *
		 * Set this to a closure that accepts two parameters (string and separator)
		 * to define a custom slugger.  e.g.:
		 *
		 *    'method' => function( $string, $sep ) {
		 *       return preg_replace('/[^a-z]+/i', $sep, $string);
		 *    },
		 *
		 * Otherwise, this will be treated as a callable to be used.  e.g.:
		 *
		 *        'method' => array('Str','slug'),
		 */
		'method' => null,

		/**
		 * Separator to use if using the default Str::slug() method.  Defaults to a hyphen.
		 */
		'separator' => '-',

		/**
		 * Enforce uniqueness of slugs?  Defaults to true.
		 * If a generated slug already exists, an incremental numeric
		 * value will be appended to the end until a unique slug is found.  e.g.:
		 *
		 *     my-slug
		 *     my-slug-1
		 *     my-slug-2
		 */
		'unique' => true,

		/**
		 * Should we include the trashed items when generating a unique slug?
		 * This only applies if the softDelete property is set for the Eloquent model.
		 * If set to "false", then a new slug could duplicate one that exists on a trashed model.
		 * If set to "true", then uniqueness is enforced across trashed and existing models.
		 */
		'include_trashed' => false,

		/**
		 * Whether to update the slug value when a model is being
		 * re-saved (i.e. already exists).  Defaults to false, which
		 * means slugs are not updated.
		 */
		'on_update' => false,

		/**
		 * An array of slug names that can never be used for this model,
		 * e.g. to prevent collisions with existing routes or controller methods, etc..
		 * Defaults to null (i.e. no reserved names).
		 * Can be a static array, e.g.:
		 *
		 *        'reserved' => array('add', 'delete'),
		 *
		 * or a closure that returns an array of reserved names.
		 * If using a closure, it will accept one parameter: the model itself, and should
		 * return an array of reserved names, or null. e.g.
		 *
		 *        'reserved' => function( Model $model) {
		 *            return $model->some_method_that_returns_an_array();
		 *        }
		 *
		 * In the case of a slug that gets generated with one of these reserved names,
		 * we will do:
		 *
		 *    $slug .= $seperator + "1"
		 *
		 * and continue from there.
		 */
		'reserved' => null,

		/**
		 * Whether or not to use Laravel's caching system to help generate
		 * incremental slug.  Defaults to false.
		 *
		 * Set it to a positive integer to use the cache (the value is the
		 * time to store slug increments in the cache).
		 *
		 * If you use this -- and we really recommend that you do, especially
		 * if 'unique' is true -- then you must use a cache backend that
		 * supports tags, i.e. not 'file' or 'database'.
		 */
		'use_cache' => false,

		/**
		 * Decode url only usefull if you want to support high unicode characters in url
		 */
		'url_decode' => false,
	];

	protected function needsSlugging() {
		$save_to = $this->sluggable['save_to'];
		$on_update = $this->sluggable['on_update'];
        $newRecord = $this->getIsNewRecord();

		if (empty($this->{$save_to})) {
			return true;
		}

		if ($this->hasChanged($save_to)) {
			return false;
		}

		return ($newRecord || $on_update);
	}

	protected function getSlugSource() {
		$from = $this->sluggable['build_from'];

		if (is_null($from)) {
			return $this->__toString();
		}

		$source = array_map(
			function ($attribute) {
				return $this->{$attribute};
			},
			(array) $from
		);

		return join($source, ' ');
	}

	protected function generateSlug($source) {
		$separator = $this->sluggable['separator'];
		$method = $this->sluggable['method'];
		$max_length = $this->sluggable['max_length'];

		if ($method === null) {
            /**
             * for russian letters
             */
			//$slug = $this->Str($source, $separator);
            $slug = \utilphp\util::slugify($source, $separator);
		} elseif (is_callable($method)) {
			$slug = call_user_func($method, $source, $separator);
		} else {
			throw new \UnexpectedValueException("Sluggable method is not callable or null.");
		}

		if (is_string($slug) && $max_length) {
			$slug = substr($slug, 0, $max_length);
		}

		return $slug;
	}

	protected function validateSlug($slug) {

		$reserved = $this->sluggable['reserved'];

		if ($reserved === null) {
			return $slug;
		}

		// check for reserved names
		if ($reserved instanceof \Closure) {
			$reserved = $reserved($this);
		}

		if (is_array($reserved)) {
			if (in_array($slug, $reserved)) {
				return $slug . $this->sluggable['separator'] . '1';
			}
			return $slug;
		}

		throw new \UnexpectedValueException("Sluggable reserved is not null, an array, or a closure that returns null/array.");

	}

	protected function makeSlugUnique($slug) {
		if (!$this->sluggable['unique']) {
			return $slug;
		}

		$separator = $this->sluggable['separator'];
		$use_cache = $this->sluggable['use_cache'];
		$save_to = $this->sluggable['save_to'];

		// if using the cache, check if we have an entry already instead
		// of querying the database
		if ($use_cache) {
			$increment = \Cache::tags('sluggable')->get($slug);
			if ($increment === null) {
				\Cache::tags('sluggable')->put($slug, 0, $use_cache);
			} else {
				\Cache::tags('sluggable')->put($slug, ++$increment, $use_cache);
				$slug .= $separator . $increment;
			}
			return $slug;
		}

		// no cache, so we need to check directly
		// find all models where the slug is like the current one
		$list = $this->getExistingSlugs($slug);

		// if ...
		// 	a) the list is empty
		// 	b) our slug isn't in the list
		// 	c) our slug is in the list and it's for our model
		// ... we are okay
		if (
			count($list) === 0 ||
			!in_array($slug, $list) ||
			(array_key_exists($this->getKey(), $list) && $list[$this->getKey()] === $slug)
		) {
			return $slug;
		}

		// map our list to keep only the increments
		$len = strlen($slug . $separator);
		array_walk($list, function (&$value, $key) use ($len) {
			$value = intval(substr($value, $len));
		});

		// find the highest increment
		rsort($list);
		$increment = reset($list) + 1;

		return $slug . $separator . $increment;

	}

	protected function getExistingSlugs($slug) {
		$save_to = $this->sluggable['save_to'];
		$include_trashed = $this->sluggable['include_trashed'];

		$instance = new static;

		$query = $instance->where($save_to, 'LIKE', $slug . '%');

		// include trashed models if required
		if ($include_trashed && $instance->usesSoftDeleting()) {
			$query = $query->withTrashed();
		}

		// get a list of all matching slugs
		$list = $query->lists($save_to, $this->getKeyName());

		return $list;
	}

	protected function usesSoftDeleting() {
		if (in_array('Illuminate\Database\Eloquent\SoftDeletingTrait', class_uses($this))) {
			return true;
		}
		return (property_exists($this, 'softDelete') && $this->softDelete == true);
	}

	protected function setSlug($slug) {
		$save_to = $this->sluggable['save_to'];
		$this->setAttribute($save_to, $slug);
	}

	public function getSlug() {
		$save_to = $this->sluggable['save_to'];
		return $this->getAttribute($save_to);
	}

	public function sluggify($force = false) {
		$this->sluggable = array_merge($this->defaultOptions, $this->sluggable);

		if ($force || $this->needsSlugging()) {

			$source = $this->getSlugSource();
			$slug = $this->generateSlug($source);

			$slug = $this->validateSlug($slug);
			$slug = $this->makeSlugUnique($slug);

			$this->setSlug($slug);
		}

		return $this;
	}

	public function resluggify() {
		return $this->sluggify(true);
	}

	public static function getBySlug($slug) {

		$instance = new static;

		$config = \App::make('config')->get('eloquent-sluggable::config');
		$config = array_merge($config, $instance->sluggable);

		return $instance->where($config['save_to'], $slug)->get();
	}

	public static function findBySlug($slug) {

		return static::getBySlug($slug)->first();
	}

	/**
	 * Sanitizes title, replacing whitespace with dashes.
	 *
	 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
	 * Whitespace becomes a dash.
	 *
	 * @param string $title The title to be sanitized.
	 * @param string $separator Slug seperator
	 * @return string The sanitized title.
	 */
	public function Str($title, $separator = '-') {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

		$title = $this->remove_accents($title);
		if ($this->seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = self::utf8_uri_encode($title, 200);
		}

		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', $separator, $title);
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', $separator, $title);
		$title = preg_replace('|-+|', $separator, $title);
		$title = trim($title, $separator);

		if ($this->sluggable['url_decode']) {
			$title = urldecode($title);
		}

		return $title;
	}

	/**
	 * Checks to see if a string is utf8 encoded.
	 *
	 * NOTE: This function checks for 5-Byte sequences, UTF8
	 *       has Bytes Sequences with a maximum length of 4.
	 *
	 * @author bmorel at ssi dot fr (modified)
	 *
	 * @param string $str The string to be checked
	 * @return bool True if $str fits a UTF-8 model, false otherwise.
	 */
	private function seems_utf8($str) {
		$length = strlen($str);
		for ($i = 0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) {
				$n = 0;
			}
			# 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) {
				$n = 1;
			}
			# 110bbbbb
			elseif (($c & 0xF0) == 0xE0) {
				$n = 2;
			}
			# 1110bbbb
			elseif (($c & 0xF8) == 0xF0) {
				$n = 3;
			}
			# 11110bbb
			elseif (($c & 0xFC) == 0xF8) {
				$n = 4;
			}
			# 111110bb
			elseif (($c & 0xFE) == 0xFC) {
				$n = 5;
			}
			# 1111110b
			else {
				return false;
			}
			# Does not match any model
			for ($j = 0; $j < $n; $j++) {
				# n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
					return false;
				}

			}
		}
		return true;
	}

	/**
	 * Encode the Unicode values to be used in the URI.
	 *
	 * @param string $utf8_string
	 * @param int $length Max length of the string
	 * @return string String with Unicode encoded for URI.
	 */
	private function utf8_uri_encode($utf8_string, $length = 0) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;

		$string_length = strlen($utf8_string);
		for ($i = 0; $i < $string_length; $i++) {

			$value = ord($utf8_string[$i]);

			if ($value < 128) {
				if ($length && ($unicode_length >= $length)) {
					break;
				}

				$unicode .= chr($value);
				$unicode_length++;
			} else {
				if (count($values) == 0) {
					$num_octets = ($value < 224) ? 2 : 3;
				}

				$values[] = $value;

				if ($length && ($unicode_length + ($num_octets * 3)) > $length) {
					break;
				}

				if (count($values) == $num_octets) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}

					$values = array();
					$num_octets = 1;
				}
			}
		}

		return $unicode;
	}

	/**
	 * Converts all accent characters to ASCII characters.
	 *
	 * If there are no accent characters, then the string given is just returned.
	 *
	 * @param string $string Text that might have accent characters
	 * @return string Filtered string with replaced "nice" characters.
	 */
	private function remove_accents($string) {
		if (!preg_match('/[\x80-\xff]/', $string)) {
			return $string;
		}

		if ($this->seems_utf8($string)) {
			$chars = array(
				// Decompositions for Latin-1 Supplement
				chr(195) . chr(128) => 'A',
				chr(195) . chr(129) => 'A',
				chr(195) . chr(130) => 'A',
				chr(195) . chr(131) => 'A',
				chr(195) . chr(132) => 'A',
				chr(195) . chr(133) => 'A',
				chr(195) . chr(135) => 'C',
				chr(195) . chr(136) => 'E',
				chr(195) . chr(137) => 'E',
				chr(195) . chr(138) => 'E',
				chr(195) . chr(139) => 'E',
				chr(195) . chr(140) => 'I',
				chr(195) . chr(141) => 'I',
				chr(195) . chr(142) => 'I',
				chr(195) . chr(143) => 'I',
				chr(195) . chr(145) => 'N',
				chr(195) . chr(146) => 'O',
				chr(195) . chr(147) => 'O',
				chr(195) . chr(148) => 'O',
				chr(195) . chr(149) => 'O',
				chr(195) . chr(150) => 'O',
				chr(195) . chr(153) => 'U',
				chr(195) . chr(154) => 'U',
				chr(195) . chr(155) => 'U',
				chr(195) . chr(156) => 'U',
				chr(195) . chr(157) => 'Y',
				chr(195) . chr(159) => 's',
				chr(195) . chr(160) => 'a',
				chr(195) . chr(161) => 'a',
				chr(195) . chr(162) => 'a',
				chr(195) . chr(163) => 'a',
				chr(195) . chr(164) => 'a',
				chr(195) . chr(165) => 'a',
				chr(195) . chr(167) => 'c',
				chr(195) . chr(168) => 'e',
				chr(195) . chr(169) => 'e',
				chr(195) . chr(170) => 'e',
				chr(195) . chr(171) => 'e',
				chr(195) . chr(172) => 'i',
				chr(195) . chr(173) => 'i',
				chr(195) . chr(174) => 'i',
				chr(195) . chr(175) => 'i',
				chr(195) . chr(177) => 'n',
				chr(195) . chr(178) => 'o',
				chr(195) . chr(179) => 'o',
				chr(195) . chr(180) => 'o',
				chr(195) . chr(181) => 'o',
				chr(195) . chr(182) => 'o',
				chr(195) . chr(182) => 'o',
				chr(195) . chr(185) => 'u',
				chr(195) . chr(186) => 'u',
				chr(195) . chr(187) => 'u',
				chr(195) . chr(188) => 'u',
				chr(195) . chr(189) => 'y',
				chr(195) . chr(191) => 'y',
				// Decompositions for Latin Extended-A
				chr(196) . chr(128) => 'A',
				chr(196) . chr(129) => 'a',
				chr(196) . chr(130) => 'A',
				chr(196) . chr(131) => 'a',
				chr(196) . chr(132) => 'A',
				chr(196) . chr(133) => 'a',
				chr(196) . chr(134) => 'C',
				chr(196) . chr(135) => 'c',
				chr(196) . chr(136) => 'C',
				chr(196) . chr(137) => 'c',
				chr(196) . chr(138) => 'C',
				chr(196) . chr(139) => 'c',
				chr(196) . chr(140) => 'C',
				chr(196) . chr(141) => 'c',
				chr(196) . chr(142) => 'D',
				chr(196) . chr(143) => 'd',
				chr(196) . chr(144) => 'D',
				chr(196) . chr(145) => 'd',
				chr(196) . chr(146) => 'E',
				chr(196) . chr(147) => 'e',
				chr(196) . chr(148) => 'E',
				chr(196) . chr(149) => 'e',
				chr(196) . chr(150) => 'E',
				chr(196) . chr(151) => 'e',
				chr(196) . chr(152) => 'E',
				chr(196) . chr(153) => 'e',
				chr(196) . chr(154) => 'E',
				chr(196) . chr(155) => 'e',
				chr(196) . chr(156) => 'G',
				chr(196) . chr(157) => 'g',
				chr(196) . chr(158) => 'G',
				chr(196) . chr(159) => 'g',
				chr(196) . chr(160) => 'G',
				chr(196) . chr(161) => 'g',
				chr(196) . chr(162) => 'G',
				chr(196) . chr(163) => 'g',
				chr(196) . chr(164) => 'H',
				chr(196) . chr(165) => 'h',
				chr(196) . chr(166) => 'H',
				chr(196) . chr(167) => 'h',
				chr(196) . chr(168) => 'I',
				chr(196) . chr(169) => 'i',
				chr(196) . chr(170) => 'I',
				chr(196) . chr(171) => 'i',
				chr(196) . chr(172) => 'I',
				chr(196) . chr(173) => 'i',
				chr(196) . chr(174) => 'I',
				chr(196) . chr(175) => 'i',
				chr(196) . chr(176) => 'I',
				chr(196) . chr(177) => 'i',
				chr(196) . chr(178) => 'IJ',
				chr(196) . chr(179) => 'ij',
				chr(196) . chr(180) => 'J',
				chr(196) . chr(181) => 'j',
				chr(196) . chr(182) => 'K',
				chr(196) . chr(183) => 'k',
				chr(196) . chr(184) => 'k',
				chr(196) . chr(185) => 'L',
				chr(196) . chr(186) => 'l',
				chr(196) . chr(187) => 'L',
				chr(196) . chr(188) => 'l',
				chr(196) . chr(189) => 'L',
				chr(196) . chr(190) => 'l',
				chr(196) . chr(191) => 'L',
				chr(197) . chr(128) => 'l',
				chr(197) . chr(129) => 'L',
				chr(197) . chr(130) => 'l',
				chr(197) . chr(131) => 'N',
				chr(197) . chr(132) => 'n',
				chr(197) . chr(133) => 'N',
				chr(197) . chr(134) => 'n',
				chr(197) . chr(135) => 'N',
				chr(197) . chr(136) => 'n',
				chr(197) . chr(137) => 'N',
				chr(197) . chr(138) => 'n',
				chr(197) . chr(139) => 'N',
				chr(197) . chr(140) => 'O',
				chr(197) . chr(141) => 'o',
				chr(197) . chr(142) => 'O',
				chr(197) . chr(143) => 'o',
				chr(197) . chr(144) => 'O',
				chr(197) . chr(145) => 'o',
				chr(197) . chr(146) => 'OE',
				chr(197) . chr(147) => 'oe',
				chr(197) . chr(148) => 'R',
				chr(197) . chr(149) => 'r',
				chr(197) . chr(150) => 'R',
				chr(197) . chr(151) => 'r',
				chr(197) . chr(152) => 'R',
				chr(197) . chr(153) => 'r',
				chr(197) . chr(154) => 'S',
				chr(197) . chr(155) => 's',
				chr(197) . chr(156) => 'S',
				chr(197) . chr(157) => 's',
				chr(197) . chr(158) => 'S',
				chr(197) . chr(159) => 's',
				chr(197) . chr(160) => 'S',
				chr(197) . chr(161) => 's',
				chr(197) . chr(162) => 'T',
				chr(197) . chr(163) => 't',
				chr(197) . chr(164) => 'T',
				chr(197) . chr(165) => 't',
				chr(197) . chr(166) => 'T',
				chr(197) . chr(167) => 't',
				chr(197) . chr(168) => 'U',
				chr(197) . chr(169) => 'u',
				chr(197) . chr(170) => 'U',
				chr(197) . chr(171) => 'u',
				chr(197) . chr(172) => 'U',
				chr(197) . chr(173) => 'u',
				chr(197) . chr(174) => 'U',
				chr(197) . chr(175) => 'u',
				chr(197) . chr(176) => 'U',
				chr(197) . chr(177) => 'u',
				chr(197) . chr(178) => 'U',
				chr(197) . chr(179) => 'u',
				chr(197) . chr(180) => 'W',
				chr(197) . chr(181) => 'w',
				chr(197) . chr(182) => 'Y',
				chr(197) . chr(183) => 'y',
				chr(197) . chr(184) => 'Y',
				chr(197) . chr(185) => 'Z',
				chr(197) . chr(186) => 'z',
				chr(197) . chr(187) => 'Z',
				chr(197) . chr(188) => 'z',
				chr(197) . chr(189) => 'Z',
				chr(197) . chr(190) => 'z',
				chr(197) . chr(191) => 's',
				// Euro Sign
				chr(226) . chr(130) . chr(172) => 'E',
				// GBP (Pound) Sign
				chr(194) . chr(163) => '');

			$string = strtr($string, $chars);
		} else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158) . chr(159) . chr(162) . chr(165) .
			chr(181) . chr(192) . chr(193) . chr(194) . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202) .
			chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210) . chr(211) . chr(212) . chr(213) . chr(214) .
			chr(216) . chr(217) . chr(218) . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227) . chr(228) .
			chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235) . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) .
			chr(242) . chr(243) . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251) . chr(252) . chr(253) .
			chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(
				chr(140),
				chr(156),
				chr(198),
				chr(208),
				chr(222),
				chr(223),
				chr(230),
				chr(240),
				chr(254));
			$double_chars['out'] = array(
				'OE',
				'oe',
				'AE',
				'DH',
				'TH',
				'ss',
				'ae',
				'dh',
				'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}

		return $string;
	}

    public function getIsNewRecord()
    {
        return $this->getDirtyState() == \Phalcon\Mvc\Model::DIRTY_STATE_TRANSIENT;
    }

}
