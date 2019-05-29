<?php
Route::settings('loginlog', function () {
	Route::get('/', [
		'as' => 'loginlog::list',
		'uses' => 'SimpleSoft\XePlugin\Loginlog\Controllers\LoginLogController@index',
		'permission' => 'user.setting',
		'settings_menu' => 'user.loginlog'
	]);
	Route::get('/setting', [
		'as' => 'loginlog::settings',
		'uses' => 'SimpleSoft\XePlugin\Loginlog\Controllers\LoginLogController@setting',
		'permission' => 'user.setting',
		'settings_menu' => 'setting.loginlog'
	]);
	Route::get(
		'delete',
		['as' => 'loginlog::settings.loginlog.delete', 'uses' => 'SimpleSoft\XePlugin\Loginlog\Controllers\LoginLogController@deletePage']
	);

	Route::delete(
		'destory',
	 ['as' => 'settings.loginlog.destroy',
	  'uses' => 'SimpleSoft\XePlugin\Loginlog\Controllers\LoginLogController@destroy'
	 ]);

	Route::post(
		'saveAdminConfig',
		['as' => 'settings.loginlog.saveAdminConfig',
		 'uses' => 'SimpleSoft\XePlugin\Loginlog\Controllers\LoginLogController@saveAdminConfig'
		]);
});