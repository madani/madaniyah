<?php
/**
 * @author	2011-2012 Nihki Prihadi <nihki@madaniyah.com>
 * @version $Id: Keys.php 1 2012-02-07 13:22:13Z $
 */

interface Pandamp_Keys
{
// -------- application in registry	
	const REGISTRY_APP_OBJECT = 'com.pandamp.registry.application';
// -------- registry
    const REGISTRY_AUTH_OBJECT = 'com.pandamp.registry.authObject';
// -------- session auth namespace
    const SESSION_AUTH_NAMESPACE = 'com.pandamp.session.authNamespace';
// -------- Use to set/get application template.
	const APP_TEMPLATE = 'com.pandamp.application.template';
}