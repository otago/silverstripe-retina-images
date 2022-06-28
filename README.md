# Retina images

## An add-on for adaptive images

Makes your images look crisp and sexy on high DPI devices.

## Usage

+ run ```composer require otago/silverstripe-retinaimages```
+ run ?flush=1 on your page

All images used in your WYSIWYG editor should now be retina upscaled images
 using srcset. you can check this by inspecting your <img /> tags

## Using retina in the template

If you want retina images in your template, you specify this with prefixing all image functions with retina.

For example, **$Image.Fill(50,50)** becomes **$Image.RetinaFill(50,50)**

```
// Scaling functions
$Image.RetinaScaleWidth(150) // Returns a 150x75px image
$Image.RetinaScaleMaxWidth(100) // Returns a 100x50px image (like ScaleWidth but prevents up-sampling)
$Image.RetinaScaleHeight(150) // Returns a 300x150px image (up-sampled. Try to avoid doing this)
$Image.RetinaScaleMaxHeight(150) // Returns a 200x100px image (like ScaleHeight but prevents up-sampling)
$Image.RetinaFit(300,300) // Returns an image that fits within a 300x300px boundary, resulting in a 300x150px image (up-sampled)
$Image.RetinaFitMax(300,300) // Returns a 200x100px image (like Fit but prevents up-sampling)

// Warning: This method can distort images that are not the correct aspect ratio
$Image.RetinaResizedImage(200, 300) // Forces dimensions of this image to the given values.

// Cropping functions
$Image.RetinaFill(150,150) // Returns a 150x150px image resized and cropped to fill specified dimensions (up-sampled)
$Image.RetinaFillMax(150,150) // Returns a 100x100px image (like Fill but prevents up-sampling)
$Image.RetinaCropWidth(150) // Returns a 150x100px image (trims excess pixels off the x axis from the center)
$Image.RetinaCropHeight(50) // Returns a 200x50px image (trims excess pixels off the y axis from the center)

// Padding functions (add space around an image)
$Image.RetinaPad(100,100) // Returns a 100x100px padded image, with white bars added at the top and bottom
$Image.RetinaPad(100, 100, CCCCCC) // Same as above but with a grey background

```



## How it works

Allows support for high-resolution displays by automatically creating different 
assets representing the same image. It specifies bitmapped images by adding a 
srcset attribute to the img element, as specified
by the W3C draft http://www.w3.org/html/wg/drafts/srcset/w3c-srcset/

When creating a generated image it creates three different images, scaled up to
the following factors: 1.0x, 1.5x, and 2.0x. The default generated image is 
also created, which is used as the src attribute. These image urls are then 
placed into the srcset tag.

It does it out the box on through the template, WYSIWYG requires a modification 
to your getCMSFields() method.

## srcset Polyfills

Older browsers require a polyfill. [All modern browsers support the tag](http://caniuse.com/#feat=srcset)

https://github.com/borismus/srcset-polyfill/
https://github.com/culshaw/srcset
http://jimbobsquarepants.github.io/srcset-polyfill/
