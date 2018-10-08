define([
    'lib/debounce',
    'lib/jds'
], function(debounce, jds) {

    /**
     * BR context Globals
     */
    var
        BASE_HREF = 'http://www.betterrecipes.com',
        FOOTER_LINKS = {
            'Recipe Categories': [
                'Appetizer',
                'Beef',
                'Bread',
                'Breakfast',
                'Cake',
                'Chicken',
                'Christmas',
                'Cookie',
                'Copycat',
                'Crock Pot',
                'Dessert',
                'Diabetic',
                'Drink',
                'Easter',
                'Easy',
                'FXB',
                'Grilling',
                'Halloween',
                'Healthy',
                'Italian',
                'Low Carb',
                'Low Fat',
                'Mexican',
                'Pork',
                'Salad',
                'Seafood',
                'Soup',
                'Thanksgiving',
                'Vegetarian',
            ],
            'Your Account': [
                ['My Recipe Box', '/myrecipebox/all/date-desc/1'],
                ['Help Resources', '/help'],
                ['Login/Register', '/signup']
            ],
            'Better Recipes': [
                ['Free Newsletters', '/email-signup'],
                ['Follow Us on Twitter', 'https://twitter.com/BetterRecipes'],
                ['Find Us on Facebook', 'https://www.facebook.com/betterrecipes']
            ]
        },
        // Do not allow ads to be refreshed more than once every
        // AD_REFRESH_TIME_LIMIT ms
        AD_REFRESH_TIME_LIMIT = 4000,
        // Keep an internal DEEP copy of OX_ads so that we can reuse it when we
        // call refreshAds().  OpenX will Array.shift and destroy OX_ads in the
        // global window context.
        OX_ads_copy = [];



    /**
     * placeholder, could do something, but doesn't
     */
    function betterrecipes() {}

    /**
     * Begin loading of Ad tags as soon as possible
     *
     *
     */
    function initAds() {
        // initialize the ad zones
        resetAdZones();

        ourbestbox();

        zergnet();
    }


    /**
     * For any class="ad" element, create a child <div> and give it an
     * id of the parent data-id.
     *
     * For example:
     *      <div class="ad" data-id="ABC-XYZ"></div>
     *
     * Becomes:
     *      <div class="ad" data-id="ABC-XYZ">
     *          <div id="ABC-XYZ">
     *          </div>
     *      </div>
     *
     * This is necessary, because OpenX, specifically the OX.load() method,
     * will replace whatever element getElementById() matches with it’s own
     * element, typically an iFrame, and will not restore the id attribute
     * on the newly created element.
     *
     * So, in order to keep this <div id="ABC-XYZ"> selectable, we must create
     * it everytime before calling OX.load().
     *
     */
    function resetAdZones() {
        $('.ad').each(function(i, e) {
            var $e = $(e);
            if (!$e.data('id')) {
                return;
            }
            $e
                .empty()
                .append($('<div>').attr('id', $e.data('id')));
        });
    }

    /**
     * Asynchronously load an ad tag (JavaScript) `url` and invoke the
     * `success` and `failure` callbacks accordingly.
     */
    function scriptAsync(url, success, failure) {
        $.ajax({
            url: url,
            dataType: 'script',
            cache: true
        }).done(success).fail(failure);
    }

    function zergnetSuccess() {
        // console.log('Zergnet loaded');
    }

    function zergnetFailure() {
        // console.error('Zergnet failed to load');
    }

    function zergnet() {
        scriptAsync(
            'http://www.zergnet.com/zerg.js?id=29457',
            zergnetSuccess,
            zergnetFailure
        );
    }

    function ourbestbox(callbackString) {
        if (!callbackString) {
            callbackString = 'ourbestbox';
        }

        // define the callback in the global window context
        W[callbackString] = function (d) {
            $('#' + callbackString).append(d && d['result'] || '');
        }

        $.ajax({
            // url: 'http://brstage.resolute.com/slideshows/ourbestbox_ajax/',
            url: 'http://www.betterrecipes.com/slideshows/ourbestbox_ajax',
            jsonp: callbackString,
            dataType: 'jsonp',
            data: {
                format: "json"
            }
        });
    }

    /**
     * This is the actual method to refresh ad units.  It will be throttle’d
     * and exposed as refreshAds
     */
    function refreshAdsNow() {
        // console.debug('Ad refresh at ' + new Date());
        resetAdZones();
        zergnet();
        ourbestbox();
    }

    // wrap and throttle the actual ad refresh method
    var refreshAds = betterrecipes.refreshAds = debounce(function() {
            ADS_BEING_THROTTLED = false;
            // add a little cushion to allow a transition to finish
            // before refreshing the ads:
            setTimeout(refreshAdsNow, 1000)
        },
        AD_REFRESH_TIME_LIMIT,
        true,
        function() {
            ADS_BEING_THROTTLED = true;
        }
    );


    function footer() {
        var $copyright = $('footer nav');
        for (var heading in FOOTER_LINKS) {
            var $nav = $('<nav>').append($('<h5>').html(heading)),
                links = FOOTER_LINKS[heading],
                links_len = links.length;
            for (var i = 0; i < links_len; i++) {
                var link = links[i],
                    name,
                    url;
                if ($.type(link) == 'string') {
                    // special case for recipe categories
                    name = link;
                    url = BASE_HREF.replace(/www/, name.toLowerCase().replace(' ', '')) + '/';
                    // special case for Copycat recipes
                    if (name == 'Copycat') {
                        url = BASE_HREF.replace(/www/, 'restaurant') + '/';
                    }
                    name += ' Recipes'
                } else {
                    url = link[1].charAt(0) == '/' ? BASE_HREF + link[1] : link[1];
                    name = link[0];
                }
                $nav.append($('<a>').attr('href', url).html(name));
            }
            $copyright.before($nav);
        }
    }

    /*
     * Ready method: runs after DOMContentLoaded and once jQuery is ready
     *
     */
    ready(function() {

        // hamburger opener/closer on a mobile viewport size
        $('body>header .menu').on(ON_CLICK_SELECTSTART_TOUCHSTART, debounce(function(evt) {
            evt.preventDefault();
            $(this).closest('header').toggleClass('open');
        }, 100, true));

        // ad unit initialization
        initAds();

        // create the footer links
        footer();

    });

    // register our refreshAds method with jds
    jds.refreshAds = refreshAds;

    // expose the global window.BR object
    W['BR'] = betterrecipes;

    return betterrecipes;

});
