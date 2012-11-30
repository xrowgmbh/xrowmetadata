<?php

class xrowSitemapItemAlternateLink extends xrowSitemapItem
{
    public $href; // text

    function __construct( $href = false )
    {
        if ( $href )
        {
            $this->href = $href;
        }

    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        $alter = $sitemap->dom->createElement( 'xhtml:link' );
        $alter->setAttribute( 'rel', 'alternate' );
        $alter->setAttribute( 'media', 'only screen and (max-width: 640px)' );
        $alter->setAttribute( 'href', $this->href );
        return $alter;
    }

    /**
     * @return xrowSitemapItemAlternateLink
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemAlternateLink( $array['href'] );
    }
}
?>
