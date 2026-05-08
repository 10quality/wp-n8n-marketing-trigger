<?php

namespace N8nMarketingTrigger\Controllers;

/**
 * Asset URL normalization controller.
 *
 * @package n8n-marketing-trigger
 */
class AssetController
{
    /**
     * Normalizes malformed base URLs used by WPMVC asset helpers.
     *
     * @param string $url
     *
     * @return string
     */
    public function normalize_base_url( $url )
    {
        if ( ! is_string( $url ) || $url === '' ) {
            return $url;
        }
        if ( strpos( $url, 'http:' ) === 0 && strpos( $url, 'http://' ) !== 0 ) {
            $url = preg_replace( '/^http:/', 'http://', $url );
        }
        if ( strpos( $url, 'https:' ) === 0 && strpos( $url, 'https://' ) !== 0 ) {
            $url = preg_replace( '/^https:/', 'https://', $url );
        }
        return rtrim( $url, '/' );
    }
    /**
     * Normalizes final enqueued asset src URL.
     *
     * @param string $src
     *
     * @return string
     */
    public function normalize_loader_src( $src )
    {
        if ( ! is_string( $src ) || $src === '' ) {
            return $src;
        }
        if ( strpos( $src, 'http:' ) === 0 && strpos( $src, 'http://' ) !== 0 ) {
            return preg_replace( '/^http:/', 'http://', $src );
        }
        if ( strpos( $src, 'https:' ) === 0 && strpos( $src, 'https://' ) !== 0 ) {
            return preg_replace( '/^https:/', 'https://', $src );
        }
        return $src;
    }
}
