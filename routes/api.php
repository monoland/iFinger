<?php

Route::get('/ceremony/events', 'Apps\CeremonyController@events');
Route::get('/ceremony/{date}/recaps', 'Apps\CeremonyController@recaps');
Route::post('/ceremony/{date}/participants', 'Apps\CeremonyController@participants');