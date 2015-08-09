<h2><?php _e('Translators', $td); ?></h2>

<p><?php _e('Other than the default English language, Post Snippets has been translated to the following languages by these great people:', $td); ?></p>

<ul>
<?php
    /**
     * Output a translator list item.
     *
     * @param  string  $country
     * @param  string  $countryCode
     * @param  string  $url
     * @param  string  $name
     * @param  string  $note
     */
    function postsnippetTranslator($country, $countryCode, $url, $name, $note = '')
    {
        global $td;
        printf('<li>%s (%s) %s <a href="%s">%s</a>%s.</li>', $country, $countryCode, __('by', $td), $url, $name, $note);
    }

    postsnippetTranslator(__('Belarusian', $td), 'be_BY', 'http://webhostinggeeks.com/science/', 'Alexander Ovsov');
    postsnippetTranslator(__('French', $td), 'fr_FR', 'http://www.oyabi.fr/', 'Thomas Cailhe (Oyabi)');
    postsnippetTranslator(__('German', $td), 'de_DE', 'http://www.inmotionhosting.com/', 'Brian Flores');
    postsnippetTranslator(__('Hebrew', $td), 'he_IL', 'http://www.sagive.co.il/', 'Sagive');
    postsnippetTranslator(__('Lithuanian', $td), 'lt_LT', 'http://www.webhostinghub.com/', 'Nata Strazda');
    postsnippetTranslator(__('Polish', $td), 'pl_PL', 'http://ittw.pl/', 'Tomasz Wesołowski');
    postsnippetTranslator(__('Romanian', $td), 'ro_RO', 'http://webhostinggeeks.com/', 'Web Hosting Geeks');
    postsnippetTranslator(__('Russian', $td), 'ru_RU', 'http://www.fatcow.com/', 'FatCow');
    postsnippetTranslator(__('Slovak', $td), 'sk_SK', 'http://webhostinggeeks.com/blog/', 'Branco Radenovich');
    postsnippetTranslator(__('Serbo-Croatian', $td), 'sr_RS', 'http://www.webhostinghub.com/', 'Borisa Djuraskovic');
    postsnippetTranslator(__('Spanish', $td), 'es_ES', 'http://www.soludata.net/site/', 'Melvis E. Leon Lopez');
    postsnippetTranslator(__('Swedish', $td), 'sv_SE', 'http://johansteen.se/', 'Johan Steen', ' (Plugin author)');
    postsnippetTranslator(__('Turkish', $td), 'tr_TR', 'http://www.tml.web.tr/', 'Ersan Özdil');
    postsnippetTranslator(__('Ukrainian', $td), 'uk_UA', 'http://getvoip.com/', 'Michael Yunat');
?>
