# Retina images

## An add-on for adaptive images

Allows support for high-resolution displays by automatically creating different 
assets representing the same image. It specifies bitmapped images by adding a 
srcset attribute to the img element, as specified
by the W3C draft http://www.w3.org/html/wg/drafts/srcset/w3c-srcset/

It does it out the box on through the template, WYSIWYG requires a modification 
to your getCMSFields() method.

Older browsers require a polyfill. [All modern browsers support the tag](http://caniuse.com/#feat=srcset)

## Usage

+ run ```composer require otago/silverstripe-retinaimages```
+ run ?flush=1 on your page
+ done! time for a beer


## How it works

When creating a generated image it creates three different images, scaled up to
the following factors: 1.0x, 1.5x, and 2.0x. The default generated image is 
also created, which is used as the src attribute. These image urls are then 
placed into the srcset tag.

## srcset Polyfills

https://github.com/borismus/srcset-polyfill/
https://github.com/culshaw/srcset
http://jimbobsquarepants.github.io/srcset-polyfill/
