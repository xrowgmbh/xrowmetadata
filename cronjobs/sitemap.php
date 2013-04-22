<?php

$ini = eZINI::instance( 'site.ini' );
$xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );

//getting custom set site access or default access
if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'AvailableSiteAccessList' ) )
{
    $siteAccessArray = $xrowsitemapINI->variable( 'SitemapSettings', 'AvailableSiteAccessList' );
}
else
{
    $siteAccessArray = array(
        $ini->variable( 'SiteSettings', 'DefaultAccess' )
    );
}

if ( $xrowsitemapINI->variable( 'Settings', 'Sitemap' ) == 'enabled' )
{
    if ( ! $isQuiet )
    {
        $cli->output( "Generating Regular Sitemaps...\n" );
    }
    xrowSitemapTools::siteaccessCallFunction( $siteAccessArray, 'xrowSitemapTools::createSitemap' );
}

xrowSitemapTools::ping();

?>