<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->get('getall', ['uses' => 'AuthController@getAll']);

    $router->post('register', ['uses' => 'UserController@register']);
    $router->post('login', ['uses' => 'AuthController@login']);
    $router->post('/password/email', 'AuthController@postEmail');
    $router->post('/password/reset/{token}', ['uses' => 'AuthController@postReset', 'as' => 'password.reset']);
});


$router->group(['prefix' => 'api/v1', 'middleware' => 'client'], function () use ($router) {
    $router->post('user/update', ['uses' => 'AuthController@updateUserInfo']);
    // Project Route
    $router->get('mabna', ['use' => 'CrawController@mabna']);
    $router->get('instruments/sync', 'CrawlContro0ller@instrumentsSync');
    $router->get('intradaytrades', 'CrawlController@intradaytrades');
    $router->get('instruments', 'CrawlController@instruments');
    $router->get('exchange/trades', 'CrawlController@trades');
    $router->get('market/category/sync', ['uses' => 'CrawlController@syncCategory']);

    //Feed
    $router->get('feed/like/{feed}', ['as' => 'feed.like', 'uses' => 'LikeController@likeFeed']);
    $router->get('comment/like/{comment}', ['as' => 'comment.like', 'uses' => 'LikeController@likeComment']);
    //******
    $router->get('feed', ['uses' => 'FeedController@getAllRelatedFeedForCurentUser']);
    $router->post('feed', ['uses' => 'FeedController@addFeed']);
    $router->post('feed/comment', ['uses' => 'FeedController@addComment']);
    $router->get('mention', ['uses' => 'FeedController@mention']);


    //People
    $router->post('people/follow', ['uses' => 'PeopleController@followPeople']);
    $router->post('people/copy', ['uses' => 'PeopleController@copyPeople']);
    $router->get('discover/people', ['uses' => 'PeopleController@discoverPeople']);
    $router->get('people/search', ['uses' => 'PeopleController@search']);


    //Market\
    $router->post('market', ['uses' => 'MarketController@getMarket']);
    $router->post('market/latest', ['uses' => 'MarketController@getLatestMarket']);
    //market alert
    $router->get('market/alert', ['uses' => 'MarketController@getMarketAlert']);
    $router->post('market/alert', ['uses' => 'MarketController@setMarketAlert']);
    $router->put('market/alert', ['uses' => 'MarketController@updateMarketAlert']);
    $router->delete('market/alert', ['uses' => 'MarketController@removeMarketAlert']);

    //market watchlist
    $router->post('create/watchlist', ['uses' => 'WatchlistController@createNewWatchlist']);
    $router->post('watchlist/add', ['uses' => 'WatchlistController@addItemToWatchlis']);
    $router->get('get/watchlist', ['uses' => 'WatchlistController@getWatchlist']);
    $router->delete('watchlist/remove/item', ['uses' => 'WatchlistController@removeItem']);
    //***************
//    $router->post('market/watchlist/add', ['uses' => 'MarketController@addWatchlist']);
//    $router->get('market/watchlist', ['uses' => 'MarketController@getWatchlist']);
//    $router->get('user/watchlist', ['uses' => 'MarketController@getWatchlist']);
//    $router->delete('market/watchlist/remove', ['uses' => 'MarketController@removeWatchlist']);
    $router->get('market/trades', ['uses' => 'MarketController@trades']);
    $router->get('market/discover', ['uses' => 'MarketController@discoverMarket']);


    //User
    $router->post('user/wallet', ['uses' => 'WalletController@getUserWallet']);
    $router->post('user/checkout', ['uses' => 'WalletController@checkoutRequest']);
    $router->post('user/inventory', ['uses' => 'WalletController@addInventory']);
    $router->get('user/list', ['uses' => 'UserController@userList']);
    // user setting
    $router->get('user/setting', ['uses' => 'UserController@getUserSetting']);
    $router->put('user/setting', ['uses' => 'UserController@updateUserSetting']);

    $router->get('user/profile', ['uses' => 'UserController@getUserProfile']);
    $router->put('user/update/info', ['uses' => 'UserController@updateUserInfo']);

    $router->get('user/feed', ['uses' => 'FeedController@getUserFeed']);
//    $router->post('user/watchlist', ['uses' => 'WatchlistController@addWatchlist']);
//    $router->delete('user/watchlist/remove', ['uses' => 'UserController@removeWatchlist']);
    $router->put('user/avatar', ['uses' => 'UserController@updateAvatar']);
    $router->get('user/avatar/photo', ['uses' => 'UserController@getUserAvatar']);
    $router->delete('user/avatar', ['uses' => 'UserController@deleteAvatar']);
//** portfolio
    $router->get('user/portfolio/history', ['uses' => 'PortfolioController@getPortfolioHistory']);
    $router->get('user/portfolio/orders', ['uses' => 'PortfolioController@getPortfolioOrders']);
    $router->get('app/portfolio-page', ['uses' => 'PortfolioController@portfolioPage']);

    $router->post('watchlist/sort', ['uses' => 'UserController@setSortWatchlist']);
    $router->post('watchlist/default', ['uses' => 'UserController@setDefaultWatchlist']);
    $router->delete('watchlist', ['uses' => 'UserController@deleteWatchList']);
    $router->put('watchlist', ['uses' => 'UserController@updateWatchList']);

    //Efficiency
    $router->get('efficiency/market/user', ['uses' => 'EfficiencyController@marketEfficiency']);
    $router->get('efficiency/category/user', ['uses' => 'EfficiencyController@categoryEfficiency']);
    $router->post('efficiency/users/sync', ['uses' => 'EfficiencyController@usersEfficiency']);
    $router->get('efficiency/user', ['uses' => 'EfficiencyController@userEfficiency']);
    $router->get('user/trades', ['uses' => 'EfficiencyController@userTrades']);
    $router->get('user/risk', ['uses' => 'EfficiencyController@userRisk']);
    $router->get('user/risk/detail', ['uses' => 'EfficiencyController@userRiskDetail']);


    //Order
    $router->post('order/buy', ['uses' => 'OrderController@buyOrder']);
    $router->post('order/sell', ['uses' => 'OrderController@sellOrder']);
    $router->delete('order', ['uses' => 'OrderController@cancelOrder']);
    $router->post('order/set/buy', ['uses' => 'OrderController@setBuyOrder']);
    $router->post('order/set/sell', ['uses' => 'OrderController@setSellOrder']);
    $router->put('order', ['uses' => 'OrderController@updateBuyOrder']);

    //parent
    $router->get('parent', ['uses' => 'ParentController@getCategoryOfParent']);
    $router->get('parent/markets', ['uses' => 'ParentContrller@getMarketsOfParent']);
    $router->get('parents', ['uses' => 'ParentsController@getParents']);


    //category
    $router->get('category', ['uses' => 'CategoryController@getMarketsOfCategory']);
    $router->get('categories', ['uses' => 'categoryController@getCategories']);
    $router->get('categories', ['uses' => 'CategoryController@getCategories']);
});

$router->group(['prefix' => 'admin/api/v1'], function () use ($router) {
    //User

    $router->post('auth', ['uses' => 'Admin/UserController@authentication']);
    $router->get('users/list', ['uses' => 'Admin/UserController@userList']);
    $router->get('user', ['uses' => 'Admin/UserController@getUser']);
    $router->put('user', ['uses' => 'Admin/UserController@updateUser']);
    $router->delete('user', ['uses' => 'Admin/UserController@removeUser']);

    //Market
    $router->get('markets/list', ['uses' => 'Admin/MarketController@getLatestMarket']);
    $router->get('market', ['uses' => 'Admin/MarketController@getMarket']);
    $router->post('market', ['uses' => 'Admin/MarketController@addMarket']);
    $router->put('market', ['uses' => 'Admin/MarketController@updateMarket']);
    $router->delete('market', ['uses' => 'Admin/MarketController@removeMarket']);

    //Parent
    $router->get('parents/list', ['uses' => 'Admin/ParentController@getLatestParent']);
    $router->get('parent', ['uses' => 'Admin/ParentController@getParent']);
    $router->post('parent', ['uses' => 'Admin/ParentController@addParent']);
    $router->put('parent', ['uses' => 'Admin/ParentController@updateParent']);
    $router->delete('parent', ['uses' => 'Admin/ParentController@removeParent']);

    //Category
    $router->get('categories/list', ['uses' => 'Admin/Controller@getCategories']);
    $router->get('category', ['uses' => 'Admin/CategoryController@getCategory']);
    $router->post('category', ['uses' => 'Admin/CategoryController@addCategory']);
    $router->put('category', ['uses' => 'Admin/CategoryController@updateCategory']);
    $router->delete('category', ['uses' => 'Admin/CategoryController@removeCategory']);
});
