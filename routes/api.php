<?php

Route::get('/ceremony/events', 'Apps\CeremonyController@events');
Route::get('/ceremony/{date}/recaps', 'Apps\CeremonyController@recaps');
Route::get('/ceremony/{date}/agencies', 'Apps\CeremonyController@agenciesReport');
Route::post('/ceremony/{date}/walkouts', 'Apps\CeremonyController@walkoutsReport');
Route::post('/ceremony/{date}/participants', 'Apps\CeremonyController@participants');