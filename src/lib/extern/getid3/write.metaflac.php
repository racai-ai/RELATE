<?php

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at https://github.com/JamesHeinrich/getID3       //
//            or https://www.getid3.org                        //
//            or http://getid3.sourceforge.net                 //
//  see readme.txt for more details                            //
/////////////////////////////////////////////////////////////////
//                                                             //
// write.metaflac.php                                          //
// module for writing metaflac tags                            //
// dependencies: /helperapps/metaflac.exe                      //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_write_metaflac
{
	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var array
	 */
	public $tag_data;

	/**
	 * Any non-critical errors will be stored here.
	 *
	 * @var array
	 */
	public $warnings = array();

	/**
	 * Any critical errors will be stored here.
	 *
	 * @var array
	 */
	public $errors   = array();

	private $pictures = array();

	public function __construct() {
	}

	/**
	 * @return bool
	 */
	public function WriteMetaFLAC() {

		if (preg_match('#(1|ON)#i', ini_get('safe_mode'))) {
			$this->errors[] = 'PHP running in Safe Mode (backtick operator not available) - cannot call metaflac, tags not written';
			return false;
		}

		$tempfilenames = array();


		if (!empty($this->tag_data['ATTACHED_PICTURE'])) {
			foreach ($this->tag_data['ATTACHED_PICTURE'] as $key => $picturedetails) {
				$temppicturefilename = tempnam(GETID3_TEMP_DIR, 'getID3');
				$tempfilenames[] = $temppicturefilename;
				if (getID3::is_writable($temppicturefilename) && is_file($temppicturefilename) && ($fpcomments = fopen($temppicturefilename, 'wb'))) {
					// https://xiph.org/flac/documentation_tools_flac.html#flac_options_picture
					// [TYPE]|[MIME-TYPE]|[DESCRIPTION]|[WIDTHxHEIGHTxDEPTH[/COLORS]]|FILE
					fwrite($fpcomments, $picturedetails['data']);
					fclose($fpcomments);
					$picture_typeid = (!empty($picturedetails['picturetypeid']) ? $this->ID3v2toFLACpictureTypes($picturedetails['picturetypeid']) : 3); // default to "3:Cover (front)"
					$picture_mimetype = (!empty($picturedetails['mime']) ? $picturedetails['mime'] : ''); // should be auto-detected
					$picture_width_height_depth = '';
					$this->pictures[] = $picture_typeid.'|'.$picture_mimetype.'|'.preg_replace('#[^\x20-\x7B\x7D-\x7F]#', '', (string) $picturedetails['description']).'|'.$picture_width_height_depth.'|'.$temppicturefilename;
				} else {
					$this->errors[] = 'failed to open temporary tags file, tags not written - fopen("'.$temppicturefilename.'", "wb")';
					return false;
				}
			}
			unset($this->tag_data['ATTACHED_PICTURE']);
		}


		// Create file with new comments
		$tempcommentsfilename = tempnam(GETID3_TEMP_DIR, 'getID3');
		$tempfilenames[] = $tempcommentsfilename;
		if (getID3::is_writable($tempcommentsfilename) && is_file($tempcommentsfilename) && ($fpcomments = fopen($tempcommentsfilename, 'wb'))) {
			foreach ($this->tag_data as $key => $value) {
				foreach ($value as $commentdata) {
					fwrite($fpcomments, $this->CleanmetaflacName($key).'='.$commentdata."\n");
				}
			}
			fclose($fpcomments);

		} else {
			$this->errors[] = 'failed to open temporary tags file, tags not written - fopen("'.$tempcommentsfilename.'", "wb")';
			return false;
		}

		$oldignoreuserabort = ignore_user_abort(true);
		if (GETID3_OS_ISWINDOWS) {

			if (file_exists(GETID3_HELPERAPPSDIR.'metaflac.exe')) {
				//$commandline = '"'.GETID3_HELPERAPPSDIR.'metaflac.exe" --no-utf8-convert --remove-all-tags --import-tags-from="'.$tempcommentsfilename.'" "'.str_replace('/', '\\', $this->filename).'"';
				//  metaflac works fine if you copy-paste the above commandline into a command prompt,
				//  but refuses to work with `backtick` if there are "doublequotes" present around BOTH
				//  the metaflac pathname and the target filename. For whatever reason...??
				//  The solution is simply ensure that the metaflac pathname has no spaces,
				//  and therefore does not need to be quoted

				// On top of that, if error messages are not always captured properly under Windows
				// To at least see if there was a problem, compare file modification timestamps before and after writing
				clearstatcache(true, $this->filename);
				$timestampbeforewriting = filemtime($this->filename);

				$commandline  = GETID3_HELPERAPPSDIR.'metaflac.exe --no-utf8-convert --remove-all-tags --import-tags-from='.escapeshellarg($tempcommentsfilename);
				foreach ($this->pictures as $picturecommand) {
					$commandline .= ' --import-picture-from='.escapeshellarg($picturecommand);
				}
				$commandline .= ' '.escapeshellarg($this->filename).' 2>&1';
				$metaflacError = `$commandline`;

				if (empty($metaflacError)) {
					clearstatcache(true, $this->filename);
					if ($timestampbeforewriting == filemtime($this->filename)) {
						$metaflacError = 'File modification timestamp has not changed - it looks like the tags were not written';
					}
				}
			} else {
				$metaflacError = 'metaflac.exe not found in '.GETID3_HELPERAPPSDIR;
			}

		} else {

			// It's simpler on *nix
			$commandline  = 'metaflac --no-utf8-convert --remove-all-tags --import-tags-from='.escapeshellarg($tempcommentsfilename);
			foreach ($this->pictures as $picturecommand) {
				$commandline .= ' --import-picture-from='.escapeshellarg($picturecommand);
			}
			$commandline .= ' '.escapeshellarg($this->filename).' 2>&1';
			$metaflacError = `$commandline`;

		}

		// Remove temporary comments file
		foreach ($tempfilenames as $tempfilename) {
			unlink($tempfilename);
		}
		ignore_user_abort($oldignoreuserabort);

		if (!empty($metaflacError)) {

			$this->errors[] = 'System call to metaflac failed with this message returned: '."\n\n".$metaflacError;
			return false;

		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function DeleteMetaFLAC() {

		if (preg_match('#(1|ON)#i', ini_get('safe_mode'))) {
			$this->errors[] = 'PHP running in Safe Mode (backtick operator not available) - cannot call metaflac, tags not deleted';
			return false;
		}

		$oldignoreuserabort = ignore_user_abort(true);
		if (GETID3_OS_ISWINDOWS) {

			if (file_exists(GETID3_HELPERAPPSDIR.'metaflac.exe')) {
				// To at least see if there was a problem, compare file modification timestamps before and after writing
				clearstatcache(true, $this->filename);
				$timestampbeforewriting = filemtime($this->filename);

				$commandline = GETID3_HELPERAPPSDIR.'metaflac.exe --remove-all-tags "'.$this->filename.'" 2>&1';
				$metaflacError = `$commandline`;

				if (empty($metaflacError)) {
					clearstatcache(true, $this->filename);
					if ($timestampbeforewriting == filemtime($this->filename)) {
						$metaflacError = 'File modification timestamp has not changed - it looks like the tags were not deleted';
					}
				}
			} else {
				$metaflacError = 'metaflac.exe not found in '.GETID3_HELPERAPPSDIR;
			}

		} else {

			// It's simpler on *nix
			$commandline = 'metaflac --remove-all-tags "'.$this->filename.'" 2>&1';
			$metaflacError = `$commandline`;

		}

		ignore_user_abort($oldignoreuserabort);

		if (!empty($metaflacError)) {
			$this->errors[] = 'System call to metaflac failed with this message returned: '."\n\n".$metaflacError;
			return false;
		}
		return true;
	}

	/**
	 * @param int $id3v2_picture_typeid
	 *
	 * @return int
	 */
	public function ID3v2toFLACpictureTypes($id3v2_picture_typeid) {
		// METAFLAC picture type list is identical to ID3v2 picture type list (as least up to 0x14 "Publisher/Studio logotype")
		// http://id3.org/id3v2.4.0-frames (section 4.14)
		// https://xiph.org/flac/documentation_tools_flac.html#flac_options_picture
		//return (isset($ID3v2toFLACpictureTypes[$id3v2_picture_typeid]) ? $ID3v2toFLACpictureTypes[$id3v2_picture_typeid] : 3); // default: "3: Cover (front)"
		return (($id3v2_picture_typeid <= 0x14) ? $id3v2_picture_typeid : 3); // default: "3: Cover (front)"
	}

	/**
	 * @param string $originalcommentname
	 *
	 * @return string
	 */
	public function CleanmetaflacName($originalcommentname) {
		// A case-insensitive field name that may consist of ASCII 0x20 through 0x7D, 0x3D ('=') excluded.
		// ASCII 0x41 through 0x5A inclusive (A-Z) is to be considered equivalent to ASCII 0x61 through
		// 0x7A inclusive (a-z).

		// replace invalid chars with a space, return uppercase text
		// Thanks Chris Bolt <chris-getid3Øbolt*cx> for improving this function
		// note: *reg_replace() replaces nulls with empty string (not space)
		return strtoupper(preg_replace('#[^ -<>-}]#', ' ', str_replace("\x00", ' ', $originalcommentname)));
	}

}
