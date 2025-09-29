<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\View\Requirements;

    /**
     * @template T of Page
     * @extends ContentController<T>
     */
    class PageController extends ContentController
    {
        /**
         * An array of actions that can be accessed via a request. Each array element should be an action name, and the
         * permissions or conditions required to allow the user to access it.
         *
         * <code>
         * [
         *     'action', // anyone can access this action
         *     'action' => true, // same as above
         *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
         *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
         * ];
         * </code>
         *
         * @var array
         */
        private static $allowed_actions = [];

        protected function init()
        {
            parent::init();
            // You can include any CSS or JS required by your project here.
            // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/

            // Bootstrap and Fontawesome
            Requirements::css("https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css");
            Requirements::javascript("https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js");
            Requirements::css("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css");

            // Leaflet (external)
            /*
            Requirements::css('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
            Requirements::javascript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
            */
        }
    }
}
