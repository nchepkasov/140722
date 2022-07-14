<?php

$routing = new \Klein\Klein ();

$routing->respond ('GET',  '[/|/domains]', function ($request) {
    return (new \App\Controllers\Main ())->getIndexPage ();
});

$routing->respond ('POST', '[/|/domains]', function ($request) {
    return (new \App\Controllers\Main ())->getDomainsAjax (
        $request->Domains,
        $request->ExcludeActive 
    );
});

$routing->dispatch ();