<?php

/**
 * Holds SAML settings for the SamlResponse and SamlAuthRequest classes.
 *
 * These settings need to be filled in by the user prior to being used.
 */
class Maestrano_Saml_Settings
{
    const NAMEID_EMAIL_ADDRESS                 = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
    const NAMEID_X509_SUBJECT_NAME             = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';
    const NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';
    const NAMEID_KERBEROS   = 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos';
    const NAMEID_ENTITY     = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';
    const NAMEID_TRANSIENT  = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';
    const NAMEID_PERSISTENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';

    /**
     * The URL to submit SAML authentication requests to.
     * @var string
     */
    public $idpSingleSignOnUrl = '';

    /**
     * The x509 certificate used to authenticate the request.
     * @var string
     */
    public $idpPublicCertificate = '';

    /**
     * The URL where to the SAML Response/SAML Assertion will be posted.
     * @var string
     */
    public $spReturnUrl = '';

    /**
     * The name of the application.
     * @var string
     */
    public $spIssuer = 'php-saml';

    /**
     * Specifies what format to return the authentication token, i.e, the email address.
     * @var string
     */
    public $requestedNameIdFormat = self::NAMEID_EMAIL_ADDRESS;
}
