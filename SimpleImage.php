<?php

define('THUMBNAIL_IMAGE_MAX_WIDTH', 58);
define('THUMBNAIL_IMAGE_MAX_HEIGHT', 85);

class SimpleImage {

	public $thumbnailMaxWidth = 58;
	public $thumbnailMaxHeight = 85;

	private $sourceGdImage = null;
	private $sourcePath = '';

	private $sourceImageWidth = 0;
	private $sourceImageHeight = 0;
	private $sourceImageType = '';

	private $otherSimpleImages = array();

	public function __construct($source_image_path) {
		$this->sourcePath = $source_image_path;
		list($this->sourceImageWidth, $this->sourceImageHeight, $this->sourceImageType) = getimagesize($source_image_path);
	}

	public function getHeight() {
		return $this->sourceImageHeight;
	}

	public function getWidth() {
		return $this->sourceImageWidth;
	}

	public function getSourceGdImage() {
		return $this->sourceGdImage;
	}

	public function loadSourceIntoGD() {
		switch ($this->sourceImageType) {
	        case IMAGETYPE_GIF:
	            $this->sourceGdImage = imagecreatefromgif($this->sourcePath);
	            break;
	        case IMAGETYPE_JPEG:
	            $this->sourceGdImage = imagecreatefromjpeg($this->sourcePath);
	            break;
	        case IMAGETYPE_PNG:
	            $this->sourceGdImage = imagecreatefrompng($this->sourcePath);
	            break;
	    }
	    return $this->sourceGdImage;
	}

	public function makeCopy($path) {
		$source = $this->loadSourceIntoGD();

		$product = imagecreatetruecolor($this->sourceImageWidth, $this->sourceImageHeight);

		imagecopy($product, $source, 0, 0, 0, 0, $this->sourceImageWidth, $this->sourceImageHeight);
		
		imagejpeg($product, $path, 90);

		imagedestroy($product);
		imagedestroy($source);

		return new SimpleImage($path);
	}

	public function generateThumbnail ($thumbnail_image_path) {
		// load source gd
		$this->loadSourceIntoGD();

		if ($this->sourceGdImage === false) {
			return false;
		}

		// get source aspect ratio
		// and thumbnail aspect ratio
		$source_aspect_ratio = $this->getAspectRatio();
		$thumbnail_aspect_ratio = $this->getThumbNailAspectRatio();

		// if source smaller than thumb just use source
		if ($this->sourceImageWidth <= $this->thumbnailMaxWidth && $this->sourceImageHeight <= $this->thumbnailMaxHeight) {
			$thumbnail_image_width = $this->sourceImageWidth;
			$thumbnail_image_height = $this->sourceImageHeight;
		} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
			// if thumb aspect ratio larger than source aspect ratio
			// then we maintain maxheight and take a fraction of the width
			$thumbnail_image_width = (int) ($this->thumbnailMaxHeight * $source_aspect_ratio);
			$thumbnail_image_height = $this->thumbnailMaxHeight;
		} else {
			// if thumb aspect ratio less than source aspect ratio
			// then we maintain maxwidth and take a fraction of the height
			$thumbnail_image_width = $this->thumbnailMaxWidth;
			$thumbnail_image_height = (int) ($this->thumbnailMaxWidth / $source_aspect_ratio);
		}

		// create the gd image of thumb based on new thumb width, height
		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);

		// copy from source gd and fill inside thumb gd
		imagecopyresampled($thumbnail_gd_image, $this->sourceGdImage, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $this->sourceImageWidth, $this->sourceImageHeight);
		// create the image file from the gd 
		imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);

		imagedestroy($thumbnail_gd_image);
		return true;
	}

/**
 * take a given image and produce a square thumbnail while filling the background with white color
 */
	public function generateSquareThumbnail ($thumbnail_image_path) {
		// load source gd
		$this->loadSourceIntoGD();

		if ($this->sourceGdImage === false) {
			return false;
		}

		// get source aspect ratio
		// and thumbnail aspect ratio
		$source_aspect_ratio	= $this->getAspectRatio();
		$thumbnail_aspect_ratio	= $this->getThumbNailAspectRatio();

		// we will not do for smaller than thumbnail max width and max height
		if ($this->sourceImageWidth <= $this->thumbnailMaxWidth && $this->sourceImageHeight <= $this->thumbnailMaxHeight) {
			return false;
		} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
			// if thumb aspect ratio larger than source aspect ratio
			// then we maintain maxheight and take a fraction of the width
			$thumbnail_image_width = (int) ($this->thumbnailMaxHeight * $source_aspect_ratio);
			$thumbnail_image_height = $this->thumbnailMaxHeight;
		} else {
			// if thumb aspect ratio less than source aspect ratio
			// then we maintain maxwidth and take a fraction of the height
			$thumbnail_image_width = $this->thumbnailMaxWidth;
			$thumbnail_image_height = (int) ($this->thumbnailMaxWidth / $source_aspect_ratio);
		}


		// create a white background image first of max width and max height
		$background = imagecreatetruecolor($this->thumbnailMaxWidth,$this->thumbnailMaxHeight);
		$whiteBackground = imagecolorallocate($background, 255, 255, 255);
		imagefill($background,0,0,$whiteBackground);

		// // create the gd image of thumb based on new thumb width, height
		// $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);

		// copy from source gd and fill inside thumb gd
		imagecopyresampled($background, $this->sourceGdImage, ($this->thumbnailMaxWidth-$thumbnail_image_width)/2, ($this->thumbnailMaxHeight-$thumbnail_image_height)/2, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $this->sourceImageWidth, $this->sourceImageHeight);
		// create the image file from the gd 
		imagejpeg($background, $thumbnail_image_path, 90);

		imagedestroy($background);
		return true;
	}

	public function getAspectRatio() {
		if ($this->sourceImageHeight > 0) {
			return ($this->sourceImageWidth / $this->sourceImageHeight);
		} else {
			return 0;
		}
	}

	public function getThumbNailAspectRatio() {
		if ($this->thumbnailMaxHeight > 0) {
			return ($this->thumbnailMaxWidth / $this->thumbnailMaxHeight);
		} else {
			return 0;
		}
	}

	public function rightAppend($destPath) {
		$otherImage = new SimpleImage($destPath);

		$right = $otherImage->loadSourceIntoGD();
		$source = $this->loadSourceIntoGD();

		$product = imagecreatetruecolor($otherImage->getWidth() + $this->sourceImageWidth, $this->sourceImageHeight);

		imagecopy($product, $source, 0, 0, 0, 0, $this->sourceImageWidth, $this->sourceImageHeight);

		imagecopy($product, $right, $this->sourceImageWidth, 0, 0, 0, $otherImage->getWidth(), $otherImage->getHeight());
		
		imagejpeg($product, $this->sourcePath, 90);

		imagedestroy($product);
		imagedestroy($source);
		imagedestroy($right);

		unset($otherImage);
		
		return true;
	}

	public function downAppend($destPath) {
		$otherImage = new SimpleImage($destPath);

		$down = $otherImage->loadSourceIntoGD();
		$source = $this->loadSourceIntoGD();

		$product = imagecreatetruecolor($this->sourceImageWidth, $this->sourceImageHeight + $otherImage->getHeight());

		imagecopy($product, $source, 0, 0, 0, 0, $this->sourceImageWidth, $this->sourceImageHeight);

		imagecopy($product, $down, 0, $this->sourceImageHeight, 0, 0, $otherImage->getWidth(), $otherImage->getHeight());
		
		imagejpeg($product, $this->sourcePath, 90);

		imagedestroy($product);
		imagedestroy($source);
		imagedestroy($down);

		unset($otherImage);

		return true;
	}	
}