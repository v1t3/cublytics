<?php
declare(strict_types=1);

namespace App;

/**
 * Class AppRegistry
 *
 * @package App
 */
class AppRegistry
{
    /**
     *
     */
    public const APP_HOST = 'https://cublytics.ru';

    /**
     *
     */
    public const HTTPS_COUB = 'https://coub.com/';

    /**
     *
     */
    public const API_COUB_SINGLE_LINK = 'http://coub.com/api/v2/coubs/';

    /**
     *
     */
    public const API_COUB_TIMELINE_LINK = 'http://coub.com/api/v2/timeline/channel/';

    /**
     *
     */
    public const TIMELINE_PER_PAGE = 20;

    /**
     *
     */
    public const TIMELINE_ORDER_BY = 'newest_popular';

    /**
     *
     */
    public const REQUEST_AUTHORIZE_APP = 'http://coub.com/oauth/authorize';

    /**
     *
     */
    public const REQUEST_REVOKE_APP = 'http://coub.com/oauth/revoke';

    /**
     *
     */
    public const REDIRECT_CALLBACK = '/auth/callback';

    /**
     *
     */
    public const REQUEST_ACCESS_TOKEN = 'http://coub.com/oauth/token';

    /**
     *
     */
    public const REQUEST_USER_INFO = 'https://coub.com/api/v2/users/me';

}