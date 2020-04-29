<?php
/**
 * Get the client
 */
require_once __DIR__ . '/autoload.php';

/**
 * Define configuration
 */

/* Username, password and endpoint used for server to server web-service calls */
Lyra\Client::setDefaultUsername("32078179");
Lyra\Client::setDefaultPassword("testpassword_lTAQbUuDdgUwlhttiGLC6Pa9gkgATOJTGDDVF5zG6Tlpa");
Lyra\Client::setDefaultEndpoint("https://api.systempay.fr");

/* publicKey and used by the javascript client */
Lyra\Client::setDefaultPublicKey("32078179:testpublickey_65SMaMZ2z7pvWq4E7bEgKbVTTcfBdgPUsxHBVi4MxITyD");

/* SHA256 key */
Lyra\Client::setDefaultSHA256Key("DpjH2fZUwX1hQLEXBJi7bwJYwyjdBHelzDTHYmQ0UwdO5");