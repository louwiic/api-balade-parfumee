<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    '_preview_error' => [['code', '_format'], ['_controller' => 'error_controller::preview', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], [], []],
    '2fa_login' => [[], ['_controller' => 'scheb_two_factor.form_controller::form'], [], [['text', '/2fa']], [], [], []],
    '2fa_login_check' => [[], [], [], [['text', '/2fa_check']], [], [], []],
    '_wdt' => [['token'], ['_controller' => 'web_profiler.controller.profiler::toolbarAction'], [], [['variable', '/', '[^/]++', 'token', true], ['text', '/_wdt']], [], [], []],
    '_profiler_home' => [[], ['_controller' => 'web_profiler.controller.profiler::homeAction'], [], [['text', '/_profiler/']], [], [], []],
    '_profiler_search' => [[], ['_controller' => 'web_profiler.controller.profiler::searchAction'], [], [['text', '/_profiler/search']], [], [], []],
    '_profiler_search_bar' => [[], ['_controller' => 'web_profiler.controller.profiler::searchBarAction'], [], [['text', '/_profiler/search_bar']], [], [], []],
    '_profiler_phpinfo' => [[], ['_controller' => 'web_profiler.controller.profiler::phpinfoAction'], [], [['text', '/_profiler/phpinfo']], [], [], []],
    '_profiler_xdebug' => [[], ['_controller' => 'web_profiler.controller.profiler::xdebugAction'], [], [['text', '/_profiler/xdebug']], [], [], []],
    '_profiler_search_results' => [['token'], ['_controller' => 'web_profiler.controller.profiler::searchResultsAction'], [], [['text', '/search/results'], ['variable', '/', '[^/]++', 'token', true], ['text', '/_profiler']], [], [], []],
    '_profiler_open_file' => [[], ['_controller' => 'web_profiler.controller.profiler::openAction'], [], [['text', '/_profiler/open']], [], [], []],
    '_profiler' => [['token'], ['_controller' => 'web_profiler.controller.profiler::panelAction'], [], [['variable', '/', '[^/]++', 'token', true], ['text', '/_profiler']], [], [], []],
    '_profiler_router' => [['token'], ['_controller' => 'web_profiler.controller.router::panelAction'], [], [['text', '/router'], ['variable', '/', '[^/]++', 'token', true], ['text', '/_profiler']], [], [], []],
    '_profiler_exception' => [['token'], ['_controller' => 'web_profiler.controller.exception_panel::body'], [], [['text', '/exception'], ['variable', '/', '[^/]++', 'token', true], ['text', '/_profiler']], [], [], []],
    '_profiler_exception_css' => [['token'], ['_controller' => 'web_profiler.controller.exception_panel::stylesheet'], [], [['text', '/exception.css'], ['variable', '/', '[^/]++', 'token', true], ['text', '/_profiler']], [], [], []],
    'app_getAllTag' => [[], ['_controller' => 'App\\Controller\\AdminController::getAllTag'], [], [['text', '/api/admin/getAllTag']], [], [], []],
    'app_addContentExclusive' => [[], ['_controller' => 'App\\Controller\\AdminController::addContentExclusive'], [], [['text', '/api/admin/addContentExclusive']], [], [], []],
    'app_update_ContentExclusive' => [['id'], ['_controller' => 'App\\Controller\\AdminController::updateContentExclusive'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/admin/edit/contentExclusive']], [], [], []],
    'app_updateContentExclusive' => [['contentExclusive'], ['_controller' => 'App\\Controller\\AdminController::removeContentExclusive'], [], [['variable', '/', '[^/]++', 'contentExclusive', true], ['text', '/api/admin/contentExclusive']], [], [], []],
    'getAllContentExclusive' => [[], ['_controller' => 'App\\Controller\\AdminController::getAllContentExclusive'], [], [['text', '/api/admin/getAllContentExclusive']], [], [], []],
    'deleteContentExclusive' => [['id'], ['_controller' => 'App\\Controller\\AdminController::deleteContentExclusive'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/admin/deleteContentExclusive']], [], [], []],
    'app_getAllNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::getAllNotification'], [], [['text', '/api/admin/getAllNotification']], [], [], []],
    'app_getUsersAllNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::getAllUserNotification'], [], [['text', '/api/users/getAllNotification']], [], [], []],
    'app_deleteNotification' => [['notification'], ['_controller' => 'App\\Controller\\AdminController::deleteNotification'], [], [['variable', '/', '[^/]++', 'notification', true], ['text', '/api/admin/deleteNotification']], [], [], []],
    'app_addNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::addNotification'], [], [['text', '/api/admin/addNotification']], [], [], []],
    'app_readNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::readNotification'], [], [['text', '/api/read/notification']], [], [], []],
    'app_isReadNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::getNotificationIsRead'], [], [['text', '/api/isRead/notification']], [], [], []],
    'app_updateNotification' => [[], ['_controller' => 'App\\Controller\\AdminController::updateNotification'], [], [['text', '/api/admin/update/notification']], [], [], []],
    'app_admin_getAll_users' => [['page'], ['_controller' => 'App\\Controller\\AdminController::getAllUser'], [], [['variable', '/', '[^/]++', 'page', true], ['text', '/api/admin/getUsers']], [], [], []],
    'get_all_total_subscriptions' => [[], ['_controller' => 'App\\Controller\\AdminController::getAllSubscriptions'], [], [['text', '/api/admin/getTotalSubscriptions']], [], [], []],
    'app_create_checkList' => [['m', 'Y'], ['_controller' => 'App\\Controller\\CheckListController::createCheckList'], [], [['variable', '/', '[^/]++', 'Y', true], ['variable', '/', '[^/]++', 'm', true], ['text', '/api/checkList']], [], [], []],
    'app_put_checkList' => [['checkList', 'fragrance'], ['_controller' => 'App\\Controller\\CheckListController::putCheckList'], [], [['variable', '/', '[^/]++', 'fragrance', true], ['variable', '/', '[^/]++', 'checkList', true], ['text', '/api/checkList']], [], [], []],
    'app_delete_checkList' => [['checkList'], ['_controller' => 'App\\Controller\\CheckListController::deleteCheckList'], [], [['variable', '/', '[^/]++', 'checkList', true], ['text', '/api/checkList/delete']], [], [], []],
    'app_get_checkList' => [[], ['_controller' => 'App\\Controller\\CheckListController::getCheckList'], [], [['text', '/api/checkList']], [], [], []],
    'app_getAllFragrance' => [[], ['_controller' => 'App\\Controller\\FragranceController::getAllFragrance'], [], [['text', '/api/fragrance']], [], [], []],
    'app_create_wishlist' => [[], ['_controller' => 'App\\Controller\\FragranceController::createWishlist'], [], [['text', '/api/wishlist']], [], [], []],
    'app_put_wishlist' => [['wishlist', 'fragrance'], ['fragrance' => null, '_controller' => 'App\\Controller\\FragranceController::PUTWishlist'], [], [['variable', '/', '[^/]++', 'fragrance', true], ['variable', '/', '[^/]++', 'wishlist', true], ['text', '/api/wishlist']], [], [], []],
    'app_DELETE_wishlist' => [['wishlist'], ['_controller' => 'App\\Controller\\FragranceController::DELETEWishlist'], [], [['variable', '/', '[^/]++', 'wishlist', true], ['text', '/api/wishlist']], [], [], []],
    'app_getWishlist' => [[], ['_controller' => 'App\\Controller\\FragranceController::getWishlist'], [], [['text', '/api/wishlist']], [], [], []],
    'app_create_Layerings' => [[], ['_controller' => 'App\\Controller\\LayeringsController::createLayerings'], [], [['text', '/api/layerings']], [], [], []],
    'app_update_Layerings' => [['layering'], ['_controller' => 'App\\Controller\\LayeringsController::updateLayerings'], [], [['variable', '/', '[^/]++', 'layering', true], ['text', '/api/layerings']], [], [], []],
    'app_DELETE_Layerings' => [['layering'], ['_controller' => 'App\\Controller\\LayeringsController::daleteLayerings'], [], [['variable', '/', '[^/]++', 'layering', true], ['text', '/api/layerings']], [], [], []],
    'app_get_Layerings' => [[], ['_controller' => 'App\\Controller\\LayeringsController::getLayerings'], [], [['text', '/api/layerings']], [], [], []],
    'subscriber_members_in_lists' => [[], ['_controller' => 'App\\Controller\\MailChimpController::subscribedMembers'], [], [['text', '/subscriber_members_in_lists']], [], [], []],
    'app_create_perfumeTrialSheet' => [['m', 'Y'], ['_controller' => 'App\\Controller\\PerfumeTrialSheetController::createPerfumeTrialSheet'], [], [['variable', '/', '[^/]++', 'Y', true], ['variable', '/', '[^/]++', 'm', true], ['text', '/api/perfumeTrialSheet']], [], [], []],
    'app_put_perfumeTrialSheet' => [['perfumeTrialSheet', 'fragrance'], ['_controller' => 'App\\Controller\\PerfumeTrialSheetController::putPerfumeTrialSheet'], [], [['variable', '/', '[^/]++', 'fragrance', true], ['variable', '/', '[^/]++', 'perfumeTrialSheet', true], ['text', '/api/perfumeTrialSheet']], [], [], []],
    'app_delete_trialsheet_checkList' => [['perfumeTrialSheet'], ['_controller' => 'App\\Controller\\PerfumeTrialSheetController::deletePerfumeTrialSheet'], [], [['variable', '/', '[^/]++', 'perfumeTrialSheet', true], ['text', '/api/perfumeTrialSheet']], [], [], []],
    'app_get_perfumeTrialSheet' => [[], ['_controller' => 'App\\Controller\\PerfumeTrialSheetController::getPerfumeTrialSheet'], [], [['text', '/api/perfumeTrialSheet']], [], [], []],
    'app_getProfil' => [[], ['_controller' => 'App\\Controller\\ProfilController::getProfil'], [], [['text', '/api/profil']], [], [], []],
    'app_updateProfilprofilPUT' => [[], ['_controller' => 'App\\Controller\\ProfilController::updateProfil'], [], [['text', '/api/profilPUT']], [], [], []],
    'app_get_feltOnMyCollection' => [[], ['_controller' => 'App\\Controller\\ProfilController::getFeltOnMyCollection'], [], [['text', '/api/profil/feltOnMyCollection']], [], [], []],
    'app_add_tag' => [[], ['_controller' => 'App\\Controller\\ProfilController::addMyFavoriteTypesOfPerfumes'], [], [['text', '/api/profil/tag']], [], [], []],
    'app_get_tag' => [[], ['_controller' => 'App\\Controller\\ProfilController::getMyFavoriteTypesOfPerfumes'], [], [['text', '/api/profil/tag']], [], [], []],
    'app_edit_tag' => [['myFavoriteTypesOfPerfumes'], ['_controller' => 'App\\Controller\\ProfilController::editMyFavoriteTypesOfPerfumes'], [], [['variable', '/', '[^/]++', 'myFavoriteTypesOfPerfumes', true], ['text', '/api/profil/tag']], [], [], []],
    'app_edit_dele' => [['myFavoriteTypesOfPerfumes'], ['_controller' => 'App\\Controller\\ProfilController::removeMyFavoriteTypesOfPerfumes'], [], [['variable', '/', '[^/]++', 'myFavoriteTypesOfPerfumes', true], ['text', '/api/profil/tag']], [], [], []],
    'aze' => [[], ['_controller' => 'App\\Controller\\ProfilController::aze'], [], [['text', '/api/']], [], [], []],
    'app_content_exclusive_data' => [[], ['_controller' => 'App\\Controller\\ProfilController::getAllContentExclusive'], [], [['text', '/api/contentExclusive']], [], [], []],
    'app_create_ReviewPerfumeNote' => [['m', 'Y'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::createReviewPerfumeNote'], [], [['variable', '/', '[^/]++', 'Y', true], ['variable', '/', '[^/]++', 'm', true], ['text', '/api/reviewPerfumeNote']], [], [], []],
    'app_reviewperfumenote_addfragrancesreviewperfumenote' => [['reviewPerfumeNote', 'fragrances'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::addFragrancesReviewPerfumeNote'], [], [['variable', '/', '[^/]++', 'fragrances', true], ['variable', '/', '[^/]++', 'reviewPerfumeNote', true], ['text', '/api/reviewPerfumeNoteAddFragrance']], [], [], []],
    'app_reviewperfumenote_removefragrancereviewperfumenote' => [['reviewPerfumeNote', 'fragrances'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::removeFragranceReviewPerfumeNote'], [], [['variable', '/', '[^/]++', 'fragrances', true], ['variable', '/', '[^/]++', 'reviewPerfumeNote', true], ['text', '/api/reviewPerfumeNoteRemoveFragrance']], [], [], []],
    'app_put_ReviewPerfumeNote' => [['ReviewPerfumeNote'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::putReviewPerfumeNote'], [], [['variable', '/', '[^/]++', 'ReviewPerfumeNote', true], ['text', '/api/reviewPerfumeNote']], [], [], []],
    'app_DELETE_ReviewPerfumeNote' => [['ReviewPerfumeNote'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::deleteReviewPerfumeNote'], [], [['variable', '/', '[^/]++', 'ReviewPerfumeNote', true], ['text', '/api/reviewPerfumeNote']], [], [], []],
    'app_get_ReviewPerfumeNote' => [[], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::getReviewPerfumeNote'], [], [['text', '/api/reviewPerfumeNote']], [], [], []],
    'app_add_fragrance_in_ReviewPerfumeNote' => [['reviewPerfumeNote', 'fragrance'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::addPerfumeInNote'], [], [['variable', '/', '[^/]++', 'fragrance', true], ['text', '/fragrance'], ['variable', '/', '[^/]++', 'reviewPerfumeNote', true], ['text', '/api/reviewPerfumeNote']], [], [], []],
    'app_delete_fragrance_in_ReviewPerfumeNote' => [['reviewPerfumeNote', 'fragrance'], ['_controller' => 'App\\Controller\\ReviewPerfumeNoteController::deletePerfumeInNote'], [], [['variable', '/', '[^/]++', 'fragrance', true], ['text', '/fragrance'], ['variable', '/', '[^/]++', 'reviewPerfumeNote', true], ['text', '/api/reviewPerfumeNote']], [], [], []],
    'get lists invoice' => [[], ['_controller' => 'App\\Controller\\StripeController::getInvoicesAction'], [], [['text', '/api/getInvoice']], [], [], []],
    'getCurrentSubscription' => [[], ['_controller' => 'App\\Controller\\StripeController::getCurrentSubscription'], [], [['text', '/api/getCurrentSubscription']], [], [], []],
    'cancelSubscription' => [[], ['_controller' => 'App\\Controller\\StripeController::cancelSubscription'], [], [['text', '/api/cancelSubscription']], [], [], []],
    'infoCreditCard' => [[], ['_controller' => 'App\\Controller\\StripeController::getLastFourDigits'], [], [['text', '/api/infoCreditCard']], [], [], []],
    'addMailChimpMember' => [[], ['_controller' => 'App\\Controller\\StripeController::addMailChimpMember'], [], [['text', '/api/changeTagMailChimp']], [], [], []],
    'changeSubscription' => [[], ['_controller' => 'App\\Controller\\StripeController::changeSubscription'], [], [['text', '/api/changeSubscription']], [], [], []],
    'check_subscription' => [[], ['_controller' => 'App\\Controller\\StripeController::checkSubscription'], [], [['text', '/api/check-subscription']], [], [], []],
    'updateCreditCard' => [[], ['_controller' => 'App\\Controller\\StripeController::updateCard'], [], [['text', '/api/updateCreditCard']], [], [], []],
    'get lists ssasa' => [[], ['_controller' => 'App\\Controller\\StripeController::eee'], [], [['text', '/eeee']], [], [], []],
    'stripe_webhook' => [[], ['_controller' => 'App\\Controller\\StripeController::handleWebhook'], [], [['text', '/get_list_mailChimp']], [], [], []],
    'get lists mailChimp' => [[], ['_controller' => 'App\\Controller\\StripeController::mailChimp'], [], [['text', '/get_list_mailChimp']], [], [], []],
    'confirm_email' => [[], ['_controller' => 'App\\Controller\\UserController::generateEmailCode'], [], [['text', '/api/auth/confirm_email']], [], [], []],
    'generate_code' => [[], ['_controller' => 'App\\Controller\\UserController::generateCode'], [], [['text', '/api/auth/generate_code']], [], [], []],
    'verify_code' => [[], ['_controller' => 'App\\Controller\\UserController::verifyCode'], [], [['text', '/api/auth/verify_code']], [], [], []],
    'update_user' => [[], ['_controller' => 'App\\Controller\\UserController::_updateUser'], [], [['text', '/api/update-user']], [], [], []],
    'app_register' => [[], ['_controller' => 'App\\Controller\\UserController::register'], [], [['text', '/register']], [], [], []],
    'app_check_phone_exist' => [[], ['_controller' => 'App\\Controller\\UserController::checkPhoneExist'], [], [['text', '/checkPhone']], [], [], []],
    'app_iSConnected' => [[], ['_controller' => 'App\\Controller\\UserController::iSConnected'], [], [['text', '/api/iSConnected']], [], [], []],
    'app_verify_email' => [[], ['_controller' => 'App\\Controller\\UserController::verifyUserEmail'], [], [['text', '/verify/email']], [], [], []],
    'api_login_check' => [[], [], [], [['text', '/api/auth/login']], [], [], []],
];
