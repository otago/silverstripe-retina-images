<?php

namespace OP;

use SilverStripe\Assets\Shortcodes\ImageShortcodeProvider;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\View\HTML;
use SilverStripe\Assets\Image;

class RetinaImageShortcodeProvider extends ImageShortcodeProvider {

    public static function handle_shortcode($args, $content, $parser, $shortcode, $extra = array()) {
        $cache = static::getCache();
        $cacheKey = static::getCacheKey($args);

        $item = $cache->get($cacheKey);
        if ($item) {
            /** @var AssetStore $store */
            $store = Injector::inst()->get(AssetStore::class);
            if (!empty($item['filename'])) {
                $store->grant($item['filename'], $item['hash']);
            }
            return $item['markup'];
        }

        // Find appropriate record, with fallback for error handlers
        $record = static::find_shortcode_record($args, $errorCode);
        if ($errorCode) {
            $record = static::find_error_record($errorCode);
        }
        if (!$record || !$record->exists()) {
            return null; // There were no suitable matches at all.
        }

        // Check if a resize is required
        $src = $record->Link();
        $src15 = '';
        $src20 = '';

        if ($record instanceof Image) {
            $width = isset($args['width']) ? $args['width'] : null;
            $height = isset($args['height']) ? $args['height'] : null;
            $hasCustomDimensions = ($width && $height);
            if ($hasCustomDimensions && (($width != $record->getWidth()) || ($height != $record->getHeight()))) {
                $resized = $record->ResizedImage($width, $height);
                // Make sure that the resized image actually returns an image
                if ($resized) {
                    $src = $resized->getURL();
                }
            }
            // we create double and 1.5x sized images regardless of the resizing.
            $resized20 = $record->ResizedImage($width * 2, $height * 2);
            if ($resized20) {
                $src20 = $resized20->getURL();
            }
            $resized15 = $record->ResizedImage($width * 1.5, $height * 1.5);
            if ($resized15) {
                $src15 = $resized15->getURL();
            }
        }

        // Build the HTML tag
        $attrs = array_merge(
                // Set overrideable defaults
                ['src' => '', 'alt' => $record->Title],
                // Use all other shortcode arguments
                $args,
                // But enforce some values
                ['id' => '', 'src' => $src], ['srcset' => "$src 1x,$src15  1.5x,$src20 2x"]
        );

        // Clean out any empty attributes
        $attrs = array_filter($attrs, function ($v) {
            return (bool) $v;
        });

        $markup = HTML::createTag('img', $attrs);

        // cache it for future reference
        $cache->set($cacheKey, [
            'markup' => $markup,
            'filename' => $record instanceof File ? $record->getFilename() : null,
            'hash' => $record instanceof File ? $record->getHash() : null,
        ]);

        return $markup;
    }

}
