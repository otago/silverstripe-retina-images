<?php

Deprecation::notification_version('1.0', 'retinaimages');

define('RETINA_MODULE_DIR', dirname(__FILE__));


Object::useCustomClass('Image', 'RetinaImage');
Object::useCustomClass('Image_Cached', 'RetinaImage_Cached');
// SiteTree uses new HtmlEditorField() instead of HtmlEditorField::create()
// as a result if you want retina images in your WYSIWYG editor you'll need to
// recreate the HtmlEditorField.
Object::useCustomClass('HtmlEditorField', 'RetinaImageHtmlEditorField');

// tinyCMS additional HTML5 elements
$wysiwyg = HtmlEditorConfig::get('cms')->getOption('valid_elements');
$wysiwyg .= ',-img[id|dir|longdesc|usemap|class|src|border|alt=|title|width|height|align|data*|srcset]';
HtmlEditorConfig::get('cms')->setOption('valid_elements', $wysiwyg);
