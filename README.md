# Watermark Plugin for phtagr

Add watermark to resized images. Watermark will be added to the largest preview
image. phTagr creates other previews by the largest preview. So all preview
images will have the watermark.


# Requirements

Required version of phtagr 2.3-dev (2014-03-02)


# Installation

Copy the Watermark plugin to the Plugin folder of phtagr and enabled it in 
phTagr's `bootstrap.php` in Config directory (`Config/boostrap.php`). Add folling
lines to the bottom of the `boostrap.php` file:

    CakePlugin::load('Watermark', array('bootstrap' => true, 'routes' => false));

This line can be obmitted if all plugins are loaded within the `bootstrap.php` 
via

    CakePlugin::loadAll(array(array('bootstrap' => true, 'route' => true));

## Delete old preview files

phTagr creates previews an cache them into the `cache` directory of each users
directory (`users/[userId]/cache`) within the phTagr directory. So only new
created previews will have a watermark.

To add the watermark to existing preview images remove all directories below 
the `cache` directory to ensure a recreation of the previews with watermark.


# Configuration

## Watermark file

It is recommended to create a squared `watermark.png` file with size of 960x960 
pixels. Since the watermark will be scaled it is also recommended to use a
large text or symbol.

A watermark file can be either set by user or globally by the gallery admin.

If a user want to add a watermark to his preview images he has to upload a
`watermark.png` file directly to his upload directory (no sub folder). The 
plugin will use this file. `watermark.gif` is also a valid watermark file.

If an administrator want a global watermark he has to configure the watermark
image via `Configuration`. Use

    Configuration::write('plugin.watermark.image', 'path/to/watermark/image');

in phTagr's `Config/core.php` file.

The user watermark has a higher priority. If the user has a watermark image
and the admin set a global watermark image the user watermark image will be
used.

## Scale mode

There are different scaling modes: `width`, `height`, `bestFit`, `inner`, 
and `none`. Default is `inner`.

If `scaleMode` is set to `width` the watermark is scaled to fit the width
of the image. Analog to `scaleMode` with value `height`.

If `scaleMode` is set to `bestFit` the watermark is scaled that it fits
into the image without cutting the watermark.

The watermark is not scaled if `scaleMode`  is set to `none`.

On `scaleMode` `inner` the longer watermark side is scaled to the shorter
image side. This ensures that the watermark has equal sizes for different
image orientations of portrait and landscape.

You can set the scaleMode in `core.php` with `plugin.watermark.scaleMode`.

    Configure::write('plugin.watermark.scaleMode', 'bestFit');

## Position

The watermark will be placed in the center of the image. However, you can use 
cardinal directions like 'n' for north, 'e' for east, 's' for south, and 
'w' for west. To place watermark in left bottom use south-east with 'se'.

To position the watermark edit phTagr's `Config/core.php` file and add

    Configure::write('plugin.watermark.position', 'se');
    

# Functionality

The plugin registers itself to the event `Component.ImageResizer.afterResize` 
which will be called after an image was resized. If the source is the original 
file (`$option['isOriginal']` is set to `true`) it will load the plugin's 
`WatermarkComponent` and add the watermark image to the preview image.


# Licence

MIT


# Support

Mail to xemle [at] phtagr.org