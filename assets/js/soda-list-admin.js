/**
 * Soda List — Admin dashboard JS.
 *
 * Handles:
 *  - Test Connection button (live AJAX call against whatever URL is in the field)
 *  - Clear Cache Now button
 */

( function () {
    'use strict';

    const cfg = window.sodaListAdmin || {};

    /* ---------------------------------------------------------------------- */
    /* Helpers                                                                 */
    /* ---------------------------------------------------------------------- */

    function post( action, data ) {
        const body = new URLSearchParams( Object.assign( { action }, data ) );
        return fetch( cfg.ajaxUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body.toString(),
        } ).then( function ( r ) { return r.json(); } );
    }

    function setResult( el, text, state ) {
        el.textContent = text;
        el.className   = 'sl-test-result is-' + state;
    }

    /* ---------------------------------------------------------------------- */
    /* Test Connection                                                         */
    /* ---------------------------------------------------------------------- */

    function initTestConnection() {
        const btn    = document.getElementById( 'sl-test-api' );
        const result = document.getElementById( 'sl-test-result' );
        const input  = document.getElementById( cfg.testAction
            ? 'soda_list_api_url'
            : 'soda_list_api_url'
        );

        if ( ! btn || ! result || ! input ) return;

        btn.addEventListener( 'click', function () {
            const url = input.value.trim();

            if ( ! url ) {
                setResult( result, 'Please enter an API URL first.', 'error' );
                return;
            }

            btn.disabled    = true;
            btn.textContent = cfg.i18n.testing || 'Testing…';
            setResult( result, cfg.i18n.testing || 'Testing…', 'loading' );

            post( cfg.testAction, {
                nonce: btn.dataset.nonce,
                url:   url,
            } )
            .then( function ( res ) {
                if ( res.success ) {
                    const msg = ( cfg.i18n.success || 'Connected! %d listing(s) found.' )
                        .replace( '%d', res.data.count );
                    setResult( result, '✓ ' + msg, 'success' );
                } else {
                    const msg = ( cfg.i18n.error || 'Connection failed: %s' )
                        .replace( '%s', ( res.data && res.data.message ) || 'Unknown error' );
                    setResult( result, '✗ ' + msg, 'error' );
                }
            } )
            .catch( function ( err ) {
                setResult( result, '✗ ' + err.message, 'error' );
            } )
            .finally( function () {
                btn.disabled    = false;
                btn.textContent = 'Test Connection';
            } );
        } );
    }

    /* ---------------------------------------------------------------------- */
    /* Clear Cache                                                             */
    /* ---------------------------------------------------------------------- */

    function initFlushCache() {
        const btn    = document.getElementById( 'sl-flush-cache' );
        const result = document.getElementById( 'sl-flush-result' );

        if ( ! btn || ! result ) return;

        btn.addEventListener( 'click', function () {
            btn.disabled    = true;
            result.textContent = cfg.i18n.flushing || 'Clearing…';

            post( cfg.flushAction, { nonce: cfg.flushNonce } )
            .then( function () {
                result.textContent = cfg.i18n.flushed || 'Cache cleared.';
                setTimeout( function () { result.textContent = ''; }, 3000 );
            } )
            .catch( function () {
                result.textContent = 'Failed.';
            } )
            .finally( function () {
                btn.disabled = false;
            } );
        } );
    }

    /* ---------------------------------------------------------------------- */
    /* Diagnostic — test the *saved* URL                                      */
    /* ---------------------------------------------------------------------- */

    function initDiagnostic() {
        const btn    = document.getElementById( 'sl-run-diag' );
        const result = document.getElementById( 'sl-diag-result' );

        if ( ! btn || ! result ) return;

        btn.addEventListener( 'click', function () {
            btn.disabled    = true;
            btn.textContent = cfg.i18n.testing || 'Testing…';
            setResult( result, cfg.i18n.testing || 'Testing…', 'loading' );

            post( cfg.diagAction, { nonce: btn.dataset.nonce } )
            .then( function ( res ) {
                if ( ! res.success ) {
                    setResult( result, '✗ Request error', 'error' );
                    return;
                }

                const d = res.data;

                if ( d.error ) {
                    const msg = ( cfg.i18n.diagErr || 'Request failed: %s' )
                        .replace( '%s', d.error );
                    setResult( result, '✗ ' + msg, 'error' );
                } else if ( d.units !== null ) {
                    const msg = ( cfg.i18n.diagOk || 'HTTP %d — %d unit(s) returned.' )
                        .replace( '%d', d.http_code )
                        .replace( '%d', d.units );
                    setResult( result, '✓ ' + msg, 'success' );
                } else {
                    const msg = ( cfg.i18n.diagFail || 'HTTP %d — no valid JSON (bot protection?).' )
                        .replace( '%d', d.http_code );
                    setResult( result, '✗ ' + msg, 'error' );
                }
            } )
            .catch( function ( err ) {
                setResult( result, '✗ ' + err.message, 'error' );
            } )
            .finally( function () {
                btn.disabled    = false;
                btn.textContent = 'Test Stored URL';
            } );
        } );
    }

    /* ---------------------------------------------------------------------- */
    /* Boot                                                                    */
    /* ---------------------------------------------------------------------- */

    document.addEventListener( 'DOMContentLoaded', function () {
        initTestConnection();
        initFlushCache();
        initDiagnostic();
    } );

} )();
