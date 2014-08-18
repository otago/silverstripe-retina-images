# Retina images

## An add-on for adaptive images

Allows support for high-resolution displays by automatically creating different 
assets representing the same image. It specifies bitmapped images by adding a 
srcset attribute to the img element, as specified
by the W3C draft http://www.w3.org/html/wg/drafts/srcset/w3c-srcset/

It does it out the box on through the template, WYSIWYG requires a modification 
to your getCMSFields() method.

Non-compliant browsers require a polyfill. At this time of writing, all 
non-webkit browsers require this.

**Only resized images** will be have adaptive images generated, for example
```$Image.paddedImage(50)```. There's no point in upscaling native images, but
you can force halving images changing the boolean on ```$forceretina``` in ```RetinaImage.php```.
Note this will half the size of every non resized image.

## Usage

+ Install the add-on
+ run ?flush=1 on your page
+ done! time for a beer

## WYSIWYG support

You'll need to modify your ```getCMSFields()``` function. ```create()``` a 
\HtmlEditorField isntead of using ```new```, there's a custom class that will 
modify its behaviour. In most cases you'll only need to do this once in your
Page.php file:

```
$fields->removeFieldFromTab('Root.Main', 'Content');
$fields->addFieldToTab('Root.Main', 
	HtmlEditorField::create('Content', _t('SiteTree.HTMLEDITORTITLE', 'Content', 'HTML editor title')),
	'Metadata'
);
```

## How it works

When creating a generated image it creates three different images, scaled up to
the following factors: 1.0x, 1.5x, and 2.0x. The default generated image is 
also created, which is used as the src attribute. These image urls are then 
placed into the srcset tag.

## Tweaking the variables

The yml config has two variables, qualityDegrade and qualityCap. qualityDegrade
is the percentage per ratio to degrade by as the images get bigger. A 
qualityDegrade of 30 will degrade a 2x image by 30% (with a default quality of
75% it will be 45%). qualityCap is there to make sure you don’t go too low. 

There are plenty of online resources that describe why lowering the quality is
a good idea as the images get larger. 

## srcset Polyfills

https://github.com/borismus/srcset-polyfill/
https://github.com/culshaw/srcset
http://jimbobsquarepants.github.io/srcset-polyfill/
