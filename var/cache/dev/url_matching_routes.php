<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/api/admin/getAllTag' => [[['_route' => 'app_getAllTag', '_controller' => 'App\\Controller\\AdminController::getAllTag'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin/addContentExclusive' => [[['_route' => 'app_addContentExclusive', '_controller' => 'App\\Controller\\AdminController::addContentExclusive'], null, ['POST' => 0], null, false, false, null]],
        '/api/admin/getAllContentExclusive' => [[['_route' => 'getAllContentExclusive', '_controller' => 'App\\Controller\\AdminController::getAllContentExclusive'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin/getAllNotification' => [[['_route' => 'app_getAllNotification', '_controller' => 'App\\Controller\\AdminController::getAllNotification'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin/addNotification' => [[['_route' => 'app_addNotification', '_controller' => 'App\\Controller\\AdminController::addNotification'], null, ['POST' => 0], null, false, false, null]],
        '/api/admin/getTotalSubscriptions' => [[['_route' => 'get_all_total_subscriptions', '_controller' => 'App\\Controller\\AdminController::getAllSubscriptions'], null, ['GET' => 0], null, false, false, null]],
        '/api/fragrance' => [[['_route' => 'app_getAllFragrance', '_controller' => 'App\\Controller\\FragranceController::getAllFragrance'], null, null, null, false, false, null]],
        '/api/wishlist' => [
            [['_route' => 'app_create_wishlist', '_controller' => 'App\\Controller\\FragranceController::createWishlist'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'app_getWishlist', '_controller' => 'App\\Controller\\FragranceController::getWishlist'], null, null, null, false, false, null],
        ],
        '/api/layerings' => [
            [['_route' => 'app_create_Layerings', '_controller' => 'App\\Controller\\LayeringsController::createLayerings'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'app_get_Layerings', '_controller' => 'App\\Controller\\LayeringsController::getLayerings'], null, ['GET' => 0], null, false, false, null],
        ],
        '/subscriber_members_in_lists' => [[['_route' => 'subscriber_members_in_lists', '_controller' => 'App\\Controller\\MailChimpController::subscribedMembers'], null, null, null, false, false, null]],
        '/api/perfumeTrialSheet' => [[['_route' => 'app_get_perfumeTrialSheet', '_controller' => 'App\\Controller\\PerfumeTrialSheetController::getPerfumeTrialSheet'], null, null, null, false, false, null]],
        '/api/profil' => [[['_route' => 'app_getProfil', '_controller' => 'App\\Controller\\ProfilController::getProfil'], null, null, null, false, false, null]],
        '/api/profilPUT' => [[['_route' => 'app_updateProfilprofilPUT', '_controller' => 'App\\Controller\\ProfilController::updateProfil'], null, ['PUT' => 0], null, false, false, null]],
        '/api/profil/feltOnMyCollection' => [[['_route' => 'app_get_feltOnMyCollection', '_controller' => 'App\\Controller\\ProfilController::getFeltOnMyCollection'], null, ['PUT' => 0], null, false, false, null]],
        '/api/profil/tag' => [
            [['_route' => 'app_add_tag', '_controller' => 'App\\Controller\\ProfilController::addMyFavoriteTypesOfPerfumes'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'app_get_tag', '_controller' => 'App\\Controller\\ProfilController::getMyFavoriteTypesOfPerfumes'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api' => [[['_route' => 'aze', '_controller' => 'App\\Controller\\ProfilController::aze'], null, ['GET' => 0], null, true, false, null]],
        '/api/contentExclusive' => [[['_route' => 'app_content_exclusive_data', '_controller' => 'App\\Controller\\ProfilController::getAllContentExclusive'], null, ['GET' => 0], null, false, false, null]],
        '/api/reviewPerfumeNote' => [[['_route' => 'app_get_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::getReviewPerfumeNote'], null, null, null, false, false, null]],
        '/api/getInvoice' => [[['_route' => 'get lists invoice', '_controller' => 'App\\Controller\\StripeController::getInvoicesAction'], null, null, null, false, false, null]],
        '/api/getCurrentSubscription' => [[['_route' => 'getCurrentSubscription', '_controller' => 'App\\Controller\\StripeController::getCurrentSubscription'], null, ['GET' => 0], null, false, false, null]],
        '/api/cancelSubscription' => [[['_route' => 'cancelSubscription', '_controller' => 'App\\Controller\\StripeController::cancelSubscription'], null, ['PUT' => 0], null, false, false, null]],
        '/api/infoCreditCard' => [[['_route' => 'infoCreditCard', '_controller' => 'App\\Controller\\StripeController::getLastFourDigits'], null, ['GET' => 0], null, false, false, null]],
        '/api/changeSubscription' => [[['_route' => 'changeSubscription', '_controller' => 'App\\Controller\\StripeController::changeSubscription'], null, ['PUT' => 0], null, false, false, null]],
        '/api/check-subscription' => [[['_route' => 'check_subscription', '_controller' => 'App\\Controller\\StripeController::checkSubscription'], null, ['GET' => 0], null, false, false, null]],
        '/api/updateCreditCard' => [[['_route' => 'updateCreditCard', '_controller' => 'App\\Controller\\StripeController::updateCard'], null, ['PUT' => 0], null, false, false, null]],
        '/eeee' => [[['_route' => 'get lists ssasa', '_controller' => 'App\\Controller\\StripeController::eee'], null, null, null, false, false, null]],
        '/get_list_mailChimp' => [
            [['_route' => 'stripe_webhook', '_controller' => 'App\\Controller\\StripeController::handleWebhook'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'get lists mailChimp', '_controller' => 'App\\Controller\\StripeController::mailChimp'], null, null, null, false, false, null],
        ],
        '/api/auth/generate_code' => [[['_route' => 'generate_code', '_controller' => 'App\\Controller\\UserController::generateCode'], null, ['POST' => 0], null, false, false, null]],
        '/api/auth/verify_code' => [[['_route' => 'verify_code', '_controller' => 'App\\Controller\\UserController::verifyCode'], null, ['POST' => 0], null, false, false, null]],
        '/register' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\UserController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/iSConnected' => [[['_route' => 'app_iSConnected', '_controller' => 'App\\Controller\\UserController::iSConnected'], null, null, null, false, false, null]],
        '/verify/email' => [[['_route' => 'app_verify_email', '_controller' => 'App\\Controller\\UserController::verifyUserEmail'], null, null, null, false, false, null]],
        '/api/auth/login' => [[['_route' => 'api_login_check'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/api/(?'
                    .'|admin/(?'
                        .'|contentExclusive/([^/]++)(*:211)'
                        .'|deleteNotification/([^/]++)(*:246)'
                        .'|notification/([^/]++)(*:275)'
                        .'|getUsers/([^/]++)(*:300)'
                    .')'
                    .'|checkList(?'
                        .'|(?:/([^/]++)(?:/([^/]++))?)?(*:349)'
                        .'|/([^/]++)/([^/]++)(*:375)'
                        .'|(*:383)'
                    .')'
                    .'|wishlist/([^/]++)(?'
                        .'|(?:/([^/]++))?(*:426)'
                        .'|(*:434)'
                    .')'
                    .'|layerings/([^/]++)(?'
                        .'|(*:464)'
                    .')'
                    .'|p(?'
                        .'|erfumeTrialSheet/([^/]++)(?'
                            .'|/([^/]++)(?'
                                .'|(*:517)'
                                .'|(*:525)'
                            .')'
                            .'|(*:534)'
                        .')'
                        .'|rofil/tag/([^/]++)(?'
                            .'|(*:564)'
                        .')'
                    .')'
                    .'|reviewPerfumeNote(?'
                        .'|/([^/]++)(?'
                            .'|/(?'
                                .'|([^/]++)(*:618)'
                                .'|fragrance/([^/]++)(?'
                                    .'|(*:647)'
                                .')'
                            .')'
                            .'|(*:657)'
                        .')'
                        .'|AddFragrance/([^/]++)/([^/]++)(*:696)'
                        .'|RemoveFragrance/([^/]++)/([^/]++)(*:737)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        211 => [[['_route' => 'app_updateContentExclusive', '_controller' => 'App\\Controller\\AdminController::removeContentExclusive'], ['contentExclusive'], ['DELETE' => 0], null, false, true, null]],
        246 => [[['_route' => 'app_deleteNotification', '_controller' => 'App\\Controller\\AdminController::deleteNotification'], ['notification'], ['DELETE' => 0], null, false, true, null]],
        275 => [[['_route' => 'app_updateNotification', '_controller' => 'App\\Controller\\AdminController::updateNotification'], ['notification'], ['PUT' => 0], null, false, true, null]],
        300 => [[['_route' => 'app_admin_getAll_users', '_controller' => 'App\\Controller\\AdminController::getAllUser'], ['page'], ['GET' => 0], null, false, true, null]],
        349 => [[['_route' => 'app_create_checkList', 'm' => false, 'Y' => false, '_controller' => 'App\\Controller\\CheckListController::createCheckList'], ['m', 'Y'], ['POST' => 0], null, false, true, null]],
        375 => [[['_route' => 'app_put_checkList', '_controller' => 'App\\Controller\\CheckListController::putCheckList'], ['checkList', 'fragrance'], ['PUT' => 0], null, false, true, null]],
        383 => [[['_route' => 'app_get_checkList', '_controller' => 'App\\Controller\\CheckListController::getCheckList'], [], null, null, false, false, null]],
        426 => [[['_route' => 'app_put_wishlist', 'fragrance' => null, '_controller' => 'App\\Controller\\FragranceController::PUTWishlist'], ['wishlist', 'fragrance'], ['PUT' => 0], null, false, true, null]],
        434 => [[['_route' => 'app_DELETE_wishlist', '_controller' => 'App\\Controller\\FragranceController::DELETEWishlist'], ['wishlist'], ['DELETE' => 0], null, false, true, null]],
        464 => [
            [['_route' => 'app_update_Layerings', '_controller' => 'App\\Controller\\LayeringsController::updateLayerings'], ['layering'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_DELETE_Layerings', '_controller' => 'App\\Controller\\LayeringsController::daleteLayerings'], ['layering'], ['DELETE' => 0], null, false, true, null],
        ],
        517 => [[['_route' => 'app_create_perfumeTrialSheet', '_controller' => 'App\\Controller\\PerfumeTrialSheetController::createPerfumeTrialSheet'], ['m', 'Y'], ['POST' => 0], null, false, true, null]],
        525 => [[['_route' => 'app_put_perfumeTrialSheet', '_controller' => 'App\\Controller\\PerfumeTrialSheetController::putPerfumeTrialSheet'], ['perfumeTrialSheet', 'fragrance'], ['PUT' => 0], null, false, true, null]],
        534 => [[['_route' => 'app_DELETE_checkList', '_controller' => 'App\\Controller\\PerfumeTrialSheetController::deletePerfumeTrialSheet'], ['perfumeTrialSheet'], ['DELETE' => 0], null, false, true, null]],
        564 => [
            [['_route' => 'app_edit_tag', '_controller' => 'App\\Controller\\ProfilController::editMyFavoriteTypesOfPerfumes'], ['myFavoriteTypesOfPerfumes'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_edit_dele', '_controller' => 'App\\Controller\\ProfilController::removeMyFavoriteTypesOfPerfumes'], ['myFavoriteTypesOfPerfumes'], ['DELETE' => 0], null, false, true, null],
        ],
        618 => [[['_route' => 'app_create_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::createReviewPerfumeNote'], ['m', 'Y'], ['POST' => 0], null, false, true, null]],
        647 => [
            [['_route' => 'app_add_fragrance_in_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::addPerfumeInNote'], ['reviewPerfumeNote', 'fragrance'], ['POST' => 0], null, false, true, null],
            [['_route' => 'app_delete_fragrance_in_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::deletePerfumeInNote'], ['reviewPerfumeNote', 'fragrance'], ['DELETE' => 0], null, false, true, null],
        ],
        657 => [
            [['_route' => 'app_put_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::putReviewPerfumeNote'], ['ReviewPerfumeNote'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_DELETE_ReviewPerfumeNote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::deleteReviewPerfumeNote'], ['ReviewPerfumeNote'], ['DELETE' => 0], null, false, true, null],
        ],
        696 => [[['_route' => 'app_reviewperfumenote_addfragrancesreviewperfumenote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::addFragrancesReviewPerfumeNote'], ['reviewPerfumeNote', 'fragrances'], null, null, false, true, null]],
        737 => [
            [['_route' => 'app_reviewperfumenote_removefragrancereviewperfumenote', '_controller' => 'App\\Controller\\ReviewPerfumeNoteController::removeFragranceReviewPerfumeNote'], ['reviewPerfumeNote', 'fragrances'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
