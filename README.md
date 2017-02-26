Dominant image color for lazy loading images in TYPO3
=====================================================

This extension adds metadata extractor that detects/calculates the dominant color of an images after upload.
The dominant color can be used in FE rendering of images to fill the empty places of the lazy loaded images.

Features:
- metadata extractor to pick/store the dominant color of every uploaded image
- `dic:lazyImage` viewhelper that can be used as replacement of `f:image`
  the `src` becomes a `data:image/svg+xml;base64` string that holds a svg placeholder image in the dominant color
  the real image is added to `data-src`
- property `dominant_color` is added to the properties of every image object `{image.properties.dominant_color}`

This extension is inspired by the blog [Dominant Colors for Lazy-Loading Images](https://manu.ninja/dominant-colors-for-lazy-loading-images) and uses [ksubileau/color-thief-php](https://github.com/ksubileau/color-thief-php) to detect the dominant color of the image.

