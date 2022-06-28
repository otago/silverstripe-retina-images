<?php

/**
 * resize the images for retina capable devices
 * @see https://docs.silverstripe.org/en/4/developer_guides/files/images/
 */

namespace OP;

use SilverStripe\Core\Extension;
use SilverStripe\View\HTML;

class RetinaImageExtension extends Extension
{

    private static $casting = [
        'RetinaScaleWidth' => 'HTMLText',
        'RetinaScaleMaxWidth' => 'HTMLText',
        'RetinaScaleHeight' => 'HTMLText',
        'RetinaScaleMaxHeight' => 'HTMLText',
        'RetinaFit' => 'HTMLText',
        'RetinaFitMax' => 'HTMLText',
        'RetinaResizedImage' => 'HTMLText',
        'RetinaFill' => 'HTMLText',
        'RetinaFillMax' => 'HTMLText',
        'RetinaCropWidth' => 'HTMLText',
        'RetinaCropHeight' => 'HTMLText',
        'RetinaPad' => 'HTMLText'
    ];

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaFill($width, $height)
    {
        $img1 = $this->owner->Fill($width, $height);
        $img2 = $this->owner->Fill($width * 1.5, $height * 1.5);
        $img3 = $this->owner->Fill($width * 2, $height * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->Fill($width, $height);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }
    
    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaFillMax($width, $height)
    {
        $img1 = $this->owner->FillMax($width, $height);
        $img2 = $this->owner->FillMax($width * 1.5, $height * 1.5);
        $img3 = $this->owner->FillMax($width * 2, $height * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->FillMax($width, $height);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaPad($width, $height, $backgroundColor = 'FFFFFF', $transparencyPercent = 0)
    {
        $img1 = $this->owner->Pad($width, $height, $backgroundColor, $transparencyPercent);
        $img2 = $this->owner->Pad($width * 1.5, $height * 1.5, $backgroundColor, $transparencyPercent);
        $img3 = $this->owner->Pad($width * 2, $height * 2, $backgroundColor, $transparencyPercent);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->Pad($width, $height, $backgroundColor, $transparencyPercent);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaFitMax($width, $height)
    {
        $img1 = $this->owner->FitMax($width, $height);
        $img2 = $this->owner->FitMax($width * 1.5, $height * 1.5);
        $img3 = $this->owner->FitMax($width * 2, $height * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->FitMax($width, $height);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaResizedImage($width, $height)
    {
        $img1 = $this->owner->ResizedImage($width, $height);
        $img2 = $this->owner->ResizedImage($width * 1.5, $height * 1.5);
        $img3 = $this->owner->ResizedImage($width * 2, $height * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->ResizedImage($width, $height);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }
    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaFit($width, $height)
    {
        $img1 = $this->owner->Fit($width, $height);
        $img2 = $this->owner->Fit($width * 1.5, $height * 1.5);
        $img3 = $this->owner->Fit($width * 2, $height * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->Fit($width, $height);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaCropWidth($width)
    {
        $img1 = $this->owner->CropWidth($width);
        $img2 = $this->owner->CropWidth($width * 1.5);
        $img3 = $this->owner->CropWidth($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->CropWidth($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaCropHeight($width)
    {
        $img1 = $this->owner->CropHeight($width);
        $img2 = $this->owner->CropHeight($width * 1.5);
        $img3 = $this->owner->CropHeight($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->CropHeight($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaScaleWidth($width)
    {
        $img1 = $this->owner->ScaleWidth($width);
        $img2 = $this->owner->ScaleWidth($width * 1.5);
        $img3 = $this->owner->ScaleWidth($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->ScaleWidth($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaScaleMaxWidth($width)
    {
        $img1 = $this->owner->ScaleMaxWidth($width);
        $img2 = $this->owner->ScaleMaxWidth($width * 1.5);
        $img3 = $this->owner->ScaleMaxWidth($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->ScaleMaxWidth($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaScaleMaxHeight($width)
    {
        $img1 = $this->owner->ScaleMaxHeight($width);
        $img2 = $this->owner->ScaleMaxHeight($width * 1.5);
        $img3 = $this->owner->ScaleMaxHeight($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->ScaleMaxHeight($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return html
     */
    public function RetinaScaleHeight($width)
    {
        $img1 = $this->owner->ScaleHeight($width);
        $img2 = $this->owner->ScaleHeight($width * 1.5);
        $img3 = $this->owner->ScaleHeight($width * 2);

        if (!$img1 || !$img2 || !$img3) {
            return $this->owner->ScaleHeight($width);
        }

        return HTML::createTag('img', [
            'alt' => $this->owner->Title,
            'src' => $img1->getURL(),
            'srcset' => $img1->getURL() . ' 1x, ' . $img2->getURL() . ' 1.5x,' . $img3->getURL() . ' 2x'
        ]);
    }
}
