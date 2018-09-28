<?php

use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->unregister('image');
ShortcodeParser::get('default')
    ->register('image', [OP\RetinaImageShortcodeProvider::class, 'handle_shortcode']);

ShortcodeParser::get('regenerator')->unregister('image');
ShortcodeParser::get('regenerator')
    ->register('image', [OP\RetinaImageShortcodeProvider::class, 'regenerate_shortcode']);