Swissflags
=

The 26 swiss canton flags ready to use in your web project.

Example
-

<pre>
    &lt;div class="flag flag-16 flag-BE"&gt;&lt;/div&gt;
    &lt;div class="flag flag-32 flag-BE"&gt;&lt;/div&gt;
    &lt;div class="flag flag-64 flag-BE"&gt;&lt;/div&gt;
</pre>

Note
-

The &laquocreate.php&raquo; script is not limited to swissflags. You could provide all kind of images
in the images folder (for example SVG files). There are also several parameters to adjust the result to fit your needs.

If you have any questions feel free to contact me anytime.
// Benny

Usage
-
<pre>
    php create.php --sizes=16,32,64 --class-name=flag
    php create.php -s16,32,64 -nflag
    
    » This will create 16px, 32px and 64px version of the images with the flag class name.
    
    php create.php --sizes=30x20,60x40 --class-name=thumbs --columns=8 --format=jpg
    php create.php -s30x20,60x40 -nthumbs -c8 -fjpg

    » This will create 30x20px and 60x40px version of the images with thumbs as class name,
      8 image columns (in a sprite) and JPG format.
</pre>


Optimize
-

Before using the images in production I suggest to open them in Photoshop and save it for web.
(cmd + alt + shift + s)


