<?php

/**
 * Class for device adaptive images (for ‘retina’ or high DPI type screens)
 */
class RetinaImage extends Image {

	protected $adaptiveimages = array();
	public static $forceretina = false;

	/**
	 * Generates the image if not already there.
	 * 
	 * @return string
	 */
	public function getTag() {
		// we generate an image to the same width all the time, so we get the
		// generated images.
		// native res - down size by 2x.
		if (strstr($this->Filename, '_resampled') === false) {
			$this->SetWidth($this->getWidth());
		} else {
			// prevously resampled.
			$this->SetWidth($this->getWidth());
		}

		$url = parent::getURL();

		$normalsizeurl = $url;
		$title = ($this->Title) ? $this->Title : $this->Filename;
		if ($this->Title) {
			$title = Convert::raw2att($this->Title);
		} else {
			if (preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) {
				$title = Convert::raw2att($matches[1]);
			}
		}
		$srcset = "";
		foreach ($this->adaptiveimages as $ratio => $filename) {
			if ($srcset != "") {
				$srcset .= ', ';
			}
			$srcset .= $filename . ' ' . $ratio . 'x';
			if ($ratio == '1.0') {
				$normalsizeurl = $filename;
			}
		}
		return "<img src=\"$normalsizeurl\" alt=\"$title\" srcset=\"$srcset\" />";
	}

	/**
	 * 
	 * @param type $width
	 * @param type $height
	 * @return \RetinaImage
	 */
	public function getThumbnail($width, $height) {
		$this->generateFormattedImage('SetRatioSize', $width, $height);
		return $this;
	}

	/**
	 * we check if the file exists on getFormattedImage(). May incur a 
	 * performance hit, but during testing it seems O.K.
	 * @param int $width
	 * @return boolean false
	 */
	public function isWidth($width) {
		return false;
	}

	/**
	 * returns the file name with extra text appended
	 * i.e. image.jpg -> image-1x.jpg
	 * @param string $filename file name to add into
	 * @param string $appendation text to insert 
	 * @return string text with the string inserted
	 */
	public function insertFilenameAppender($filename, $appendation) {
		$ext = $this->getExtension();
		if ($ext != 'unknown') {
			$outstr = substr($filename, 0, -(strlen($ext) + 1));
			$outstr .= $appendation;
			$outstr .= '.' . $ext;
			return $outstr;
		}
		return $this->FileName;
	}

	/**
	 * removes the appendation from the name
	 * @param type $filename
	 * @param type $appendation
	 * @return string
	 */
	public static function removeFilenameAppender($filename, $appendation) {
		$ext = File::get_file_extension($filename);
		if ($ext != 'unknown') {
			$outstr = substr($filename, 0, -(strlen($ext) + 1));
			$outstr = substr($outstr, 0, -(strlen($appendation)));
			$outstr .= '.' . $ext;
			return $outstr;
		}
		return $filename;
	}

	/**
	 * similar to as Image::getFormattedImage, but uses create() function
	 * @param type $format
	 * @return RetinaImage_Cached
	 */
	public function getFormattedImage($format) {
		$args = func_get_args();

		if ($this->ID && $this->Filename && Director::fileExists($this->Filename)) {
			$cacheFile = call_user_func_array(array($this, "cacheFilename"), $args);
			$filename = Director::baseFolder() . "/" . $cacheFile;
			$filename = $this->insertFilenameAppender($filename, '-10x');
			if (!file_exists($filename) || isset($_GET['flush'])) {
				call_user_func_array(array($this, "generateFormattedImage"), $args);
			} else {
				$this->adaptiveimages['1.0'] = $this->insertFilenameAppender($cacheFile, '-10x');
				$this->adaptiveimages['1.5'] = $this->insertFilenameAppender($cacheFile, '-15x');
				$this->adaptiveimages['2.0'] = $this->insertFilenameAppender($cacheFile, '-20x');
			}

			$cached = RetinaImage_Cached::create($cacheFile);
			// Pass through the title so the templates can use it
			$cached->Title = $this->Title;
			// Pass through the parent, to store cached images in correct folder.
			$cached->ParentID = $this->ParentID;

			return $cached;
		}
	}

	/**
	 * iterative version of Image::generateFormattedImage
	 * @see Image::generateFormattedImage
	 * @param string $format function string
	 */
	public function generateFormattedImage($format) {
		$args = func_get_args();

		// list of independent resoultions to generate
		$cacheFile = call_user_func_array(array($this, "cacheFilename"), $args);

		$DPIlist = array(
			'1.0' => $this->insertFilenameAppender($cacheFile, '-10x'),
			'1.5' => $this->insertFilenameAppender($cacheFile, '-15x'),
			'2.0' => $this->insertFilenameAppender($cacheFile, '-20x'),
		);
		$gdbackend = Image::get_backend();
		$defaultQuality = config::inst()->get('RetinaImage', 'defaultQuality');

		// degrade the quality of the image as the dimensions increase
		$qualityDegrade = config::inst()->get('RetinaImage', 'qualityDegrade');
		$qualityCap = config::inst()->get('RetinaImage', 'qualityCap');

		// iterate through each resoultion to generate
		foreach ($DPIlist as $multiplier => $filename) {
			$backend = Injector::inst()->createWithArgs($gdbackend, array(
				Director::baseFolder() . "/" . $this->Filename
			));

			if ($backend->hasImageResource()) {
				$generateFunc = "generate$format";
				if ($this->hasMethod($generateFunc)) {
					array_shift($args);
					array_unshift($args, $backend);

					$modifiedargs = $args;
					// most formatted images have width 
					if (isset($args[1]) && isset($args[2]) &&
							is_numeric($args[1]) && is_numeric($args[2])) {
						$modifiedargs[1] = (int) ($args[1] * $multiplier);
						$modifiedargs[2] = (int) ($args[2] * $multiplier);
						// for SetHeight, SetWidth
					} else if (isset($args[1]) && is_numeric($args[1])) {
						$modifiedargs[1] = (int) ($args[1] * $multiplier);
					}

					// degrade the quality as the higher images progress
					$quality = $defaultQuality - (($multiplier - 1) * $qualityDegrade);
					if ($quality < $qualityCap) {
						$quality = $qualityCap;
					}
					$backend->setQuality($quality);
					$backend = call_user_func_array(array($this, $generateFunc), $modifiedargs);
					if ($backend) {
						$backend->writeTo(Director::baseFolder() . "/" . $filename);
					}
					$this->adaptiveimages [$multiplier] = $filename;
				} else {
					user_error("Image::generateFormattedImage - Image $format public function not found.", E_USER_WARNING);
				}
			}
		}
	}

	/**
	 * return a 1x image url if resampled
	 * @return string
	 */
	public function getURL() {
		$url = parent::getURL();
		
		if (strstr($url, '_resampled') === false) {
			return $url;
		}
		return $this->insertFilenameAppender($url, '-10x');
	}

}

class RetinaImage_Cached extends RetinaImage {

	/**
	 * Create a new cached image.
	 * @param string $filename The filename of the image.
	 * @param boolean $isSingleton This this to true if this is a singleton() object, a stub for calling methods.
	 *                             Singletons don't have their defaults set.
	 */
	public function __construct($filename = null, $isSingleton = false) {
		parent::__construct(array(), $isSingleton);
		$this->ID = -1;
		$this->Filename = $filename;
	}

	public function getRelativePath() {
		return $this->getField('Filename');
	}

	/**
	 * Prevent creating new tables for the cached record
	 *
	 * @return false
	 */
	public function requireTable() {
		return false;
	}

	/**
	 * Prevent writing the cached image to the database
	 *
	 * @throws Exception
	 */
	public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false) {
		throw new Exception("{$this->ClassName} can not be written back to the database.");
	}

	/**
	 * Return an XHTML img tag for this Image,
	 * or NULL if the image file doesn't exist on the filesystem.
	 * 
	 * @return string
	 */
	public function getTag() {
		$url = Image::getURL();
		$title = ($this->Title) ? $this->Title : $this->Filename;
		if ($this->Title) {
			$title = Convert::raw2att($this->Title);
		} else {
			if (preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) {
				$title = Convert::raw2att($matches[1]);
			}
		}
		$onex10 = $this->insertFilenameAppender($url, '-10x');
		$onex15 = $this->insertFilenameAppender($url, '-15x');
		$onex20 = $this->insertFilenameAppender($url, '-20x');

		// try to get the 10x file size
		$filename = urldecode(Director::makeRelative($onex10));
		$imagefile = Director::baseFolder() . '/' . $filename;
		$size = getimagesize($imagefile);

		return "<img src=\"$onex10\" alt=\"$title\" width=\"$size[0]\" height=\"$size[1]\" srcset=\"$onex10 1x, $onex15 1.5x, $onex20 2x\" />";
	}
}

class RetinaImageHtmlEditorField extends HtmlEditorField {

	/**
	 * @see TextareaField::__construct()
	 */
	public function __construct($name, $title = null, $value = '') {
		parent::__construct($name, $title, $value);

		$this->extraClasses [] = 'htmleditor';

		self::include_js();
	}

	public function saveInto(DataObjectInterface $record) {
		if ($record->hasField($this->name) && $record->escapeTypeForField($this->name) != 'xml') {
			throw new Exception(
			'HtmlEditorField->saveInto(): This field should save into a HTMLText or HTMLVarchar field.'
			);
		}

		$htmlValue = Injector::inst()->create('HTMLValue', $this->value);

		// Sanitise if requested
		if ($this->config()->sanitise_server_side) {
			$santiser = Injector::inst()->create('HtmlEditorSanitiser', HtmlEditorConfig::get_active());
			$santiser->sanitise($htmlValue);
		}

		// Resample images and add default attributes
		if ($images = $htmlValue->getElementsByTagName('img'))
			foreach ($images as $img) {
				// strip any ?r=n data from the src attribute
				$img->setAttribute('src', preg_replace('/([^\?]*)\?r=[0-9]+$/i', '$1', $img->getAttribute('src')));

				// Resample the images if the width & height have changed.
				// TODO: look for -10x here?
				$filename = RetinaImage::removeFilenameAppender(urldecode(Director::makeRelative($img->getAttribute('src'))), '-10x');
				$image = File::find($filename);

				// try to find it using the legacy way
				if (!$image) {
					$image = File::find(urldecode(Director::makeRelative($img->getAttribute('src'))));
				}

				if ($image) {
					$imagemap = $image->toMap();
					$retinaimage = RetinaImage::create();
					foreach ($imagemap as $key => $value) {
						$retinaimage->$key = $value;
					}
					$width = $img->getAttribute('width');
					$height = $img->getAttribute('height');

					if ($width && $height && ($width != $retinaimage->getWidth() || $height != $retinaimage->getHeight()) || (!$img->hasAttribute('srcset') && RetinaImage::$forceretina)) {
						//Make sure that the resized image actually returns an image:
						if (!is_numeric($width) || !is_numeric($height)) {
							$width = (int) ($retinaimage->getWidth() / 2);
							$height = (int) ($retinaimage->getHeight() / 2);
						}
						$resized = $retinaimage->ResizedImage($width, $height);
						$url = $resized->getRelativePath();

						$onex10 = $retinaimage->insertFilenameAppender($url, '-10x');
						$onex15 = $retinaimage->insertFilenameAppender($url, '-15x');
						$onex20 = $retinaimage->insertFilenameAppender($url, '-20x');

						if ($resized)
							$img->setAttribute('src', $onex10);
						// srcset=\"$onex10 1x, $onex15 1.5x, $onex20 2x\"
						$img->setAttribute('srcset', "$onex10 1x, $onex15 1.5x, $onex20 2x");
					}
				}

				// Add default empty title & alt attributes.
				if (!$img->getAttribute('alt'))
					$img->setAttribute('alt', '');
				if (!$img->getAttribute('title'))
					$img->setAttribute('title', '');
			}

		// Store into record
		$record->{$this->name} = $htmlValue->getContent();
	}

}
