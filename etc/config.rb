# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "../web/css"
sass_dir = "../src/scss"
images_dir = "../web/img"
fonts_dir = "fonts"
http_images_path = "img"
javascripts_dir = "../web/js"

# You can select your preferred output style here (can be overridden via the command line):
output_style = :compressed

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false

# Disable all of the asset cache busting garbage
asset_cache_buster :none

# Enable Sourcemaps
# sass_options = {:sourcemap => true}