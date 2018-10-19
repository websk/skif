<?php

namespace WebSK\Skif;

class HTTP
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const STATUS_CONTINUE = 100;
    const STATUS_SWITCHING_PROTOCOLS = 101;
    const STATUS_PROCESSING = 102;            // RFC2518
    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_ACCEPTED = 202;
    const STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
    const STATUS_NO_CONTENT = 204;
    const STATUS_RESET_CONTENT = 205;
    const STATUS_PARTIAL_CONTENT = 206;
    const STATUS_MULTI_STATUS = 207;          // RFC4918
    const STATUS_ALREADY_REPORTED = 208;      // RFC5842
    const STATUS_IM_USED = 226;               // RFC3229
    const STATUS_MULTIPLE_CHOICES = 300;
    const STATUS_MOVED_PERMANENTLY = 301;
    const STATUS_FOUND = 302;
    const STATUS_SEE_OTHER = 303;
    const STATUS_NOT_MODIFIED = 304;
    const STATUS_USE_PROXY = 305;
    const STATUS_RESERVED = 306;
    const STATUS_TEMPORARY_REDIRECT = 307;
    const STATUS_PERMANENTLY_REDIRECT = 308;  // RFC7238
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_PAYMENT_REQUIRED = 402;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_METHOD_NOT_ALLOWED = 405;
    const STATUS_NOT_ACCEPTABLE = 406;
    const STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
    const STATUS_REQUEST_TIMEOUT = 408;
    const STATUS_CONFLICT = 409;
    const STATUS_GONE = 410;
    const STATUS_LENGTH_REQUIRED = 411;
    const STATUS_PRECONDITION_FAILED = 412;
    const STATUS_REQUEST_ENTITY_TOO_LARGE = 413;
    const STATUS_REQUEST_URI_TOO_LONG = 414;
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const STATUS_EXPECTATION_FAILED = 417;
    const STATUS_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const STATUS_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    const STATUS_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const STATUS_LOCKED = 423;                                                      // RFC4918
    const STATUS_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const STATUS_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const STATUS_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const STATUS_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const STATUS_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const STATUS_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    const STATUS_NOT_IMPLEMENTED = 501;
    const STATUS_BAD_GATEWAY = 502;
    const STATUS_SERVICE_UNAVAILABLE = 503;
    const STATUS_GATEWAY_TIMEOUT = 504;
    const STATUS_VERSION_NOT_SUPPORTED = 505;
    const STATUS_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const STATUS_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const STATUS_LOOP_DETECTED = 508;                                               // RFC5842
    const STATUS_NOT_EXTENDED = 510;                                                // RFC2774
    const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    const HEADER_ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    const HEADER_ORIGIN = 'Origin';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
    const HEADER_CACHE_CONTROL = 'Cache-Control';
    const HEADER_EXPIRES = 'Expires';
    const HEADER_PRAGMA = 'Pragma';
}