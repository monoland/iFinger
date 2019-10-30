<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Apel</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="/dashboard/styles/monoland.css">
    <link rel="stylesheet" href="/dashboard/styles/print.css">
</head>

<style>
    [v-cloak] {
        display: none;
    }
</style>

<body>
    <div id="monoland"></div>

    <noscript>
        <div class="application">
            <div class="application--wrap">
                <div class="v-content">
                    <div class="v-content__wrap">
                        <div class="container fluid fill-height">
                            <div class="layout align-center justify-center">
                                <div class="subheading font-weight-thin">
                                    <span class="d-block">Mohon maaf, aplikasi tidak dapat bekerja tanpa JavaScript aktif.</span>
                                    <span class="d-block" style="text-align: center;">Silahkan aktifkan untuk melanjutkan.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </noscript>

    <script src="/dashboard/scripts/manifest.js"></script>
    <script src="/dashboard/scripts/vendor.js"></script>
    <script src="/dashboard/scripts/monoland.js"></script>
</body>
</html>