<?php

namespace Classes\Utils;

/**
 * Class which extends twig with our custom filters
 *
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class Filters extends \Twig_Extension {

    public function getName() {
        return "filters";
    }

    public function getFilters() {
        return array(
            "gravatar" => new \Twig_Filter_Method($this, "gravatar"),
        );
    }

    public function gravatar($input, $options = null) {
        return \Classes\User\Helper::getGravatarImage($input);
    }
}
